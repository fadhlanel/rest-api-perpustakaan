<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadBuktiReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('public');
    }

    /** Helper: anggota + bearer token. */
    private function anggotaWithToken(): array
    {
        $anggota = Anggota::create(['nis' => '20247777', 'nama' => 'Siswa Uji']);
        $token = 'upload-bukti-token';
        $anggota->forceFill(['api_token_hash' => hash('sha256', $token)])->save();

        return [$anggota, ['Authorization' => 'Bearer '.$token]];
    }

    /** Base64 PNG valid 1x1 (magic bytes PNG) untuk simulasi upload HP. */
    private function base64Png(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }

    /**
     * REGRESI: Android kirim 'foto_bukti' sebagai string base64 JSON,
     * backend harus menerima, simpan file, dan buat record pengembalian.
     */
    public function test_upload_bukti_base64_diterima_dan_menyimpan_file(): void
    {
        [$anggota, $headers] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $peminjaman = Peminjaman::create([
            'anggota_id' => $anggota->id,
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
            'status' => Peminjaman::STATUS_DIPINJAM,
        ]);

        $response = $this->withHeaders($headers)->postJson(
            "/api/v1/peminjaman/{$peminjaman->id}/upload-bukti",
            ['foto_bukti' => $this->base64Png()],
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.dikonfirmasi', false);

        $pengembalian = $peminjaman->fresh()->pengembalian;
        $this->assertNotNull($pengembalian);
        $this->assertNotNull($pengembalian->foto_bukti);
        Storage::disk('public')->assertExists($pengembalian->foto_bukti);

        // Status peminjaman TIDAK berubah sampai admin konfirmasi.
        $this->assertSame(Peminjaman::STATUS_DIPINJAM, $peminjaman->fresh()->status);
        $this->assertTrue((bool) $peminjaman->fresh()->bukti_pending_konfirmasi);
    }

    /**
     * REGRESI: format base64 selain data:image harus ditolak 422.
     */
    public function test_upload_bukti_base64_invalid_ditolak(): void
    {
        [$anggota, $headers] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $peminjaman = Peminjaman::create([
            'anggota_id' => $anggota->id,
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
            'status' => Peminjaman::STATUS_DIPINJAM,
        ]);

        $this->withHeaders($headers)->postJson(
            "/api/v1/peminjaman/{$peminjaman->id}/upload-bukti",
            ['foto_bukti' => 'bukan-base64-image'],
        )->assertStatus(422);

        $this->assertNull($peminjaman->fresh()->pengembalian);
    }

    /**
     * REGRESI: mode booking tanggal_pinjam = tanggal_ambil,
     * tanggal_jatuh_tempo = tanggal_ambil + durasi, stok DIKUNCI (berkurang 1).
     */
    public function test_booking_tanggal_pinjam_sama_dengan_tanggal_ambil(): void
    {
        [$anggota, $headers] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $stokAwal = $buku->stok_tersedia;
        $tanggalAmbil = now()->addDays(2)->toDateString();

        $response = $this->withHeaders($headers)->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'mode' => 'booking',
            'tanggal_ambil' => $tanggalAmbil,
            'durasi_hari' => 3,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.tanggal_pinjam', $tanggalAmbil)
            ->assertJsonPath('data.tanggal_ambil', $tanggalAmbil)
            ->assertJsonPath('data.tanggal_jatuh_tempo', now()->parse($tanggalAmbil)->addDays(3)->toDateString())
            ->assertJsonPath('data.is_booking', true)
            ->assertJsonPath('data.status_reservasi', Peminjaman::STATUS_RESERVASI_BOOKING);

        // Stok langsung dialokasikan/dikurangi saat reservasi.
        $this->assertSame($stokAwal - 1, $buku->fresh()->stok_tersedia);
    }

    /**
     * REGRESI: Peminjaman dengan status booking TIDAK boleh dianggap hampir tenggat
     * (is_hampir_tenggat = false), meskipun tanggal jatuh tempo dekat,
     * karena buku belum diambil oleh peminjam.
     */
    public function test_booking_tidak_pernah_hampir_tenggat(): void
    {
        [$anggota] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();

        $booking = Peminjaman::create([
            'anggota_id' => $anggota->id,
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_ambil' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->toDateString(), // hari ini
            'status' => Peminjaman::STATUS_DIPINJAM,
            'status_reservasi' => Peminjaman::STATUS_RESERVASI_BOOKING,
        ]);

        $this->assertTrue($booking->is_booking);
        $this->assertFalse($booking->is_hampir_tenggat);
    }

    /**
     * Admin menyerahkan buku booking fisik ke siswa:
     * status_reservasi berubah menjadi dipinjam, stok TIDAK dipotong lagi
     * karena sudah dikunci sejak reservasi dibuat.
     */
    public function test_admin_bisa_menyerahkan_buku_booking(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$anggota, $headers] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $stokAwal = $buku->stok_tersedia;

        $bookingResponse = $this->withHeaders($headers)->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'mode' => 'booking',
            'tanggal_ambil' => now()->toDateString(),
            'durasi_hari' => 2,
        ]);
        $bookingResponse->assertCreated();
        $bookingId = $bookingResponse->json('data.id');
        $stokSetelahBooking = $buku->fresh()->stok_tersedia;
        $this->assertSame($stokAwal - 1, $stokSetelahBooking);

        $response = $this->actingAs($admin)->post(
            route('admin.peminjaman.serahkan-buku', $bookingId)
        );

        $response->assertRedirect();
        $booking = Peminjaman::findOrFail($bookingId);

        $this->assertSame(Peminjaman::STATUS_RESERVASI_DIPINJAM, $booking->status_reservasi);
        $this->assertFalse($booking->is_booking);
        $this->assertSame($stokSetelahBooking, $buku->fresh()->stok_tersedia);
    }

    /**
     * Booking kedua ditolak saat stok sudah dikunci booking pertama.
     */
    public function test_booking_melebihi_stok_ditolak(): void
    {
        [$anggotaA, $headersA] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $buku->update(['stok_tersedia' => 1]);

        $this->withHeaders($headersA)->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => $anggotaA->nama,
            'nis' => $anggotaA->nis,
            'buku_id' => $buku->id,
            'mode' => 'booking',
            'tanggal_ambil' => now()->toDateString(),
        ])->assertCreated();

        $anggotaB = Anggota::create(['nis' => '20247778', 'nama' => 'Siswa Uji Dua']);
        $tokenB = 'booking-token-b';
        $anggotaB->forceFill(['api_token_hash' => hash('sha256', $tokenB)])->save();

        $this->withHeaders(['Authorization' => 'Bearer '.$tokenB])->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => $anggotaB->nama,
            'nis' => $anggotaB->nis,
            'buku_id' => $buku->id,
            'mode' => 'booking',
            'tanggal_ambil' => now()->toDateString(),
        ])->assertStatus(400)
            ->assertJsonPath('success', false);

        $this->assertSame(0, $buku->fresh()->stok_tersedia);
        $this->assertSame(1, Peminjaman::where('buku_id', $buku->id)
            ->where('status_reservasi', Peminjaman::STATUS_RESERVASI_BOOKING)
            ->count());
    }

    /**
     * Booking yang terlewat tanggal ambil otomatis expired dan stok dikembalikan.
     */
    public function test_booking_terlewat_tanggal_ambil_expired_dan_stok_kembali(): void
    {
        [$anggota] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $buku->update(['stok_tersedia' => 1]);

        $booking = Peminjaman::create([
            'anggota_id' => $anggota->id,
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->subDays(3)->toDateString(),
            'tanggal_ambil' => now()->subDay()->toDateString(),
            'tanggal_jatuh_tempo' => now()->subDay()->toDateString(),
            'durasi_pinjam' => 3,
            'status' => Peminjaman::STATUS_DIPINJAM,
            'status_reservasi' => Peminjaman::STATUS_RESERVASI_BOOKING,
        ]);
        $buku->decrement('stok_tersedia');

        $this->artisan('app:expire-book-reservations')->assertExitCode(0);

        $this->assertSame(Peminjaman::STATUS_RESERVASI_EXPIRED, $booking->fresh()->status_reservasi);
        $this->assertSame(1, $buku->fresh()->stok_tersedia);
    }

    /**
     * Siswa bisa membatalkan reservasi; stok kembali dan status menjadi dibatalkan.
     */
    public function test_siswa_bisa_membatalkan_reservasi(): void
    {
        [$anggota, $headers] = $this->anggotaWithToken();
        $buku = Buku::firstOrFail();
        $buku->update(['stok_tersedia' => 1]);

        $response = $this->withHeaders($headers)->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => $anggota->nama,
            'nis' => $anggota->nis,
            'buku_id' => $buku->id,
            'mode' => 'booking',
            'tanggal_ambil' => now()->addDay()->toDateString(),
        ]);
        $response->assertCreated();
        $bookingId = $response->json('data.id');

        $this->withHeaders($headers)->postJson("/api/v1/peminjaman/{$bookingId}/batalkan-reservasi")
            ->assertOk()
            ->assertJsonPath('data.status_reservasi', Peminjaman::STATUS_RESERVASI_DIBATALKAN);

        $this->assertSame(1, $buku->fresh()->stok_tersedia);

        // Pembatalan kedua tidak mengembalikan stok dua kali.
        $this->withHeaders($headers)->postJson("/api/v1/peminjaman/{$bookingId}/batalkan-reservasi")
            ->assertStatus(400);
        $this->assertSame(1, $buku->fresh()->stok_tersedia);
    }
}

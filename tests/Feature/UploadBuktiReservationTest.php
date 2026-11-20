<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
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
     * REGRESI: mode booking tanggal_pinjam = tanggal_ambil (bukan hari ini),
     * tanggal_jatuh_tempo = tanggal_ambil + durasi, stok TIDAK dikurangi.
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

        // Stok tidak dikurangi saat reservasi.
        $this->assertSame($stokAwal, $buku->fresh()->stok_tersedia);
    }
}

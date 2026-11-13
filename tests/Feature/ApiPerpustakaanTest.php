<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPerpustakaanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed database dan data dummy sebelum setiap uji.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Pagination wajib tampil pada endpoint daftar buku.
     */
    public function test_daftar_buku_memiliki_pagination(): void
    {
        $response = $this->getJson('/api/v1/buku?per_page=5&page=2');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 15);

        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Searching buku harus bisa lewat judul maupun nama penulis.
     */
    public function test_searching_buku_berdasarkan_judul_dan_penulis(): void
    {
        $judul = $this->getJson('/api/v1/buku?search=Laskar');
        $judul->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.judul', 'Laskar Pelangi');

        $penulis = $this->getJson('/api/v1/buku?search=Andrea');
        $penulis->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_daftar_buku_memfilter_relasi_dan_mempertahankan_pagination(): void
    {
        $buku = Buku::query()->with(['kategori', 'penulis', 'penerbit'])->firstOrFail();

        $response = $this->getJson('/api/v1/buku?search='.urlencode($buku->isbn).'&kategori_id='.$buku->kategori_id.'&penulis_id='.$buku->penulis_id.'&penerbit_id='.$buku->penerbit_id.'&per_page=1');

        $response->assertOk()
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $buku->id);
    }

    /**
     * CRUD master berjalan; kategori yang dipakai buku tidak boleh dihapus.
     */
    public function test_crud_kategori_dan_proteksi_hapus(): void
    {
        // Create
        $create = $this->postJson('/api/v1/kategori', ['nama' => 'Kamus']);
        $create->assertCreated()->assertJsonPath('success', true);

        // Update
        $update = $this->putJson('/api/v1/kategori/1', ['nama' => 'Novel Fiksi']);
        $update->assertOk()->assertJsonPath('data.nama', 'Novel Fiksi');

        // Read
        $show = $this->getJson('/api/v1/kategori/1');
        $show->assertOk()->assertJsonPath('data.id', 1);

        // Delete kategori yang masih dipakai buku harus ditolak.
        $this->deleteJson('/api/v1/kategori/1')->assertStatus(400);

        // Delete kategori yang tidak dipakai boleh dihapus.
        $kategoriBaru = Kategori::where('nama', 'Kamus')->firstOrFail();
        $this->deleteJson("/api/v1/kategori/{$kategoriBaru->id}")->assertOk();
    }

    /**
     * Validasi input wajib menolak payload yang tidak sesuai (422).
     */
    public function test_validasi_menolak_data_buku_tidak_lengkap(): void
    {
        $response = $this->postJson('/api/v1/buku', [
            'judul' => '',
            'stok' => 0,
            'tahun_terbit' => 99999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['judul', 'stok', 'tahun_terbit', 'kategori_id', 'penulis_id', 'penerbit_id']);
    }

    /**
     * Peminjaman mengurangi stok tersedia dan ditolak saat stok habis.
     */
    public function test_peminjaman_mengurangi_stok_dan_habisnya_ditolak(): void
    {
        $buku = Buku::where('judul', 'Perjalanan Cinta Pangeran Andika')->firstOrFail();

        $pinjam = $this->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => 'Budi Santoso',
            'nis' => '20249999',
            'buku_id' => $buku->id,
        ]);

        $pinjam->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'dipinjam')
            ->assertJsonStructure(['data' => ['tanggal_pinjam', 'tanggal_jatuh_tempo']])
            ->assertJsonPath('data.tanggal_jatuh_tempo', now()->addDays((int) env('LOAN_DURATION_DAYS', 3))->toDateString());

        $this->assertSame($buku->stok_tersedia - 1, $buku->fresh()->stok_tersedia);

        $stok = $buku->fresh()->stok_tersedia;
        for ($i = 0; $i < $stok; $i++) {
            $this->postJson('/api/v1/peminjaman', [
                'nama_peminjam' => "Peminjam {$i}",
                'nis' => '20240100',
                'buku_id' => $buku->id,
            ])->assertCreated();
        }

        // Stok sudah habis, peminjaman berikutnya harus ditolak.
        $this->postJson('/api/v1/peminjaman', [
            'nama_peminjam' => 'Peminjam Terakhir',
            'nis' => '20240100',
            'buku_id' => $buku->id,
        ])->assertStatus(400);
    }

    /**
     * Pengembalian menaikkan stok tersedia kembali dan hanya boleh sekali.
     */
    public function test_pengembalian_menaikkan_stok_dan_sudah_dikembalikan_ditolak(): void
    {
        $buku = Buku::where('judul', 'Bumi Manusia')->firstOrFail();
        $stokAwal = $buku->stok_tersedia;

        $peminjaman = Peminjaman::create([
            'nama_peminjam' => 'Rina Melati',
            'nis' => '20248888',
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->subDays(5)->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
            'status' => Peminjaman::STATUS_DIPINJAM,
        ]);
        $buku->decrement('stok_tersedia');

        $kembali = $this->postJson('/api/v1/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
        ]);

        $kembali->assertCreated()
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonStructure(['data' => ['pengembalian']]);

        $this->assertSame($stokAwal, $buku->fresh()->stok_tersedia);
        $this->assertSame('dikembalikan', $peminjaman->fresh()->status);

        // Peminjaman yang telah dikembalikan tidak boleh diproses lagi.
        $this->postJson('/api/v1/pengembalian', ['peminjaman_id' => $peminjaman->id])->assertStatus(400);
    }
}

<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiListingSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Endpoint daftar yang dipakai aplikasi mobile harus selalu 200.
     * Regresi: di produksi /buku dan /peminjaman mengembalikan 500 karena
     * eager load relasi opsional (ulasan/pengembalian) sementara tabelnya
     * belum ada di database server.
     */
    public function test_daftar_buku_dapat_dimuat(): void
    {
        $this->getJson('/api/v1/buku?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_daftar_peminjaman_dapat_dimuat(): void
    {
        $this->getJson('/api/v1/peminjaman?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_daftar_pengembalian_dapat_dimuat(): void
    {
        $this->getJson('/api/v1/pengembalian?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_detail_buku_dapat_dimuat(): void
    {
        $id = $this->getJson('/api/v1/buku?per_page=1')->json('data.0.id');

        $this->getJson('/api/v1/buku/'.$id)
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_ulasan_buku_dapat_dimuat(): void
    {
        $id = $this->getJson('/api/v1/buku?per_page=1')->json('data.0.id');

        $this->getJson('/api/v1/buku/'.$id.'/ulasan')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    /**
     * Saat migrasi tabel `ulasan` belum jalan di server, katalog dan daftar
     * peminjaman tetap harus bisa dimuat (tanpa data rating).
     */
    public function test_katalog_tetap_tampil_saat_tabel_ulasan_belum_ada(): void
    {
        Schema::drop('ulasan');

        $this->getJson('/api/v1/buku?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/peminjaman?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}

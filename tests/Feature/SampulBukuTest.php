<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SampulBukuTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_sampul_when_creating_buku(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();

        $file = UploadedFile::fake()->image('cover.jpg');

        $response = $this->actingAs($admin)->post(route('admin.buku.store'), [
            'judul' => 'Test Buku',
            'deskripsi' => 'Deskripsi',
            'sinopsis' => 'Sinopsis',
            'ringkasan' => 'Ringkasan',
            'tahun_terbit' => 2024,
            'isbn' => '978-0000000000',
            'stok' => 5,
            'kategori_id' => Kategori::factory()->create()->id,
            'penulis_id' => Penulis::factory()->create()->id,
            'penerbit_id' => Penerbit::factory()->create()->id,
            'sampul' => $file,
        ]);

        $response->assertRedirect();

        $buku = Buku::where('judul', 'Test Buku')->first();
        $this->assertNotNull($buku);
        $this->assertNotNull($buku->sampul);
        Storage::disk('public')->assertExists($buku->sampul);
    }

    public function test_sampul_endpoint_returns_image_when_file_exists(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();

        $file = UploadedFile::fake()->image('cover.jpg');

        $this->actingAs($admin)->post(route('admin.buku.store'), [
            'judul' => 'Test Buku',
            'deskripsi' => 'Deskripsi',
            'sinopsis' => 'Sinopsis',
            'ringkasan' => 'Ringkasan',
            'tahun_terbit' => 2024,
            'isbn' => '978-0000000000',
            'stok' => 5,
            'kategori_id' => Kategori::factory()->create()->id,
            'penulis_id' => Penulis::factory()->create()->id,
            'penerbit_id' => Penerbit::factory()->create()->id,
            'sampul' => $file,
        ]);

        $buku = Buku::where('judul', 'Test Buku')->first();
        $this->assertNotNull($buku);

        $response = $this->get(route('admin.buku.sampul', $buku));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_api_sampul_endpoint_returns_404_when_file_not_exists(): void
    {
        $buku = Buku::factory()->create(['sampul' => 'sampul/nonexistent.jpg']);

        $response = $this->getJson('/api/v1/buku/'.$buku->id.'/sampul');
        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Sampul tidak ditemukan.');
    }

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BukuImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_csv_with_minimal_columns_succeeds(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $csv = "Judul,ISBN,Kategori,Penulis,Penerbit,Stok\n\"Laskar Pelangi\",9789792212280,Novel,\"Andrea Hirata\",\"Gramedia Pustaka Utama\",12";
        $file = UploadedFile::fake()->createWithContent('buku.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.buku.import.preview'), ['file' => $file])
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.buku.import.process'), [
                'selected' => [0],
                'rows' => [
                    0 => ['Laskar Pelangi', '9789792212280', 'Novel', 'Andrea Hirata', 'Gramedia Pustaka Utama', '12'],
                ],
            ])
            ->assertRedirect(route('admin.buku.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('buku', ['judul' => 'Laskar Pelangi', 'isbn' => '9789792212280', 'stok' => 12]);
    }
}

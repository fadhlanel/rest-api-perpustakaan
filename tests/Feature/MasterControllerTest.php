<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_delete_kategori_with_no_books(): void
    {
        $kategori = Kategori::factory()->create();

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('master.destroy', ['type' => 'kategori', 'id' => $kategori->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
    }

    /** @test */
    public function admin_can_delete_penulis_with_no_books(): void
    {
        $penulis = Penulis::factory()->create();

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('master.destroy', ['type' => 'penulis', 'id' => $penulis->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('penulis', ['id' => $penulis->id]);
    }

    /** @test */
    public function admin_can_delete_penerbit_with_no_books(): void
    {
        $penerbit = Penerbit::factory()->create();

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('master.destroy', ['type' => 'penerbit', 'id' => $penerbit->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('penerbit', ['id' => $penerbit->id]);
    }

    /** @test */
    public function delete_kategori_with_books_redirects_to_reassign_with_warning(): void
    {
        $kategori = Kategori::factory()->create();
        Buku::factory()->count(3)->create(['kategori_id' => $kategori->id]);

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('master.destroy', ['type' => 'kategori', 'id' => $kategori->id]));

        $response->assertRedirect(route('master.reassign', 'kategori'));
        $response->assertSessionHas('warning');
        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
        $this->assertEquals(3, Buku::whereNull('kategori_id')->count());
    }

    /** @test */
    public function reassign_page_loads_buku_without_kategori(): void
    {
        $buku = Buku::factory()->create(['kategori_id' => null]);

        $response = $this->actingAs($this->createAdmin())
            ->get(route('master.reassign', 'kategori'));

        $response->assertOk();
        $response->assertViewHas('type', 'kategori');
        $response->assertViewHas('bukus');
        $response->assertViewHas('options');
    }

    /** @test */
    public function bulk_reassign_updates_all_null_buku(): void
    {
        Kategori::factory()->create();
        $newKategori = Kategori::factory()->create();
        Buku::factory()->count(5)->create(['kategori_id' => null]);

        $response = $this->actingAs($this->createAdmin())
            ->post(route('master.reassign.bulk', 'kategori'), ['new_id' => $newKategori->id]);

        $response->assertRedirect(route('master.index', 'kategori'));
        $response->assertSessionHas('success');
        $this->assertEquals(5, Buku::where('kategori_id', $newKategori->id)->count());
    }

    private function createAdmin()
    {
        return User::factory()->admin()->create();
    }
}

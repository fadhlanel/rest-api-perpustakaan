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

    public function test_admin_can_delete_kategori_with_no_books(): void
    {
        $kategori = Kategori::factory()->create();

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('admin.master.destroy', ['type' => 'kategori', 'id' => $kategori->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
    }

    public function test_admin_can_delete_penulis_with_no_books(): void
    {
        $penulis = Penulis::factory()->create();

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('admin.master.destroy', ['type' => 'penulis', 'id' => $penulis->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('penulis', ['id' => $penulis->id]);
    }

    public function test_admin_can_delete_penerbit_with_no_books(): void
    {
        $penerbit = Penerbit::factory()->create();

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('admin.master.destroy', ['type' => 'penerbit', 'id' => $penerbit->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('penerbit', ['id' => $penerbit->id]);
    }

    public function test_delete_kategori_with_books_returns_warning_and_reassign_link(): void
    {
        $kategori = Kategori::factory()->create();
        Buku::factory()->count(3)->create(['kategori_id' => $kategori->id]);

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('admin.master.destroy', ['type' => 'kategori', 'id' => $kategori->id]));

        $response->assertRedirect(route('admin.master.index', 'kategori'));
        $response->assertSessionHas('warning', '3 Buku data kategorinya kosong, perbaiki datanya segera!');
        $response->assertSessionHas('warning_url', route('admin.master.reassign', 'kategori'));
        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
        $this->assertEquals(3, Buku::whereNull('kategori_id')->count());
    }

    public function test_delete_penulis_with_books_returns_warning_and_reassign_link(): void
    {
        $penulis = Penulis::factory()->create();
        Buku::factory()->count(2)->create(['penulis_id' => $penulis->id]);

        $response = $this->actingAs($this->createAdmin())
            ->delete(route('admin.master.destroy', ['type' => 'penulis', 'id' => $penulis->id]));

        $response->assertRedirect(route('admin.master.index', 'penulis'));
        $response->assertSessionHas('warning', '2 Buku data penulisnya kosong, perbaiki datanya segera!');
        $response->assertSessionHas('warning_url', route('admin.master.reassign', 'penulis'));
        $this->assertEquals(2, Buku::whereNull('penulis_id')->count());
    }

    public function test_reassign_page_loads_buku_without_kategori(): void
    {
        Buku::factory()->create(['kategori_id' => null]);

        $response = $this->actingAs($this->createAdmin())
            ->get(route('admin.master.reassign', 'kategori'));

        $response->assertOk();
        $response->assertViewHas('type', 'kategori');
        $response->assertViewHas('bukus');
        $response->assertViewHas('options');
    }

    public function test_bulk_reassign_updates_all_null_buku(): void
    {
        $newKategori = Kategori::factory()->create();
        Buku::factory()->count(5)->create(['kategori_id' => null]);

        $response = $this->actingAs($this->createAdmin())
            ->post(route('admin.master.reassign.bulk', 'kategori'), ['new_id' => $newKategori->id]);

        $response->assertRedirect(route('admin.master.index', 'kategori'));
        $response->assertSessionHas('success');
        $this->assertEquals(5, Buku::where('kategori_id', $newKategori->id)->count());
    }

    public function test_bell_notification_lists_all_master_data_issues(): void
    {
        $kategori = Kategori::factory()->create();
        $penulis = Penulis::factory()->create();
        $penerbit = Penerbit::factory()->create();

        Buku::factory()->count(13)->create([
            'kategori_id' => null,
            'penulis_id' => $penulis->id,
            'penerbit_id' => $penerbit->id,
        ]);
        Buku::factory()->count(2)->create([
            'kategori_id' => $kategori->id,
            'penulis_id' => null,
            'penerbit_id' => $penerbit->id,
        ]);
        Buku::factory()->count(4)->create([
            'kategori_id' => $kategori->id,
            'penulis_id' => $penulis->id,
            'penerbit_id' => null,
        ]);

        $response = $this->actingAs($this->createAdmin())
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('13 Buku data kategorinya kosong, perbaiki datanya segera!');
        $response->assertSee('2 Buku data penulisnya kosong, perbaiki datanya segera!');
        $response->assertSee('4 Buku data penerbitnya kosong, perbaiki datanya segera!');
        $response->assertSee(route('admin.master.reassign', 'kategori'), false);
        $response->assertSee(route('admin.master.reassign', 'penulis'), false);
        $response->assertSee(route('admin.master.reassign', 'penerbit'), false);
    }

    public function test_master_index_renders_clickable_warning(): void
    {
        $response = $this->actingAs($this->createAdmin())
            ->withSession([
                'warning' => '13 Buku data kategorinya kosong, perbaiki datanya segera!',
                'warning_url' => route('admin.master.reassign', 'kategori'),
            ])
            ->get(route('admin.master.index', 'kategori'));

        $response->assertOk();
        $response->assertSee('13 Buku data kategorinya kosong, perbaiki datanya segera!');
        $response->assertSee('Perbaiki sekarang');
        $response->assertSee(route('admin.master.reassign', 'kategori'), false);
    }

    public function test_bell_hidden_when_no_master_data_issues(): void
    {
        $response = $this->actingAs($this->createAdmin())
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Buku data kategorinya kosong');
        $response->assertSee('Tidak ada peringatan.');
    }

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }
}

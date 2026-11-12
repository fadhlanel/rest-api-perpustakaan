<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_is_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_public_announcements_route_is_registered(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/api/v1/pengumuman', 'GET'));

        $this->assertSame('App\\Http\\Controllers\\Api\\PengumumanController@index', $route->getActionName());
    }

    public function test_public_config_endpoint_returns_settings(): void
    {
        $response = $this->getJson('/api/v1/config');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_perpustakaan', 'PustakaDigital')
            ->assertJsonPath('data.status_layanan', 'aktif');
    }

    public function test_admin_can_access_and_update_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Sistem Perpustakaan');

        $this->actingAs($admin)
            ->put(route('admin.pengaturan.update'), [
                'nama_perpustakaan' => 'Perpustakaan SMK 1',
                'nama_sekolah' => 'SMK Negeri 1',
                'durasi_peminjaman' => 5,
                'batas_buku' => 4,
                'durasi_perpanjangan' => 2,
                'status_layanan' => 'aktif',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'nama_perpustakaan',
            'nilai' => 'Perpustakaan SMK 1',
        ]);
    }

    public function test_admin_can_store_master_data_via_json_inline(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.master.store', 'kategori'), [
                'nama' => 'Teknologi Informasi',
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Teknologi Informasi');

        $this->assertDatabaseHas('kategori', [
            'nama' => 'Teknologi Informasi',
        ]);
    }
}

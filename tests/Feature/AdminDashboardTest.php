<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_admin_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_admin_can_open_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Master Data & Katalog Buku', false)
            ->assertSee('Total Judul Terdaftar');
    }

    public function test_login_page_loads_vite_asset_and_accessible_fields(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('build/assets', false)
            ->assertSee('for="email"', false)
            ->assertSee('for="password"', false);
    }
}

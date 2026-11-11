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
            ->assertSee('Dashboard')
            ->assertSee('Koneksi database');
    }

    public function test_login_page_loads_vite_asset_and_accessible_fields(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('app.css', false)
            ->assertSee('for="email"', false)
            ->assertSee('for="password"', false);
    }
}

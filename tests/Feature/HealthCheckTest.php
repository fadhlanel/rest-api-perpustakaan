<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_status_page_is_removed(): void
    {
        $this->get('/api')->assertNotFound();
    }

    public function test_legacy_health_path_still_responds_for_cloud_probe(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'database' => 'ok',
            ]);
    }

    public function test_root_redirects_to_api_status_page(): void
    {
        $this->get('/')->assertRedirect('/admin/login');
    }

    public function test_admin_api_status_page_requires_authentication(): void
    {
        $this->get('/admin/status-api')->assertRedirect('/admin/login');
    }
}

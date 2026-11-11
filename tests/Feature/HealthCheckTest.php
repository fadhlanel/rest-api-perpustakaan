<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check_reports_application_and_database_status(): void
    {
        $this->getJson('/api')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'database' => 'ok',
            ]);
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
        $this->get('/')->assertRedirect('/api');
    }

    public function test_browser_api_status_page_renders(): void
    {
        $this->get('/api', ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('Pustaka Digital — API Status')
            ->assertSee('Database');
    }

    public function test_api_request_is_logged_and_shown_on_status_page(): void
    {
        $this->seed(DatabaseSeeder::class);
        Cache::store('file')->forget('api_request_logs');

        $this->getJson('/api/v1/buku')->assertOk();

        $this->get('/api', ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('/api/v1/buku');
    }
}

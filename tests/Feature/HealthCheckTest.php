<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_check_reports_application_and_database_status(): void
    {
        $this->getJson('/api')
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
}

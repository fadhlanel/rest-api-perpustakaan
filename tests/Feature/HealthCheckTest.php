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
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    public function test_admin_login_is_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_public_announcements_endpoint_is_registered(): void
    {
        $this->getJson('/api/v1/pengumuman')->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    public function test_admin_login_is_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_public_announcements_route_is_registered(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/api/v1/pengumuman', 'GET'));

        $this->assertSame('App\\Http\\Controllers\\Api\\PengumumanController@index', $route->getActionName());
    }
}

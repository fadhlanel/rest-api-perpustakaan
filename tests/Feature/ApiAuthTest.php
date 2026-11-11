<?php

namespace Tests\Feature;

use App\Models\Anggota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_login_and_read_profile(): void
    {
        Anggota::create(['nis' => '123', 'nama' => 'Siswa', 'kelas' => 'X', 'pin' => Hash::make('1234')]);

        $login = $this->postJson('/api/v1/login', ['nis' => '123', 'pin' => '1234'])->assertOk();
        $token = $login->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.siswa.nis', '123');
    }

    public function test_invalid_siswa_login_is_rejected(): void
    {
        $this->postJson('/api/v1/login', ['nis' => 'missing', 'pin' => '1234'])->assertUnauthorized();
    }
}

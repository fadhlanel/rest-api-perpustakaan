<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@perpustakaan.com'],
            [
                'name' => 'Ratna Sari, S.Pd',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}

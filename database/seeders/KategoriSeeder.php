<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Novel', 'Sejarah', 'Sains', 'Teknologi', 'Pendidikan'] as $nama) {
            Kategori::create(['nama' => $nama]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Penerbit;
use Illuminate\Database\Seeder;

class PenerbitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penerbits = [
            ['nama' => 'Gramedia Pustaka Utama', 'alamat' => 'Jl. Pancoran Timur, Jakarta Pusat'],
            ['nama' => 'Bentang Pustaka', 'alamat' => 'Yogyakarta'],
            ['nama' => 'Mizan Pustaka', 'alamat' => 'Bandung'],
            ['nama' => 'Falcon Publishing', 'alamat' => 'Jakarta'],
            ['nama' => 'Pustaka Republika', 'alamat' => 'Jakarta'],
        ];

        foreach ($penerbits as $penerbit) {
            Penerbit::create($penerbit);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Penulis;
use Illuminate\Database\Seeder;

class PenulisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penuliss = [
            ['nama' => 'Andrea Hirata', 'biografi' => 'Penulis asal Gantung, Belitung, terkenal lewat novel Laskar Pelangi.'],
            ['nama' => 'Tere Liye', 'biografi' => 'Penulis novel Indonesia populer, lulusan akuntansi dan penulis produktif.'],
            ['nama' => 'Dee Lestari', 'biografi' => 'Penulis dan penyanyi Indonesia, dikenal lewat Supernova dan Hana.'],
            ['nama' => 'Pramoedya Ananta Toer', 'biografi' => 'Sastrawan legendaris Indonesia, penulis tetralogi Buru.'],
            ['nama' => 'Khalil Gibran', 'biografi' => 'Puisi, pelukis, dan penulis Libanon-Amerika, pengarang Sang Nabi.'],
            ['nama' => 'Buya Hamka', 'biografi' => 'Ulama dan penulis Indonesia, penulis novel Tenggelamnya Kapal Van Der Wijck.'],
            ['nama' => 'Raditya Dika', 'biografi' => 'Penulis buku humor, komika, dan pembuat film Indonesia.'],
            ['nama' => 'Bung Karno (Soekarno)', 'biografi' => 'Proklamator Indonesia yang juga produktif menulis buku dan pidato.'],
        ];

        foreach ($penuliss as $penulis) {
            Penulis::create($penulis);
        }
    }
}

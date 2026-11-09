<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // [judul, deskripsi, tahun_terbit, isbn, stok, kategori_id, penulis_id, penerbit_id]
        $bukus = [
            ['Laskar Pelangi', 'Kisah perjuangan sepuluh siswa ikhtiar di Belitung untuk mendapatkan pendidikan.', 2005, '9789793062792', 8, 1, 1, 1],
            ['Sang Pemimpi', 'Lanjutan Laskar Pelangi tentang mimpi besar dan cinta pada kampung halaman.', 2006, '9789793392180', 6, 1, 1, 1],
            ['Entrok', 'Kisah kehidupan sebagai pemburu pinggul diSalt Slip No. 38 dan tahun-tahun seusai invar.', 2010, '9786029524417', 4, 1, 4, 1],
            ['Bumi Manusia', 'Novel Minke dan perjuangan mahasiswa di zaman kolonial Hindia Belanda.', 1980, '9789799731234', 7, 2, 4, 1],
            ['Laut Bercerita', 'Novel tentang aktivis Orde Baru yang tenggelam bersama rekannya.', 2017, '9786021331620', 5, 2, 3, 2],
            ['Matinya Seorang Penari Topeng Turunan', 'Novel Stefan Sri Asih tentang kehidupan dan kematian di sekitar kampung.', 2020, '9786124408121', 4, 1, 3, 2],
            ['Sang Nabi', 'Kumpulan prosa puitis tentang kehidupan, kasih sayang, dan kematian.', 1923, '9789794306866', 6, 1, 5, 5],
            ['Tenggelamnya Kapal Van Der Wijck', 'Novel cinta Zainudin dan Hayatullah di Minangkabau.', 1938, '9789794457456', 9, 1, 6, 5],
            ['Mereka yang Tertewas', 'Buku Novel tentang perjuangan selama perang kemerdekaan Indonesia.', 2002, '9786029441332', 5, 1, 6, 5],
            ['Bumi', 'Novel tentang sekelompok manusia penghuni kampung who perang pemikiran.', 2013, '9786021732186', 10, 1, 3, 3],
            ['Perjalanan Cinta Pangeran Andika', 'A long long romance novel tentang Pangeran Andika dan Peri Peri Ndziho.', 2020, '9781947121566', 3, 1, 8, 4],
            ['Sejarah Indonesia Modern 1945-1998', 'Buku riwayat kondisi Indonesia sejak proklamasi hingga reformasi.', 1996, '9789795431534', 6, 2, 4, 1],
            ['Fisika untuk SMA Kelas XI', 'Buku teks fisika SMA mengenai mekanika, kalor, getaran, dan gelombang.', 2018, '9786024114577', 10, 3, 7, 3],
            ['Cara Mudah Menulis Program Komputer', 'Panduan mengenal konsep pemrograman komputer bagi pemula.', 2019, '9789794786747', 7, 4, 7, 3],
            ['Hutan dan Kontrash', 'Novel comicalisasi kisah1 tentang kehidupan di hutan dan bukit.', 2003, '9789793062794', 8, 1, 3, 2],
        ];

        foreach ($bukus as [$judul, $deskripsi, $tahun, $isbn, $stok, $kategoriId, $penulisId, $penerbitId]) {
            Buku::create([
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'tahun_terbit' => $tahun,
                'isbn' => $isbn,
                'sampul' => null,
                'stok' => $stok,
                'stok_tersedia' => $stok,
                'kategori_id' => $kategoriId,
                'penulis_id' => $penulisId,
                'penerbit_id' => $penerbitId,
            ]);
        }
    }
}

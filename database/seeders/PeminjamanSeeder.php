<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Peminjaman masih berjalan (mengurangi stok tersedia).
        $dipinjam = [
            ['nama_peminjam' => 'Ahmad Fauzan', 'nis' => '20240101', 'judul' => 'Laskar Pelangi', 'tanggal_pinjam' => now()->subDays(3)],
            ['nama_peminjam' => 'Siti Nurhaliza', 'nis' => '20240102', 'judul' => 'Sang Pemimpi', 'tanggal_pinjam' => now()->subDays(1)],
        ];

        foreach ($dipinjam as $item) {
            $buku = Buku::where('judul', $item['judul'])->firstOrFail();
            $buku->decrement('stok_tersedia');

            Peminjaman::create([
                'nama_peminjam' => $item['nama_peminjam'],
                'nis' => $item['nis'],
                'buku_id' => $buku->id,
                'tanggal_pinjam' => $item['tanggal_pinjam']->toDateString(),
                'tanggal_jatuh_tempo' => $item['tanggal_pinjam']->addDays(7)->toDateString(),
                'status' => Peminjaman::STATUS_DIPINJAM,
            ]);
        }

        // Peminjaman yang sudah selesai dikembalikan.
        $buku = Buku::where('judul', 'Bumi Manusia')->firstOrFail();
        $buku->decrement('stok_tersedia');

        $peminjaman = Peminjaman::create([
            'nama_peminjam' => 'Rizki Wijaya',
            'nis' => '20240103',
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->subDays(10)->toDateString(),
            'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
            'status' => Peminjaman::STATUS_DIPINJAM,
        ]);

        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali' => now()->subDays(2)->toDateString(),
        ]);

        $peminjaman->update(['status' => Peminjaman::STATUS_DIKEMBALIKAN]);
        $buku->increment('stok_tersedia');
    }
}

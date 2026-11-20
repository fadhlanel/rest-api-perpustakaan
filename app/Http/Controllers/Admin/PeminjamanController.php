<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        // Query dasar
        $query = Peminjaman::with(['buku', 'pengembalian']);

        // Filter status khusus
        $status = $request->string('status')->toString();
        if ($status === 'hampir_tenggat') {
            $query->where('status', 'dipinjam')
                ->where('status_reservasi', '!=', Peminjaman::STATUS_RESERVASI_BOOKING)
                ->where('tanggal_jatuh_tempo', '<=', now()->addDay()->toDateString())
                ->where('tanggal_jatuh_tempo', '>=', $today);
        } elseif ($status === 'booking') {
            $query->where('status_reservasi', Peminjaman::STATUS_RESERVASI_BOOKING);
        } elseif ($status === 'bukti_pending') {
            $query->buktiPendingKonfirmasi();
        } elseif ($status === 'kembali_mandiri') {
            $query->where('status', 'dikembalikan')
                ->whereHas('pengembalian', function ($q) {
                    $q->whereNotNull('foto_bukti')->orWhere('tipe_kembali', 'mandiri');
                });
        } elseif ($status === 'terlambat') {
            $query->where('status', 'dipinjam')
                ->where('tanggal_jatuh_tempo', '<', $today);
        } elseif ($status === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        } elseif ($status === 'dipinjam') {
            $query->where('status', 'dipinjam');
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', '%'.$search.'%')
                    ->orWhere('nis', 'like', '%'.$search.'%')
                    ->orWhereHas('buku', fn ($q) => $q->where('judul', 'like', '%'.$search.'%'));
            });
        }

        // Default sort: bukti pending di atas
        if (empty($status)) {
            $query->orderByRaw('
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM pengembalian p
                        WHERE p.peminjaman_id = peminjaman.id
                        AND p.foto_bukti IS NOT NULL
                        AND p.dikonfirmasi = false
                    ) THEN 0 ELSE 1 END
            ')->latest('id');
        }

        $peminjaman = $query->paginate(10)->onEachSide(0)->withQueryString();

        // Hitung notifikasi & statistik status
        $stats = [
            'hampir_tenggat' => Peminjaman::where('status', 'dipinjam')
                ->where('status_reservasi', '!=', Peminjaman::STATUS_RESERVASI_BOOKING)
                ->where('tanggal_jatuh_tempo', '<=', now()->addDay()->toDateString())
                ->where('tanggal_jatuh_tempo', '>=', $today)
                ->count(),
            'booking' => Peminjaman::where('status_reservasi', Peminjaman::STATUS_RESERVASI_BOOKING)->count(),
            'kembali_mandiri' => Peminjaman::where('status', 'dikembalikan')
                ->whereHas('pengembalian', function ($q) {
                    $q->whereNotNull('foto_bukti')->orWhere('tipe_kembali', 'mandiri');
                })->count(),
            'sudah_dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'terlambat' => Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_jatuh_tempo', '<', $today)
                ->count(),
            'dipinjam_aktif' => Peminjaman::where('status', 'dipinjam')->count(),
            'bukti_pending' => Peminjaman::buktiPendingKonfirmasi()->count(),
        ];

        return view('admin.peminjaman.index', compact('peminjaman', 'stats'));
    }

    /**
     * Konfirmasi penyerahan buku booking/reservasi fisik kepada siswa.
     * Mengubah status_reservasi menjadi 'dipinjam', memotong stok buku,
     * dan mengaktifkan masa peminjaman aktif.
     */
    public function serahkanBuku(Peminjaman $peminjaman)
    {
        if (! $peminjaman->is_booking) {
            return back()->with('error', 'Peminjaman ini bukan reservasi atau buku sudah diserahkan.');
        }

        if ($peminjaman->status === 'dikembalikan') {
            return back()->with('error', 'Peminjaman ini sudah selesai/dikembalikan.');
        }

        $buku = $peminjaman->buku;
        if ($buku->stok_tersedia < 1) {
            return back()->with('error', 'Stok buku ini sedang habis di perpustakaan.');
        }

        DB::transaction(function () use ($peminjaman, $buku) {
            $today = now();
            $durasi = $peminjaman->durasi_pinjam ?: 3;

            $peminjaman->update([
                'status_reservasi' => Peminjaman::STATUS_RESERVASI_DIPINJAM,
                'tanggal_pinjam' => $today->toDateString(),
                'jam_pinjam' => $today->format('H:i'),
                'tanggal_jatuh_tempo' => $today->copy()->addDays($durasi)->toDateString(),
                'waktu_jatuh_tempo' => '23:59',
            ]);

            $buku->decrement('stok_tersedia');
        });

        return back()->with('success', "Buku {$buku->judul} berhasil diserahkan kepada {$peminjaman->nama_peminjam}. Status kini aktif dipinjam.");
    }

    public function return(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan.');
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update(['status' => 'dikembalikan']);

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_kembali' => now()->toDateString(),
                'tipe_kembali' => 'petugas',
                'catatan' => 'Dikonfirmasi kembali oleh admin perpustakaan',
            ]);

            $peminjaman->buku->increment('stok_tersedia');
        });

        return back()->with('success', 'Buku berhasil dikembalikan.');
    }

    /**
     * Sajikan foto bukti pengembalian mandiri siswa.
     */
    public function buktiFoto(Pengembalian $pengembalian): Response
    {
        $disk = Storage::disk('public');
        if (! $pengembalian->foto_bukti || ! $disk->exists($pengembalian->foto_bukti)) {
            return response('Foto bukti tidak ditemukan.', 404);
        }

        return response($disk->get($pengembalian->foto_bukti), 200, [
            'Content-Type' => $disk->mimeType($pengembalian->foto_bukti),
        ]);
    }

    /**
     * Konfirmasi pengembalian oleh admin (setelah cek bukti foto).
     */
    public function konfirmasiKembali(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan.');
        }

        $pengembalian = $peminjaman->pengembalian;

        if (! $pengembalian || ! $pengembalian->foto_bukti) {
            return back()->with('error', 'Tidak ada bukti foto pengembalian untuk dikonfirmasi.');
        }

        if ($pengembalian->dikonfirmasi) {
            return back()->with('error', 'Bukti ini sudah dikonfirmasi sebelumnya.');
        }

        DB::transaction(function () use ($peminjaman, $pengembalian) {
            $peminjaman->update(['status' => 'dikembalikan']);

            $pengembalian->update([
                'dikonfirmasi' => true,
                'tanggal_konfirmasi' => now(),
            ]);

            $peminjaman->buku->increment('stok_tersedia');
        });

        return back()->with('success', 'Pengembalian berhasil dikonfirmasi. Stok buku diperbarui.');
    }
}

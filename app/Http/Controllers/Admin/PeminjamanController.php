<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['buku', 'pengembalian']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', '%'.$search.'%')
                    ->orWhere('nis', 'like', '%'.$search.'%')
                    ->orWhereHas('buku', fn ($q) => $q->where('judul', 'like', '%'.$search.'%'));
            });
        }

        $peminjaman = $query->latest()->paginate(10)->withQueryString();

        return view('admin.peminjaman.index', compact('peminjaman'));
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
                'tanggal_kembali' => now(),
            ]);

            $peminjaman->buku->increment('stok_tersedia');
        });

        return back()->with('success', 'Buku berhasil dikembalikan.');
    }
}

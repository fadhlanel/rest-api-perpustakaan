<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_buku' => 0,
            'total_eksemplar' => 0,
            'tersedia' => 0,
            'dipinjam' => 0,
            'database_ok' => false,
        ];
        $buku = collect();
        $kategori = collect();

        try {
            DB::connection()->getPdo();
            $stats = [
                'total_buku' => Buku::count(),
                'total_eksemplar' => Buku::sum('stok'),
                'tersedia' => Buku::sum('stok_tersedia'),
                'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
                'database_ok' => true,
            ];

            $query = Buku::with(['kategori', 'penulis', 'penerbit']);

            // Filter kategori
            if ($request->filled('kategori_id')) {
                $query->where('kategori_id', $request->integer('kategori_id'));
            }

            // Filter ketersediaan
            if ($request->filled('status_ketersediaan')) {
                $status = $request->string('status_ketersediaan')->toString();
                if ($status === 'tersedia') {
                    $query->where('stok_tersedia', '>', 0);
                } elseif ($status === 'habis') {
                    $query->where('stok_tersedia', 0);
                }
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', '%'.$search.'%')
                        ->orWhere('isbn', 'like', '%'.$search.'%')
                        ->orWhereHas('penulis', fn ($q) => $q->where('nama', 'like', '%'.$search.'%'))
                        ->orWhereHas('penerbit', fn ($q) => $q->where('nama', 'like', '%'.$search.'%'));
                });
            }

            $buku = $query->latest()->paginate(10)->withQueryString();
            $kategori = Kategori::orderBy('nama')->get(['id', 'nama']);
        } catch (\Throwable) {
        }

        return view('admin.dashboard', compact('stats', 'buku', 'kategori'));
    }
}

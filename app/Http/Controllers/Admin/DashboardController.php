<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_buku' => 0,
            'total_eksemplar' => 0,
            'tersedia' => 0,
            'dipinjam' => 0,
            'database_ok' => false,
        ];

        try {
            DB::connection()->getPdo();
            $stats = [
                'total_buku' => Buku::count(),
                'total_eksemplar' => Buku::sum('stok'),
                'tersedia' => Buku::sum('stok_tersedia'),
                'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
                'database_ok' => true,
            ];
        } catch (\Throwable) {
        }

        return view('admin.dashboard', compact('stats'));
    }
}

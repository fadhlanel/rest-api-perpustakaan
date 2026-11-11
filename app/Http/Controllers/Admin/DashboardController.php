<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $databaseOk = true;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $databaseOk = false;
        }

        $stats = [
            'total_buku' => Buku::count(),
            'total_eksemplar' => Buku::sum('stok'),
            'tersedia' => Buku::sum('stok_tersedia'),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'database_ok' => $databaseOk,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

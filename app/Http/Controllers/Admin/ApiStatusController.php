<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class ApiStatusController extends Controller
{
    public function index(): View
    {
        $databaseOk = false;
        $bookCount = 0;

        try {
            DB::connection()->getPdo();
            $databaseOk = true;
            $bookCount = Buku::count();
        } catch (\Throwable) {
        }

        return view('admin.api-status.index', [
            'databaseOk' => $databaseOk,
            'bookCount' => $bookCount,
            'checkedAt' => now(),
            'apiUrl' => url('/api'),
        ]);
    }
}

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
            'apiUrl' => url('/api/v1'),
            'logs' => array_slice((array) cache()->store('file')->get('api_request_logs', []), 0, 20),
            'logStats' => collect((array) cache()->store('file')->get('api_request_logs', []))->reduce(
                static function (array $stats, array $log): array {
                    $status = (int) ($log['status'] ?? 500);
                    $stats['total']++;
                    $status < 400 ? $stats['success']++ : $stats['error']++;

                    return $stats;
                },
                ['total' => 0, 'success' => 0, 'error' => 0],
            ),
        ]);
    }
}

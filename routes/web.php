<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PeminjamanController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('buku', BukuController::class)->except(['show']);

        Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('peminjaman/{peminjaman}/return', [PeminjamanController::class, 'return'])->name('peminjaman.return');
    });
});

Route::get('/api', function (): View|JsonResponse {
    $databaseOk = true;

    try {
        DB::connection()->getPdo();
    } catch (Throwable) {
        $databaseOk = false;
    }

    $payload = [
        'status' => $databaseOk ? 'ok' : 'error',
        'database' => $databaseOk ? 'ok' : 'unavailable',
    ];

    if (request()->expectsJson()) {
        return response()->json($payload, $databaseOk ? 200 : 503);
    }

    return view('status', [
        'databaseOk' => $databaseOk,
        'logs' => Cache::store('file')->get('api_request_logs', []),
    ]);
});

Route::get('/health', function (): View|JsonResponse {
    $databaseOk = true;

    try {
        DB::connection()->getPdo();
    } catch (Throwable) {
        $databaseOk = false;
    }

    if (request()->expectsJson()) {
        return response()->json([
            'status' => $databaseOk ? 'ok' : 'error',
            'database' => $databaseOk ? 'ok' : 'unavailable',
        ], $databaseOk ? 200 : 503);
    }

    return redirect('/api');
});

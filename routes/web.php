<?php

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PengumumanController;
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
        Route::get('buku/export', [BukuController::class, 'export'])->name('buku.export');
        Route::post('buku/import', [BukuController::class, 'import'])->name('buku.import');
        Route::get('master/{type}', [MasterController::class, 'index'])->name('master.index');
        Route::post('master/{type}', [MasterController::class, 'store'])->name('master.store');
        Route::put('master/{type}/{id}', [MasterController::class, 'update'])->name('master.update');
        Route::delete('master/{type}/{id}', [MasterController::class, 'destroy'])->name('master.destroy');
        Route::resource('anggota', AnggotaController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('pengumuman', PengumumanController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('peminjaman/{peminjaman}/return', [PeminjamanController::class, 'return'])->name('peminjaman.return');
    });
});

Route::get('/api', function (): View|JsonResponse {
    $databaseOk = true;
    $bukuCount = 0;
    $checkedAt = now()->toISOString();

    try {
        DB::select('SELECT 1');
        $bukuCount = DB::table('buku')->count();
    } catch (Throwable) {
        $databaseOk = false;
    }

    $payload = [
        'app' => 'ok',
        'status' => $databaseOk ? 'ok' : 'error',
        'database' => $databaseOk ? 'ok' : 'unavailable',
        'buku_count' => $bukuCount,
        'checked_at' => $checkedAt,
    ];

    if (request()->expectsJson()) {
        return response()->json($payload, $databaseOk ? 200 : 503);
    }

    return view('status', [
        'databaseOk' => $databaseOk,
        'bukuCount' => $bukuCount,
        'checkedAt' => $checkedAt,
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

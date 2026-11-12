<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PenerbitController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\PenulisController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('config', [ConfigController::class, 'index']);
    // Modul Master Buku (CRUD lengkap)
    Route::apiResource('buku', BukuController::class);
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('penulis', PenulisController::class)
        // Laravel meng-singularize "penulis" menjadi "penuli"; paksa agar
        // nama parameter rute cocok dengan nama argumen di controller.
        ->parameters(['penulis' => 'penulis']);
    Route::apiResource('penerbit', PenerbitController::class);

    // Modul Transaksi
    Route::get('peminjaman', [PeminjamanController::class, 'index']);
    Route::get('peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
    Route::post('peminjaman', [PeminjamanController::class, 'store']);
    Route::post('peminjaman/{peminjaman}/perpanjang', [PeminjamanController::class, 'extend']);

    Route::get('pengumuman', [PengumumanController::class, 'index']);
    Route::get('pengumuman/{pengumuman}', [PengumumanController::class, 'show']);

    Route::get('pengembalian', [PengembalianController::class, 'index']);
    Route::post('pengembalian', [PengembalianController::class, 'store']);
});

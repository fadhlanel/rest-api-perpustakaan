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
use App\Http\Controllers\Api\UlasanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'update']);
    Route::get('profile/edits', [AuthController::class, 'edits']);
    Route::put('profile/pin', [AuthController::class, 'changePin']);
    Route::get('config', [ConfigController::class, 'index']);
    // Modul Master Buku (CRUD lengkap)
    Route::apiResource('buku', BukuController::class);
    Route::get('buku/{buku}/sampul', [BukuController::class, 'sampul'])->name('v1.buku.sampul');
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

    // Rating buku (hanya peminjam, buku sudah dikembalikan).
    Route::get('peminjaman/{peminjaman}/rating', [UlasanController::class, 'index']);
    Route::post('peminjaman/{peminjaman}/rating', [UlasanController::class, 'store']);
    Route::get('buku/{buku}/ulasan', [UlasanController::class, 'buku']);

    Route::get('pengumuman', [PengumumanController::class, 'index']);
    Route::get('pengumuman/{pengumuman}', [PengumumanController::class, 'show']);

    Route::get('pengembalian', [PengembalianController::class, 'index']);
    Route::post('pengembalian', [PengembalianController::class, 'store']);
});

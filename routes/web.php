<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'ok',
            'database' => 'ok',
        ]);
    } catch (Throwable) {
        return response()->json([
            'status' => 'error',
            'database' => 'unavailable',
        ], 503);
    }
});

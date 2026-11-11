<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function () {
    return redirect('/api');
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

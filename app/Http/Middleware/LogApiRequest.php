<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Mencatat request API terakhir (maksimal 20) untuk ditampilkan
 * pada halaman status /api.
 */
class LogApiRequest
{
    private const MAX_ENTRIES = 20;

    private const CACHE_KEY = 'api_request_logs';

    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $logs = (array) Cache::get(self::CACHE_KEY, []);

        array_unshift($logs, [
            'time' => now()->format('d M Y H:i:s'),
            'method' => $request->method(),
            'path' => '/'.$request->path(),
            'status' => $response->getStatusCode(),
        ]);

        Cache::forever(self::CACHE_KEY, array_slice($logs, 0, self::MAX_ENTRIES));

        return $response;
    }
}

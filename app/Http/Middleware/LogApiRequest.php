<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Mencatat request API terakhir (maksimal 20) untuk ditampilkan
 * pada halaman status /api.
 *
 * Cache store file dipakai (bukan default database/array) agar log
 * tetap ada selama container nyala dan tidak bergantung pada tabel
 * cache database yang mungkin belum siap saat request pertama masuk.
 */
class LogApiRequest
{
    private const MAX_ENTRIES = 20;

    private const CACHE_STORE = 'file';

    private const CACHE_KEY = 'api_request_logs';

    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $store = Cache::store(self::CACHE_STORE);

        $logs = (array) $store->get(self::CACHE_KEY, []);

        array_unshift($logs, [
            'time' => now()->format('d M Y H:i:s'),
            'method' => $request->method(),
            'path' => '/'.$request->path(),
            'status' => $response->getStatusCode(),
        ]);

        $store->forever(self::CACHE_KEY, array_slice($logs, 0, self::MAX_ENTRIES));

        return $response;
    }
}

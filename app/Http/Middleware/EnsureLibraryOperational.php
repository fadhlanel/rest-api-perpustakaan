<?php

namespace App\Http\Middleware;

use App\Models\Pengaturan;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLibraryOperational
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $status = Pengaturan::get('status_layanan', 'aktif');
        if ($status === 'aktif') {
            return $next($request);
        }

        $label = $status === 'tutup' ? 'tutup' : 'dalam pemeliharaan';

        return new JsonResponse([
            'success' => false,
            'message' => "Layanan perpustakaan sedang {$label}. Peminjaman dan transaksi belum tersedia.",
            'status_layanan' => $status,
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}

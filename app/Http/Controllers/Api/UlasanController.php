<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UlasanResource;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    /**
     * Ulasan buku untuk satu peminjaman (hanya peminjam & sudah dikembalikan).
     */
    public function index(Peminjaman $peminjaman): JsonResponse
    {
        return $this->successResponse(
            new UlasanResource($peminjaman->load('ulasan')->ulasan->first()),
            'Detail ulasan peminjaman.'
        );
    }

    /**
     * Beri atau ubah rating untuk peminjaman selesai.
     *
     * Validasi: peminjam yang login, status dikembalikan, rating 1–5,
     * komentar maksimal 1000 karakter.
     */
    public function store(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        if (! $anggota) {
            return $this->errorResponse('Token tidak valid.', 401);
        }

        if (strtolower((string) $peminjaman->nis) !== strtolower((string) $anggota->nis)) {
            return $this->errorResponse('Bukan peminjam buku ini.', 403);
        }

        if ($peminjaman->status !== Peminjaman::STATUS_DIKEMBALIKAN) {
            return $this->errorResponse('Rating hanya dapat diberikan setelah buku dikembalikan.', 422);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
        ]);

        $ulasan = Ulasan::updateOrCreate(
            ['peminjaman_id' => $peminjaman->id],
            [
                'buku_id' => $peminjaman->buku_id,
                'nis' => $peminjaman->nis,
                'rating' => $validated['rating'],
                'komentar' => $validated['komentar'] ?? null,
            ]
        );

        return $this->successResponse(
            new UlasanResource($ulasan->load('buku')),
            $ulasan->wasRecentlyCreated ? 'Rating berhasil disimpan.' : 'Rating berhasil diperbarui.',
            $ulasan->wasRecentlyCreated ? 201 : 200
        );
    }

    public function buku(Buku $buku): JsonResponse
    {
        $ulasan = $buku->ulasan()->latest()->get();

        return $this->successResponse(
            UlasanResource::collection($ulasan),
            'Daftar ulasan buku.'
        );
    }

    private function authenticatedStudent(Request $request): ?Anggota
    {
        $token = $request->bearerToken();

        return $token ? Anggota::where('api_token_hash', hash('sha256', $token))->first() : null;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenulisRequest;
use App\Http\Requests\UpdatePenulisRequest;
use App\Http\Resources\PenulisResource;
use App\Models\Penulis;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenulisController extends Controller
{
    /**
     * Menampilkan daftar penulis (support pagination & searching).
     */
    public function index(Request $request): JsonResponse
    {
        /** @var LengthAwarePaginator<Penulis> $penuliss */
        $penuliss = Penulis::query()
            ->when($request->filled('search'), fn ($query) => $query->where('nama', 'like', '%'.$request->string('search').'%'))
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($penuliss, PenulisResource::class, 'Daftar penulis buku.', $request);
    }

    /**
     * Menyimpan penulis baru.
     */
    public function store(StorePenulisRequest $request): JsonResponse
    {
        $penulis = Penulis::create($request->validated());

        return $this->successResponse(new PenulisResource($penulis), 'Penulis berhasil ditambahkan.', 201);
    }

    /**
     * Menampilkan detail satu penulis.
     */
    public function show(Penulis $penulis): JsonResponse
    {
        return $this->successResponse(new PenulisResource($penulis), 'Detail penulis.');
    }

    /**
     * Memperbarui penulis.
     */
    public function update(UpdatePenulisRequest $request, Penulis $penulis): JsonResponse
    {
        $penulis->update($request->validated());

        return $this->successResponse(new PenulisResource($penulis->fresh()), 'Penulis berhasil diperbarui.');
    }

    /**
     * Menghapus penulis.
     */
    public function destroy(Penulis $penulis): JsonResponse
    {
        if ($penulis->bukus()->exists()) {
            return $this->errorResponse('Penulis tidak dapat dihapus karena masih digunakan oleh buku.', 400);
        }

        $penulis->delete();

        return $this->successResponse(null, 'Penulis berhasil dihapus.');
    }

    /**
     * Hitung jumlah data per halaman (1-100).
     */
    private function perPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', 10);

        return max(1, min(100, $perPage));
    }
}

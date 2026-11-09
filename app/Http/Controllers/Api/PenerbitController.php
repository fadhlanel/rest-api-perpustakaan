<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenerbitRequest;
use App\Http\Requests\UpdatePenerbitRequest;
use App\Http\Resources\PenerbitResource;
use App\Models\Penerbit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenerbitController extends Controller
{
    /**
     * Menampilkan daftar penerbit (support pagination & searching).
     */
    public function index(Request $request): JsonResponse
    {
        /** @var LengthAwarePaginator<Penerbit> $penerbits */
        $penerbits = Penerbit::query()
            ->when($request->filled('search'), fn ($query) => $query->where('nama', 'like', '%'.$request->string('search').'%'))
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($penerbits, PenerbitResource::class, 'Daftar penerbit buku.', $request);
    }

    /**
     * Menyimpan penerbit baru.
     */
    public function store(StorePenerbitRequest $request): JsonResponse
    {
        $penerbit = Penerbit::create($request->validated());

        return $this->successResponse(new PenerbitResource($penerbit), 'Penerbit berhasil ditambahkan.', 201);
    }

    /**
     * Menampilkan detail satu penerbit.
     */
    public function show(Penerbit $penerbit): JsonResponse
    {
        return $this->successResponse(new PenerbitResource($penerbit), 'Detail penerbit.');
    }

    /**
     * Memperbarui penerbit.
     */
    public function update(UpdatePenerbitRequest $request, Penerbit $penerbit): JsonResponse
    {
        $penerbit->update($request->validated());

        return $this->successResponse(new PenerbitResource($penerbit->fresh()), 'Penerbit berhasil diperbarui.');
    }

    /**
     * Menghapus penerbit.
     */
    public function destroy(Penerbit $penerbit): JsonResponse
    {
        if ($penerbit->bukus()->exists()) {
            return $this->errorResponse('Penerbit tidak dapat dihapus karena masih digunakan oleh buku.', 400);
        }

        $penerbit->delete();

        return $this->successResponse(null, 'Penerbit berhasil dihapus.');
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

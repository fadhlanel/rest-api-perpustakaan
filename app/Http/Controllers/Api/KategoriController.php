<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar kategori (support pagination & searching).
     */
    public function index(Request $request): JsonResponse
    {
        /** @var LengthAwarePaginator<Kategori> $kategoris */
        $kategoris = Kategori::query()
            ->when($request->filled('search'), fn ($query) => $query->where('nama', 'like', '%'.$request->string('search').'%'))
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($kategoris, KategoriResource::class, 'Daftar kategori buku.', $request);
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(StoreKategoriRequest $request): JsonResponse
    {
        $kategori = Kategori::create($request->validated());

        return $this->successResponse(new KategoriResource($kategori), 'Kategori berhasil ditambahkan.', 201);
    }

    /**
     * Menampilkan detail satu kategori.
     */
    public function show(Kategori $kategori): JsonResponse
    {
        return $this->successResponse(new KategoriResource($kategori), 'Detail kategori.');
    }

    /**
     * Memperbarui kategori.
     */
    public function update(UpdateKategoriRequest $request, Kategori $kategori): JsonResponse
    {
        $kategori->update($request->validated());

        return $this->successResponse(new KategoriResource($kategori->fresh()), 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Kategori $kategori): JsonResponse
    {
        if ($kategori->bukus()->exists()) {
            return $this->errorResponse('Kategori tidak dapat dihapus karena masih digunakan oleh buku.', 400);
        }

        $kategori->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus.');
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

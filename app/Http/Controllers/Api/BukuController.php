<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Menampilkan daftar buku (support pagination & searching).
     *
     * Query string yang didukung:
     * - search: cari berdasarkan judul buku atau nama penulis.
     * - kategori_id, penulis_id, penerbit_id: filter relasi.
     * - per_page, page: data pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search');

        /** @var LengthAwarePaginator<Buku> $bukus */
        $bukus = Buku::query()
            ->with(['kategori', 'penulis', 'penerbit'])
            ->when($request->filled('search'), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('judul', 'like', '%'.$search.'%')
                        ->orWhere('isbn', 'like', '%'.$search.'%')
                        ->orWhereHas('penulis', fn ($penulisQuery) => $penulisQuery->where('nama', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('kategori_id'), fn ($query) => $query->where('kategori_id', $request->integer('kategori_id')))
            ->when($request->filled('penulis_id'), fn ($query) => $query->where('penulis_id', $request->integer('penulis_id')))
            ->when($request->filled('penerbit_id'), fn ($query) => $query->where('penerbit_id', $request->integer('penerbit_id')))
            ->when($request->filled('urutkan'), fn ($query) => $query->orderBy($request->string('urutkan'), $request->string('arah', 'asc')))
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($bukus, BukuResource::class, 'Daftar buku perpustakaan.', $request);
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(StoreBukuRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Stok tersedia awal = stok total karena belum ada yang meminjam.
        $data['stok_tersedia'] = $data['stok'];

        $buku = Buku::create($data)->load(['kategori', 'penulis', 'penerbit']);

        return $this->successResponse(new BukuResource($buku), 'Buku berhasil ditambahkan.', 201);
    }

    /**
     * Menampilkan detail satu buku.
     */
    public function show(Buku $buku): JsonResponse
    {
        return $this->successResponse(
            new BukuResource($buku->load(['kategori', 'penulis', 'penerbit'])),
            'Detail buku.'
        );
    }

    /**
     * Memperbarui data buku.
     */
    public function update(UpdateBukuRequest $request, Buku $buku): JsonResponse
    {
        $data = $request->validated();

        // Jika stok diubah, sesuaikan stok_tersedia selisihnya (total koleksi berubah, buku dipinjam tetap).
        if (array_key_exists('stok', $data) && $data['stok'] !== $buku->stok) {
            $selisih = (int) $data['stok'] - $buku->stok;
            $stokTersediaBaru = $buku->stok_tersedia + $selisih;

            if ($stokTersediaBaru < 0) {
                return $this->errorResponse(
                    "Stok tidak dapat dikurangi menjadi {$data['stok']} karena ada {$buku->stok_tersedia} buku yang sedang dipinjam.",
                    400
                );
            }

            $data['stok_tersedia'] = $stokTersediaBaru;
        }

        $buku->update($data);

        return $this->successResponse(
            new BukuResource($buku->fresh()->load(['kategori', 'penulis', 'penerbit'])),
            'Buku berhasil diperbarui.'
        );
    }

    /**
     * Menghapus buku.
     */
    public function destroy(Buku $buku): JsonResponse
    {
        if ($buku->peminjamans()->exists()) {
            return $this->errorResponse('Buku tidak dapat dihapus karena memiliki riwayat peminjaman.', 400);
        }

        $buku->delete();

        return $this->successResponse(null, 'Buku berhasil dihapus.');
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

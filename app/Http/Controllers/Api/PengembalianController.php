<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengembalianRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Menampilkan daftar pengembalian (support pagination & searching).
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search');

        /** @var LengthAwarePaginator<Pengembalian> $pengembalians */
        $pengembalians = Pengembalian::query()
            ->with(['peminjaman.buku', 'peminjaman.pengembalian'])
            ->when($request->filled('search'), function ($query) use ($search) {
                $query->whereHas('peminjaman', fn ($peminjamanQuery) => $peminjamanQuery->where('nama_peminjam', 'like', '%'.$search.'%')
                    ->orWhere('nis', 'like', '%'.$search.'%')
                    ->orWhereHas('buku', fn ($bukuQuery) => $bukuQuery->where('judul', 'like', '%'.$search.'%')));
            })
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($pengembalians, PengembalianResource::class, 'Daftar pengembalian buku.', $request);
    }

    /**
     * Memproses pengembalian buku peminjaman yang masih berstatus dipinjam.
     */
    public function store(StorePengembalianRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $hasil = DB::transaction(function () use ($validated) {
            /** @var Peminjaman|null $peminjaman */
            $peminjaman = Peminjaman::query()
                ->with(['buku' => fn ($query) => $query->lockForUpdate()])
                ->whereKey($validated['peminjaman_id'])
                ->first();

            if (! $peminjaman instanceof Peminjaman) {
                return ['error' => $this->errorResponse('Data peminjaman tidak ditemukan.', 404)];
            }

            if ($peminjaman->status === Peminjaman::STATUS_DIKEMBALIKAN) {
                return ['error' => $this->errorResponse('Buku sudah dikembalikan sebelumnya.', 400)];
            }

            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_kembali' => now()->toDateString(),
            ]);

            $peminjaman->update(['status' => Peminjaman::STATUS_DIKEMBALIKAN]);

            // Buku yang dikembalikan meningkatkan stok tersedia kembali.
            $peminjaman->buku->increment('stok_tersedia');

            $peminjaman->load(['buku.kategori', 'buku.penulis', 'buku.penerbit', 'pengembalian']);

            return ['data' => new PeminjamanResource($peminjaman)];
        });

        if (isset($hasil['error'])) {
            return $hasil['error'];
        }

        return $this->successResponse($hasil['data'], 'Pengembalian buku berhasil diproses.', 201);
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

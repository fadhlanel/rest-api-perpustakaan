<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman (support pagination & filter).
     *
     * Query string: search (nama/NIS peminjam), status (dipinjam|dikembalikan), per_page, page.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search');

        /** @var LengthAwarePaginator<Peminjaman> $peminjamans */
        $peminjamans = Peminjaman::query()
            ->with(['buku.kategori', 'buku.penulis', 'buku.penerbit', 'pengembalian', 'ulasan'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('buku_id'), fn ($query) => $query->where('buku_id', $request->integer('buku_id')))
            ->when($request->filled('search'), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama_peminjam', 'like', '%'.$search.'%')
                        ->orWhere('nis', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($peminjamans, PeminjamanResource::class, 'Daftar peminjaman buku.', $request);
    }

    /**
     * Mencatat peminjaman buku oleh siswa.
     */
    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $hasil = DB::transaction(function () use ($validated) {
            $buku = Buku::whereKey($validated['buku_id'])->lockForUpdate()->first();

            if (! $buku instanceof Buku) {
                return ['error' => $this->errorResponse('Buku tidak ditemukan.', 404)];
            }

            if ($buku->stok_tersedia < 1) {
                return ['error' => $this->errorResponse('Stok buku ini sedang habis, tidak dapat dipinjam.', 400)];
            }

            $buku->decrement('stok_tersedia');

            $peminjaman = Peminjaman::create([
                'nama_peminjam' => $validated['nama_peminjam'],
                'nis' => $validated['nis'],
                'buku_id' => $buku->id,
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_ambil' => $validated['tanggal_ambil'] ?? now()->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays((int) env('LOAN_DURATION_DAYS', 3))->toDateString(),
                'status' => Peminjaman::STATUS_DIPINJAM,
            ]);

            return ['data' => new PeminjamanResource($peminjaman->load('buku'))];
        });

        if (isset($hasil['error'])) {
            return $hasil['error'];
        }

        return $this->successResponse($hasil['data'], 'Peminjaman buku berhasil dicatat.', 201);
    }

    /**
     * Memperpanjang peminjaman satu kali selama tujuh hari.
     */
    public function extend(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIPINJAM) {
            return $this->errorResponse('Peminjaman sudah dikembalikan.', 400);
        }

        if ($peminjaman->jumlah_perpanjangan >= 1) {
            return $this->errorResponse('Peminjaman hanya dapat diperpanjang satu kali.', 400);
        }

        $peminjaman->update([
            'tanggal_jatuh_tempo' => $peminjaman->tanggal_jatuh_tempo->addDays((int) env('LOAN_EXTENSION_DAYS', 1)),
            'jumlah_perpanjangan' => $peminjaman->jumlah_perpanjangan + 1,
        ]);

        return $this->successResponse(
            new PeminjamanResource($peminjaman->fresh()->load('buku')),
            'Peminjaman berhasil diperpanjang.',
        );
    }

    /**
     * Menampilkan detail satu peminjaman.
     */
    public function show(Peminjaman $peminjaman): JsonResponse
    {
        return $this->successResponse(
            new PeminjamanResource($peminjaman->load(['buku.kategori', 'buku.penulis', 'buku.penerbit', 'pengembalian', 'ulasan'])),
            'Detail peminjaman.'
        );
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

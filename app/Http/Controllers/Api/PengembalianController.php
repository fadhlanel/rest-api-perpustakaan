<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengembalianRequest;
use App\Http\Resources\PeminjamanResource;
use App\Http\Resources\PengembalianResource;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('bukti_kembali', 'public');
        } elseif ($request->filled('foto_bukti')) {
            $data = $request->string('foto_bukti')->toString();
            if (str_starts_with($data, 'data:image')) {
                @preg_match('/data:image\/(.*?);base64,(.*)$/', $data, $matches);
                if (isset($matches[2])) {
                    $ext = $matches[1] ?: 'jpg';
                    $decoded = base64_decode($matches[2]);
                    $filename = 'bukti_kembali/bukti_'.uniqid().'.'.$ext;
                    Storage::disk('public')->put($filename, $decoded);
                    $fotoPath = $filename;
                }
            } else {
                $fotoPath = $data;
            }
        }

        $hasil = DB::transaction(function () use ($validated, $fotoPath) {
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

            $dataPengembalian = [
                'peminjaman_id' => $peminjaman->id,
                'tanggal_kembali' => now()->toDateString(),
            ];

            if ($fotoPath) {
                $dataPengembalian['foto_bukti'] = $fotoPath;
            }
            if (isset($validated['tipe_kembali'])) {
                $dataPengembalian['tipe_kembali'] = $validated['tipe_kembali'];
            } elseif ($fotoPath) {
                $dataPengembalian['tipe_kembali'] = 'mandiri';
            }
            if (isset($validated['catatan'])) {
                $dataPengembalian['catatan'] = $validated['catatan'];
            }

            $pengembalian = Pengembalian::create($dataPengembalian);

            $peminjaman->update(['status' => Peminjaman::STATUS_DIKEMBALIKAN]);

            // Buku yang dikembalikan meningkatkan stok tersedia kembali.
            $peminjaman->buku->increment('stok_tersedia');

            $peminjaman->load(['buku.kategori', 'buku.penulis', 'buku.penerbit', 'pengembalian']);

            return ['data' => new PeminjamanResource($peminjaman)];
        });

        if (isset($hasil['error'])) {
            return $hasil['error'];
        }

        return $this->successResponse($hasil['data'], 'Buku berhasil dikembalikan.', 201);
    }

    /**
     * Sajikan foto bukti pengembalian.
     */
    public function buktiFoto(Pengembalian $pengembalian): Response
    {
        $disk = Storage::disk('public');
        if (! $pengembalian->foto_bukti || ! $disk->exists($pengembalian->foto_bukti)) {
            return response('Foto bukti tidak ditemukan.', 404);
        }

        return response($disk->get($pengembalian->foto_bukti), 200, [
            'Content-Type' => $disk->mimeType($pengembalian->foto_bukti),
        ]);
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

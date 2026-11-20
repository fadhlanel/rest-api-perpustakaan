<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman (support pagination & filter).
     *
     * Query string: search (nama/NIS peminjam), status (dipinjam|dikembalikan), per_page, page.
     */
    public function index(Request $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        $search = $request->string('search');

        /** @var LengthAwarePaginator<Peminjaman> $peminjamans */
        $peminjamans = Peminjaman::query()
            ->when($anggota, fn ($query) => $query->where('anggota_id', $anggota->id))
            ->with($this->relations())
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('buku_id'), fn ($query) => $query->where('buku_id', $request->integer('buku_id')))
            ->when($request->filled('search'), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama_peminjam', 'like', '%'.$search.'%')
                        ->orWhereHas('buku', fn ($q) => $q->where('judul', 'like', '%'.$search.'%'));
                });
            })
            ->orderByDesc('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($peminjamans, PeminjamanResource::class, 'Daftar peminjaman buku.', $request);
    }

    private function authenticatedStudent(Request $request): ?Anggota
    {
        $token = $request->bearerToken();

        return $token ? Anggota::where('api_token_hash', hash('sha256', $token))->first() : null;
    }

    /**
     * Mencatat peminjaman buku oleh siswa.
     * Mode 'booking': reservasi (status_reservasi = booking, status = dipinjam, stok belum dikurangi)
     * Mode 'pinjam': langsung pinjam (stok dikurangi, status_reservasi = dipinjam)
     */
    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);
        if (! $anggota) {
            return $this->errorResponse('Sesi tidak valid. Silakan masuk kembali.', 401);
        }

        $validated = $request->validated();
        $validated['anggota_id'] = $anggota->id;
        $validated['nis'] = $anggota->nis;
        $validated['nama_peminjam'] = $anggota->nama;

        $mode = $validated['mode'] ?? 'pinjam'; // default: pinjam langsung

        $hasil = DB::transaction(function () use ($validated, $mode) {
            $buku = Buku::whereKey($validated['buku_id'])->lockForUpdate()->first();

            if (! $buku instanceof Buku) {
                return ['error' => $this->errorResponse('Buku tidak ditemukan.', 404)];
            }

            $maxDurasi = (int) (Pengaturan::get('durasi_peminjaman', 3));
            if ($maxDurasi < 1) {
                $maxDurasi = 3;
            }

            $durasiDefault = $mode === 'booking' ? min($maxDurasi, 3) : $maxDurasi;
            $durasiDipilih = isset($validated['durasi_hari']) && (int) $validated['durasi_hari'] > 0
                ? min((int) $validated['durasi_hari'], 3) // max 3 hari untuk reservasi
                : $durasiDefault;

            $tanggalAmbil = $validated['tanggal_ambil'] ?? now()->toDateString();
            $tanggalJatuhTempo = Carbon::parse($tanggalAmbil)->addDays($durasiDipilih)->toDateString();

            if ($mode === 'booking') {
                // Mode reservasi: stok TIDAK dikurangi, status_reservasi = booking
                if ($buku->stok_tersedia < 1) {
                    return ['error' => $this->errorResponse('Stok buku ini sedang habis, tidak dapat direservasi.', 400)];
                }

                $peminjaman = Peminjaman::create([
                    'anggota_id' => $validated['anggota_id'],
                    'nama_peminjam' => $validated['nama_peminjam'],
                    'nis' => $validated['nis'],
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => now()->toDateString(),
                    'tanggal_ambil' => $tanggalAmbil,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                    'durasi_pinjam' => $durasiDipilih,
                    'durasi_reservasi' => $durasiDipilih,
                    'tanggal_kembali_rencana' => $tanggalJatuhTempo,
                    'status' => Peminjaman::STATUS_DIPINJAM,
                    'status_reservasi' => Peminjaman::STATUS_RESERVASI_BOOKING,
                ]);

                return ['data' => new PeminjamanResource($peminjaman->load('buku')), 'mode' => 'booking'];
            }

            // Mode pinjam langsung: stok dikurangi
            if ($buku->stok_tersedia < 1) {
                return ['error' => $this->errorResponse('Stok buku ini sedang habis, tidak dapat dipinjam.', 400)];
            }

            $buku->decrement('stok_tersedia');

            $peminjaman = Peminjaman::create([
                'anggota_id' => $validated['anggota_id'],
                'nama_peminjam' => $validated['nama_peminjam'],
                'nis' => $validated['nis'],
                'buku_id' => $buku->id,
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_ambil' => $tanggalAmbil,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'durasi_pinjam' => $durasiDipilih,
                'durasi_reservasi' => $durasiDipilih,
                'tanggal_kembali_rencana' => $tanggalJatuhTempo,
                'status' => Peminjaman::STATUS_DIPINJAM,
                'status_reservasi' => Peminjaman::STATUS_RESERVASI_DIPINJAM,
            ]);

            return ['data' => new PeminjamanResource($peminjaman->load('buku')), 'mode' => 'pinjam'];
        });

        if (isset($hasil['error'])) {
            return $hasil['error'];
        }

        $message = $hasil['mode'] === 'booking'
            ? 'Reservasi buku berhasil. Ambil buku paling lambat tanggal '.$validated['tanggal_ambil'].'.'
            : 'Peminjaman buku berhasil dicatat.';

        return $this->successResponse($hasil['data'], $message, 201);
    }

    /**
     * Memperpanjang peminjaman dengan durasi hari yang dapat ditentukan pengguna (1 hari atau lebih).
     */
    public function extend(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIPINJAM) {
            return $this->errorResponse('Peminjaman sudah dikembalikan.', 400);
        }

        $requestDays = (int) request()->input('durasi_hari', env('LOAN_EXTENSION_DAYS', 1));
        if ($requestDays < 1) {
            return $this->errorResponse('Durasi perpanjangan minimal 1 hari.', 400);
        }

        $peminjaman->update([
            'tanggal_jatuh_tempo' => $peminjaman->tanggal_jatuh_tempo->addDays($requestDays),
            'durasi_pinjam' => $peminjaman->durasi_pinjam + $requestDays,
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
            new PeminjamanResource($peminjaman->load($this->relations())),
            'Detail peminjaman.'
        );
    }

    /**
     * Upload bukti foto pengembalian oleh siswa (Android).
     * Status peminjaman TIDAK berubah, menunggu konfirmasi admin.
     */
    public function uploadBuktiKembali(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);
        if (! $anggota) {
            return $this->errorResponse('Sesi tidak valid. Silakan masuk kembali.', 401);
        }

        // Cek ownership
        if ($peminjaman->anggota_id !== $anggota->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke peminjaman ini.', 403);
        }

        if ($peminjaman->status === 'dikembalikan') {
            return $this->errorResponse('Buku sudah dikembalikan dan dikonfirmasi.', 400);
        }

        $validated = $request->validate([
            'foto_bukti' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // 5MB max
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        // Hapus foto lama jika ada
        $pengembalian = $peminjaman->pengembalian;
        if ($pengembalian && $pengembalian->foto_bukti) {
            Storage::disk('public')->delete($pengembalian->foto_bukti);
        }

        $path = $request->file('foto_bukti')->store('bukti-kembali', 'public');

        if (! $pengembalian) {
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_kembali' => now()->toDateString(),
                'foto_bukti' => $path,
                'tipe_kembali' => 'mandiri',
                'catatan' => $validated['catatan'] ?? 'Dikembalikan mandiri oleh siswa di perpustakaan',
                'dikonfirmasi' => false,
            ]);
        } else {
            $pengembalian->update([
                'foto_bukti' => $path,
                'tipe_kembali' => 'mandiri',
                'catatan' => $validated['catatan'] ?? 'Dikembalikan mandiri oleh siswa di perpustakaan',
                'dikonfirmasi' => false,
                'tanggal_konfirmasi' => null,
            ]);
        }

        return $this->successResponse(
            [
                'peminjaman_id' => $peminjaman->id,
                'foto_bukti_url' => $pengembalian->foto_bukti_url,
                'dikonfirmasi' => false,
                'message' => 'Bukti pengembalian berhasil diunggah. Menunggu konfirmasi admin.',
            ],
            'Bukti pengembalian berhasil diunggah. Menunggu konfirmasi admin.'
        );
    }

    /**
     * Batalkan/Edit bukti pengembalian (sebelum dikonfirmasi admin).
     */
    public function batalkanBuktiKembali(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);
        if (! $anggota) {
            return $this->errorResponse('Sesi tidak valid. Silakan masuk kembali.', 401);
        }

        if ($peminjaman->anggota_id !== $anggota->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke peminjaman ini.', 403);
        }

        $pengembalian = $peminjaman->pengembalian;

        if (! $pengembalian || ! $pengembalian->foto_bukti) {
            return $this->errorResponse('Tidak ada bukti yang bisa dibatalkan.', 400);
        }

        if ($pengembalian->dikonfirmasi) {
            return $this->errorResponse('Bukti sudah dikonfirmasi admin, tidak bisa dibatalkan.', 400);
        }

        Storage::disk('public')->delete($pengembalian->foto_bukti);

        $pengembalian->update([
            'foto_bukti' => null,
            'dikonfirmasi' => false,
            'tanggal_konfirmasi' => null,
        ]);

        return $this->successResponse(
            ['peminjaman_id' => $peminjaman->id],
            'Bukti pengembalian dibatalkan. Anda bisa unggah ulang bukti baru.'
        );
    }

    /**
     * Daftar peminjaman dengan bukti kembalikan menunggu konfirmasi (Admin panel).
     */
    public function buktiPendingKonfirmasi(Request $request): JsonResponse
    {
        $peminjamans = Peminjaman::buktiPendingKonfirmasi()
            ->with($this->relations())
            ->orderBy('id')
            ->paginate($this->perPage($request));

        return $this->paginatedResponse($peminjamans, PeminjamanResource::class, 'Daftar bukti pengembalian menunggu konfirmasi.', $request);
    }

    /**
     * Relasi yang dimuat bersama peminjaman. Relasi `ulasan` opsional:
     * hanya dimuat bila tabelnya sudah ada, supaya daftar peminjaman tetap
     * tampil saat migrasi database produksi tertinggal.
     *
     * @return array<int, string>
     */
    private function relations(): array
    {
        $relations = ['buku.kategori', 'buku.penulis', 'buku.penerbit', 'pengembalian'];

        if (Schema::hasTable('ulasan')) {
            $relations[] = 'ulasan';
        }

        return $relations;
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

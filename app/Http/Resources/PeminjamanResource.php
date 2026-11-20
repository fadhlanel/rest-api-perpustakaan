<?php

namespace App\Http\Resources;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $now = now();
        $tanggalPinjam = $this->tanggal_pinjam;
        $tanggalJatuhTempo = $this->tanggal_jatuh_tempo;

        $hariBerjalan = $tanggalPinjam ? $tanggalPinjam->diffInDays($now, false) : 0;
        $sisaHari = $tanggalJatuhTempo ? max(0, $now->diffInDays($tanggalJatuhTempo, false)) : 0;
        $durasiHariTotal = $this->durasi_pinjam ?? 3;
        $waktuJatuhTempo = $this->waktu_jatuh_tempo;
        $sisaJam = $this->sisa_jam;
        $isHampirTenggat = $this->is_hampir_tenggat;

        $pengembalian = $this->relationLoaded('pengembalian') ? $this->pengembalian : null;

        return [
            'id' => $this->id,
            'nama_peminjam' => $this->nama_peminjam,
            'nis' => $this->nis,
            'buku' => $this->whenLoaded('buku', fn () => new BukuResource($this->buku)),
            'tanggal_pinjam' => $this->tanggal_pinjam?->toDateString(),
            'tanggal_ambil' => $this->tanggal_ambil?->toDateString(),
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo?->toDateString(),
            'tanggal_kembali_rencana' => $this->tanggal_kembali_rencana?->toDateString(),
            'jam_pinjam' => $this->created_at?->format('H:i'),
            'waktu_pinjam' => $this->created_at?->toIso8601String(),
            'waktu_jatuh_tempo' => $waktuJatuhTempo?->toIso8601String(),
            'durasi_pinjam' => (int) $durasiHariTotal,
            'durasi_reservasi' => $this->durasi_reservasi,
            'durasi_hari_total' => (int) $durasiHariTotal,
            'sisa_hari' => (int) $sisaHari,
            'sisa_jam' => (int) $sisaJam,
            'hampir_tenggat' => (bool) $isHampirTenggat,
            'jumlah_perpanjangan' => $this->jumlah_perpanjangan,
            'bisa_perpanjang' => $this->status === Peminjaman::STATUS_DIPINJAM && $this->jumlah_perpanjangan < 1,
            'status' => $this->status,
            'status_reservasi' => $this->status_reservasi ?? 'dipinjam',
            'is_booking' => $this->status_reservasi === Peminjaman::STATUS_RESERVASI_BOOKING,
            'bisa_batalkan_reservasi' => $this->bisa_batalkan_reservasi,
            'hari_berjalan' => (int) $hariBerjalan,
            'foto_bukti_url' => $pengembalian?->foto_bukti_url,
            'tipe_kembali' => $pengembalian?->tipe_kembali ?? ($pengembalian ? 'petugas' : null),
            'is_kembali_mandiri' => $this->is_kembali_mandiri,
            'bukti_pending_konfirmasi' => $this->bukti_pending_konfirmasi,
            'pengembalian' => $this->whenLoaded('pengembalian', fn () => new PengembalianResource($this->pengembalian)),
            'ulasan' => $this->whenLoaded('ulasan', fn () => new UlasanResource($this->ulasan)),
            'bisa_rating' => $this->status === Peminjaman::STATUS_DIKEMBALIKAN,
            'dibuat' => $this->created_at?->toDateTimeString(),
            'diubah' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

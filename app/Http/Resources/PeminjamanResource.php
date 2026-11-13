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
        return [
            'id' => $this->id,
            'nama_peminjam' => $this->nama_peminjam,
            'nis' => $this->nis,
            'buku' => $this->whenLoaded('buku', fn () => new BukuResource($this->buku)),
            'tanggal_pinjam' => $this->tanggal_pinjam?->toDateString(),
            'tanggal_ambil' => $this->tanggal_ambil?->toDateString(),
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo?->toDateString(),
            'jumlah_perpanjangan' => $this->jumlah_perpanjangan,
            'bisa_perpanjang' => $this->status === Peminjaman::STATUS_DIPINJAM && $this->jumlah_perpanjangan < 1,
            'status' => $this->status,
            'pengembalian' => $this->whenLoaded('pengembalian', fn () => new PengembalianResource($this->pengembalian)),
            'ulasan' => $this->whenLoaded('ulasan', fn () => new UlasanResource($this->ulasan)),
            'bisa_rating' => $this->status === Peminjaman::STATUS_DIKEMBALIKAN,
            'dibuat' => $this->created_at?->toDateTimeString(),
            'diubah' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

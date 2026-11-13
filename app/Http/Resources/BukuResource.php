<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
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
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tahun_terbit' => $this->tahun_terbit,
            'isbn' => $this->isbn,
            'sampul' => $this->sampul,
            'sampul_url' => $this->sampul
                ? route('v1.buku.sampul', ['buku' => $this->id], true)
                : null,
            'stok' => $this->stok,
            'stok_tersedia' => $this->stok_tersedia,
            'kategori' => $this->whenLoaded('kategori', fn () => new KategoriResource($this->kategori)),
            'penulis' => $this->whenLoaded('penulis', fn () => new PenulisResource($this->penulis)),
            'penerbit' => $this->whenLoaded('penerbit', fn () => new PenerbitResource($this->penerbit)),
            'dibuat' => $this->created_at?->toDateTimeString(),
            'diubah' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengembalianResource extends JsonResource
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
            'peminjaman' => $this->whenLoaded('peminjaman', fn () => new PeminjamanResource($this->peminjaman)),
            'tanggal_kembali' => $this->tanggal_kembali?->toDateString(),
            'dibuat' => $this->created_at?->toDateTimeString(),
            'diubah' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

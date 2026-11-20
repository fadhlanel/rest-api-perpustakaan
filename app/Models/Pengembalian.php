<?php

namespace App\Models;

use Database\Factories\PengembalianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    /** @use HasFactory<PengembalianFactory> */
    use HasFactory;

    protected $table = 'pengembalian';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'peminjaman_id',
        'tanggal_kembali',
        'foto_bukti',
        'tipe_kembali',
        'catatan',
        'dikonfirmasi',
        'tanggal_konfirmasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_kembali' => 'date:Y-m-d',
        'dikonfirmasi' => 'boolean',
        'tanggal_konfirmasi' => 'datetime',
    ];

    /**
     * URL untuk mengakses foto bukti pengembalian.
     */
    public function getFotoBuktiUrlAttribute(): ?string
    {
        if (! $this->foto_bukti) {
            return null;
        }

        if (str_starts_with($this->foto_bukti, 'http://') || str_starts_with($this->foto_bukti, 'https://') || str_starts_with($this->foto_bukti, 'data:image')) {
            return $this->foto_bukti;
        }

        return url('storage/'.$this->foto_bukti);
    }

    /**
     * Peminjaman yang telah dikembalikan.
     *
     * @return BelongsTo<Peminjaman, $this>
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }
}

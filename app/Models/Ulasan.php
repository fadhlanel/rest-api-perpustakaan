<?php

namespace App\Models;

use Database\Factories\UlasanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulasan extends Model
{
    /** @use HasFactory<UlasanFactory> */
    use HasFactory;

    protected $table = 'ulasan';

    protected $fillable = [
        'peminjaman_id',
        'buku_id',
        'nis',
        'rating',
        'komentar',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Peminjaman yang memberi rating ini.
     *
     * @return BelongsTo<Peminjaman, $this>
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * Buku yang dirating.
     *
     * @return BelongsTo<Buku, $this>
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }
}

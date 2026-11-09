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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_kembali' => 'date:Y-m-d',
    ];

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

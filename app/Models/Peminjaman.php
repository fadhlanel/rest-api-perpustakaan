<?php

namespace App\Models;

use Database\Factories\PeminjamanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    /** @use HasFactory<PeminjamanFactory> */
    use HasFactory;

    protected $table = 'peminjaman';

    public const STATUS_DIPINJAM = 'dipinjam';

    public const STATUS_DIKEMBALIKAN = 'dikembalikan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_peminjam',
        'nis',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pinjam' => 'date:Y-m-d',
        'tanggal_jatuh_tempo' => 'date:Y-m-d',
    ];

    /**
     * Buku yang dipinjam.
     *
     * @return BelongsTo<Buku, $this>
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    /**
     * Data pengembalian dari peminjaman ini.
     *
     * @return HasOne<Pengembalian, $this>
     */
    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }
}

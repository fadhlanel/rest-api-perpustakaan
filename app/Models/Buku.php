<?php

namespace App\Models;

use Database\Factories\BukuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    /** @use HasFactory<BukuFactory> */
    use HasFactory;

    protected $table = 'buku';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'tahun_terbit',
        'isbn',
        'sampul',
        'stok',
        'stok_tersedia',
        'kategori_id',
        'penulis_id',
        'penerbit_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun_terbit' => 'integer',
        'stok' => 'integer',
        'stok_tersedia' => 'integer',
    ];

    /**
     * Kategori dari buku ini.
     *
     * @return BelongsTo<Kategori, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Penulis dari buku ini.
     *
     * @return BelongsTo<Penulis, $this>
     */
    public function penulis(): BelongsTo
    {
        return $this->belongsTo(Penulis::class);
    }

    /**
     * Penerbit dari buku ini.
     *
     * @return BelongsTo<Penerbit, $this>
     */
    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(Penerbit::class);
    }

    /**
     * Riwayat peminjaman terhadap buku ini.
     */
    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }
}

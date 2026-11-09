<?php

namespace App\Models;

use Database\Factories\PenulisFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penulis extends Model
{
    /** @use HasFactory<PenulisFactory> */
    use HasFactory;

    protected $table = 'penulis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'biografi',
    ];

    /**
     * Buku-buku yang ditulis oleh penulis ini.
     *
     * @return HasMany<Buku, $this>
     */
    public function bukus(): HasMany
    {
        return $this->hasMany(Buku::class);
    }
}

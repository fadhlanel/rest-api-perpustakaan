<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = ['nis', 'nama', 'kelas', 'email', 'telepon', 'pin', 'pin_reset_at'];

    protected $hidden = ['pin', 'api_token_hash'];

    /**
     * Peminjaman oleh anggota ini.
     *
     * @return HasMany<Peminjaman, $this>
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Ulasan (rating) oleh anggota ini.
     *
     * @return HasMany<Ulasan, $this>
     */
    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class, 'nis', 'nis');
    }
}

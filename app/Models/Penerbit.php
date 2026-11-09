<?php

namespace App\Models;

use Database\Factories\PenerbitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerbit extends Model
{
    /** @use HasFactory<PenerbitFactory> */
    use HasFactory;

    protected $table = 'penerbit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'alamat',
    ];

    /**
     * Buku-buku yang diterbitkan oleh penerbit ini.
     *
     * @return HasMany<Buku, $this>
     */
    public function bukus(): HasMany
    {
        return $this->hasMany(Buku::class);
    }
}

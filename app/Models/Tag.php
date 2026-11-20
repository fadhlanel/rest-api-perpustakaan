<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['nama'];

    public function buku(): BelongsToMany
    {
        return $this->belongsToMany(Buku::class, 'buku_tag');
    }
}

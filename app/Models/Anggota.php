<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = ['nis', 'nama', 'kelas', 'email', 'telepon', 'pin', 'pin_reset_at'];

    protected $hidden = ['pin', 'api_token_hash'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = ['judul', 'isi', 'aktif', 'diterbitkan_pada'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'diterbitkan_pada' => 'datetime'];
    }
}

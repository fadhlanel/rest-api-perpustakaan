<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Konfigurasi sistem perpustakaan (key-value), dikelola dari halaman
 * Pengaturan Sistem dan dibaca Android via GET /api/v1/config.
 */
class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    /**
     * Nilai default saat kunci belum tersimpan di database.
     */
    public const DEFAULTS = [
        'nama_perpustakaan' => 'PustakaDigital',
        'nama_sekolah' => 'SMK / SMA',
        'durasi_peminjaman' => '3',
        'batas_buku' => '3',
        'durasi_perpanjangan' => '1',
        'status_layanan' => 'aktif',
    ];

    public static function get(string $kunci, ?string $default = null): string
    {
        return static::where('kunci', $kunci)->value('nilai')
            ?? static::DEFAULTS[$kunci]
            ?? $default
            ?? '';
    }

    public static function set(string $kunci, string $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
    }
}

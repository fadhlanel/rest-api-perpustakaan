<?php

namespace App\Models;

use Carbon\Carbon;
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

    public const STATUS_RESERVASI_BOOKING = 'booking';

    public const STATUS_RESERVASI_DIPINJAM = 'dipinjam';

    public const STATUS_RESERVASI_DIBATALKAN = 'dibatalkan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'anggota_id',
        'nama_peminjam',
        'nis',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_ambil',
        'tanggal_jatuh_tempo',
        'durasi_pinjam',
        'jumlah_perpanjangan',
        'status',
        'status_reservasi',
        'durasi_reservasi',
        'tanggal_kembali_rencana',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pinjam' => 'date:Y-m-d',
        'tanggal_ambil' => 'date:Y-m-d',
        'tanggal_jatuh_tempo' => 'date:Y-m-d',
        'tanggal_kembali_rencana' => 'date:Y-m-d',
        'durasi_pinjam' => 'integer',
        'durasi_reservasi' => 'integer',
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

    /**
     * Anggota yang melakukan peminjaman.
     *
     * @return BelongsTo<Anggota, $this>
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /**
     * Ulasan (rating) dari peminjaman ini.
     *
     * @return HasOne<Ulasan, $this>
     */
    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class);
    }

    /**
     * Waktu jatuh tempo spesifik dengan jam peminjaman.
     */
    public function getWaktuJatuhTempoAttribute()
    {
        if (! $this->tanggal_jatuh_tempo) {
            return null;
        }

        $jam = $this->created_at ? $this->created_at->format('H:i:s') : '23:59:59';

        return Carbon::parse($this->tanggal_jatuh_tempo->format('Y-m-d').' '.$jam);
    }

    /**
     * Menghitung sisa jam sebelum jatuh tempo.
     */
    public function getSisaJamAttribute(): int
    {
        if ($this->status === self::STATUS_DIKEMBALIKAN) {
            return 0;
        }

        $jatuhTempo = $this->waktu_jatuh_tempo;
        if (! $jatuhTempo) {
            return 0;
        }

        return (int) now()->diffInHours($jatuhTempo, false);
    }

    /**
     * Indikator status apakah hampir tenggat (kurang dari atau sama dengan 5 jam).
     * PENTING: Buku yang masih berstatus reservasi (booking) belum diambil oleh siswa,
     * sehingga TIDAK boleh dianggap hampir tenggat waktu pengembalian.
     */
    public function getIsHampirTenggatAttribute(): bool
    {
        if ($this->status !== self::STATUS_DIPINJAM || $this->is_booking) {
            return false;
        }

        $sisaJam = $this->sisa_jam;

        // Kurang dari 5 jam sebelum jatuh tempo (dan belum lewat dari 24 jam terlambat)
        return $sisaJam <= 5 && $sisaJam >= -24;
    }

    /**
     * Indikator apakah dikembalikan mandiri oleh siswa dengan bukti foto.
     */
    public function getIsKembaliMandiriAttribute(): bool
    {
        if (! $this->pengembalian) {
            return false;
        }

        return ! empty($this->pengembalian->foto_bukti) || ($this->pengembalian->tipe_kembali === 'mandiri');
    }

    /**
     * Cek apakah ada bukti kembalikan yang menunggu konfirmasi admin.
     */
    public function getBuktiPendingKonfirmasiAttribute(): bool
    {
        if (! $this->pengembalian) {
            return false;
        }

        return ! empty($this->pengembalian->foto_bukti)
            && $this->pengembalian->dikonfirmasi === false;
    }

    /**
     * Scope untuk peminjaman dengan bukti kembalikan pending konfirmasi.
     */
    public function scopeBuktiPendingKonfirmasi($query)
    {
        return $query->whereHas('pengembalian', function ($q) {
            $q->whereNotNull('foto_bukti')->where('dikonfirmasi', false);
        });
    }

    /**
     * Cek apakah status reservasi masih booking (menunggu ambil).
     */
    public function getIsBookingAttribute(): bool
    {
        return $this->status_reservasi === self::STATUS_RESERVASI_BOOKING;
    }

    /**
     * Cek apakah reservasi bisa dibatalkan (masih booking, belum diambil).
     */
    public function getBisaBatalkanReservasiAttribute(): bool
    {
        return $this->status_reservasi === self::STATUS_RESERVASI_BOOKING
            && $this->status === self::STATUS_DIPINJAM;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireBookReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-book-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire uncollected book reservations and release stock';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = 0;

        Peminjaman::query()
            ->where('status_reservasi', Peminjaman::STATUS_RESERVASI_BOOKING)
            ->whereDate('tanggal_ambil', '<', today())
            ->with('buku')
            ->chunkById(100, function ($bookings) use (&$count): void {
                foreach ($bookings as $booking) {
                    DB::transaction(function () use ($booking, &$count): void {
                        $locked = Peminjaman::whereKey($booking->id)->lockForUpdate()->first();
                        if (! $locked || ! $locked->is_booking) {
                            return;
                        }

                        $locked->update(['status_reservasi' => Peminjaman::STATUS_RESERVASI_EXPIRED]);
                        $locked->buku()->lockForUpdate()->first()?->increment('stok_tersedia');
                        $count++;
                    });
                }
            });

        $this->info("Expired {$count} reservation(s).");

        return self::SUCCESS;
    }
}

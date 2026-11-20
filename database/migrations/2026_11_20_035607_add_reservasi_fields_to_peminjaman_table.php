<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (! Schema::hasColumn('peminjaman', 'status_reservasi')) {
                $table->enum('status_reservasi', ['booking', 'dipinjam', 'dibatalkan'])->default('dipinjam')->after('status');
            }
            if (! Schema::hasColumn('peminjaman', 'durasi_reservasi')) {
                $table->unsignedTinyInteger('durasi_reservasi')->nullable()->after('status_reservasi');
            }
            if (! Schema::hasColumn('peminjaman', 'tanggal_kembali_rencana')) {
                $table->date('tanggal_kembali_rencana')->nullable()->after('durasi_reservasi');
            }
            if (! Schema::hasColumn('peminjaman', 'anggota_id')) {
                $table->foreignId('anggota_id')->nullable()->after('id')->constrained('anggotas')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['status_reservasi', 'durasi_reservasi', 'tanggal_kembali_rencana']);
            if (Schema::hasColumn('peminjaman', 'anggota_id')) {
                $table->dropForeign(['anggota_id']);
                $table->dropColumn('anggota_id');
            }
        });
    }
};

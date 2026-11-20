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
        Schema::table('pengembalian', function (Blueprint $table) {
            if (! Schema::hasColumn('pengembalian', 'foto_bukti')) {
                $table->string('foto_bukti', 255)->nullable()->after('tanggal_kembali');
            }
            if (! Schema::hasColumn('pengembalian', 'tipe_kembali')) {
                $table->string('tipe_kembali', 30)->default('petugas')->after('foto_bukti');
            }
            if (! Schema::hasColumn('pengembalian', 'catatan')) {
                $table->text('catatan')->nullable()->after('tipe_kembali');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn(['foto_bukti', 'tipe_kembali', 'catatan']);
        });
    }
};

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
            if (! Schema::hasColumn('pengembalian', 'dikonfirmasi')) {
                $table->boolean('dikonfirmasi')->default(false)->after('catatan');
            }
            if (! Schema::hasColumn('pengembalian', 'tanggal_konfirmasi')) {
                $table->timestamp('tanggal_konfirmasi')->nullable()->after('dikonfirmasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn(['dikonfirmasi', 'tanggal_konfirmasi']);
        });
    }
};

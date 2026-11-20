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
            $table->enum('status_reservasi', ['booking', 'dipinjam', 'dibatalkan', 'expired'])
                ->default('dipinjam')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status_reservasi', ['booking', 'dipinjam', 'dibatalkan'])
                ->default('dipinjam')
                ->change();
        });
    }
};

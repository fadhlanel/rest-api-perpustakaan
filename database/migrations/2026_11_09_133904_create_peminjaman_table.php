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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peminjam', 150);
            $table->string('nis', 20);
            $table->foreignId('buku_id')->constrained('buku')->cascadeOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_ambil')->nullable();
            $table->date('tanggal_jatuh_tempo');
            $table->unsignedTinyInteger('jumlah_perpanjangan')->default(0);
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};

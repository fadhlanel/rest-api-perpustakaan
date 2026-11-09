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
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('tahun_terbit');
            $table->string('isbn', 30)->nullable();
            $table->string('sampul', 255)->nullable();
            $table->unsignedInteger('stok')->default(0);
            $table->unsignedInteger('stok_tersedia')->default(0);
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->foreignId('penulis_id')->constrained('penulis')->cascadeOnDelete();
            $table->foreignId('penerbit_id')->constrained('penerbit')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};

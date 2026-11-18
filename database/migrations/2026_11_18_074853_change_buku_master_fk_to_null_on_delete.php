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
        Schema::table('buku', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['kategori_id']);
            $table->dropForeign(['penulis_id']);
            $table->dropForeign(['penerbit_id']);
        });

        Schema::table('buku', function (Blueprint $table) {
            // Make columns nullable
            $table->unsignedBigInteger('kategori_id')->nullable()->change();
            $table->unsignedBigInteger('penulis_id')->nullable()->change();
            $table->unsignedBigInteger('penerbit_id')->nullable()->change();
        });

        Schema::table('buku', function (Blueprint $table) {
            // Re-add with SET NULL on delete
            $table->foreign('kategori_id')->references('id')->on('kategori')->nullOnDelete();
            $table->foreign('penulis_id')->references('id')->on('penulis')->nullOnDelete();
            $table->foreign('penerbit_id')->references('id')->on('penerbit')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropForeign(['penulis_id']);
            $table->dropForeign(['penerbit_id']);
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable(false)->change();
            $table->unsignedBigInteger('penulis_id')->nullable(false)->change();
            $table->unsignedBigInteger('penerbit_id')->nullable(false)->change();
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategori')->cascadeOnDelete();
            $table->foreign('penulis_id')->references('id')->on('penulis')->cascadeOnDelete();
            $table->foreign('penerbit_id')->references('id')->on('penerbit')->cascadeOnDelete();
        });
    }
};

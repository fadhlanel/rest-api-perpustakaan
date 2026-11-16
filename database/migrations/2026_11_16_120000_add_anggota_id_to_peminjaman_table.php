<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('peminjaman', 'anggota_id')) {
            Schema::table('peminjaman', function (Blueprint $table): void {
                $table->foreignId('anggota_id')->nullable()->after('id')->constrained('anggota')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('peminjaman', 'anggota_id')) {
            Schema::table('peminjaman', function (Blueprint $table): void {
                $table->dropForeign(['anggota_id']);
                $table->dropColumn('anggota_id');
            });
        }
    }
};

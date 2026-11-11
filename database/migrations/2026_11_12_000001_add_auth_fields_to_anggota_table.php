<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table): void {
            $table->string('pin')->nullable()->after('telepon');
            $table->string('api_token_hash')->nullable()->index()->after('pin');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table): void {
            $table->dropIndex(['api_token_hash']);
            $table->dropColumn(['pin', 'api_token_hash']);
        });
    }
};

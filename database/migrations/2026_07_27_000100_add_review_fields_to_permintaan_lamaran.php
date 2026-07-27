<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table): void {
            $table->text('alasan_penolakan')->nullable()->after('status');
            $table->text('catatan_revisi')->nullable()->after('alasan_penolakan');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table): void {
            $table->dropColumn(['alasan_penolakan', 'catatan_revisi']);
        });
    }
};

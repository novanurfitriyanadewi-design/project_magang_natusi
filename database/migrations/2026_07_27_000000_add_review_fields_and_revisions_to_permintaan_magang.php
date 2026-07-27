<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_magang', function (Blueprint $table): void {
            $table->enum('status', ['menunggu', 'perlu_revisi', 'disetujui', 'ditolak'])
                ->default('menunggu')->change();
            $table->text('alasan_penolakan')->nullable()->after('status');
            $table->text('catatan_revisi')->nullable()->after('alasan_penolakan');
        });

        Schema::create('riwayat_berkas_magang', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_magang', 'id_permintaan')->cascadeOnDelete();
            $table->string('jenis_berkas', 100);
            $table->string('path');
            $table->unsignedInteger('versi')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_berkas_magang');
        Schema::table('permintaan_magang', function (Blueprint $table): void {
            $table->dropColumn(['alasan_penolakan', 'catatan_revisi']);
        });
    }
};

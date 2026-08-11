<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
            public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            $table->string('surat_lamaran_path')->nullable();
            $table->string('ijazah_path')->nullable();
            $table->string('ktp_path')->nullable();
            $table->string('pas_foto_path')->nullable();
            $table->string('skck_path')->nullable();
            $table->string('pengalaman_kerja_path')->nullable();
        });
    }
    

    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            $table->dropColumn([
                'surat_lamaran_path', 'ijazah_path', 'ktp_path',
                'pas_foto_path', 'skck_path', 'pengalaman_kerja_path',
            ]);
        });
    }
};
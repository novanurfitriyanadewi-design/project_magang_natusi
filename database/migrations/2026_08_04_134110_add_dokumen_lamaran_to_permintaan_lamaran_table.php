<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            $table->string('surat_lamaran_path')->nullable()->after('cv_path');
            $table->string('ijazah_path')->nullable()->after('surat_lamaran_path');
            $table->string('ktp_path')->nullable()->after('ijazah_path');
            $table->string('pas_foto_path')->nullable()->after('ktp_path');
            $table->string('skck_path')->nullable()->after('pas_foto_path');
            $table->string('pengalaman_kerja_path')->nullable()->after('portfolio_path');
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
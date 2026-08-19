<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_lamaran', 'surat_lamaran_path')) {
                $table->string('surat_lamaran_path')->nullable();
            }
            if (!Schema::hasColumn('permintaan_lamaran', 'cv_path')) {
                $table->string('cv_path')->nullable();
            }
            if (!Schema::hasColumn('permintaan_lamaran', 'ijazah_path')) {
                $table->string('ijazah_path')->nullable();
            }
            if (!Schema::hasColumn('permintaan_lamaran', 'ktp_path')) {
                $table->string('ktp_path')->nullable();
            }
            if (!Schema::hasColumn('permintaan_lamaran', 'pas_foto_path')) {
                $table->string('pas_foto_path')->nullable();
            }
            if (!Schema::hasColumn('permintaan_lamaran', 'skck_path')) {
                $table->string('skck_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            foreach (['ijazah_path', 'ktp_path', 'pas_foto_path', 'skck_path'] as $col) {
                if (Schema::hasColumn('permintaan_lamaran', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
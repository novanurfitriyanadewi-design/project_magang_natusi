<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permintaan_magang_anggota')) {
            return;
        }

        $hasCv = Schema::hasColumn('permintaan_magang_anggota', 'cv_path');
        $hasSurat = Schema::hasColumn('permintaan_magang_anggota', 'surat_pengajuan_path');

        if (! $hasCv) {
            Schema::table('permintaan_magang_anggota', function (Blueprint $table): void {
                $table->string('cv_path')->nullable()->after('no_hp');
            });
        }

        if (! $hasSurat) {
            Schema::table('permintaan_magang_anggota', function (Blueprint $table): void {
                $table->string('surat_pengajuan_path')->nullable()->after('cv_path');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permintaan_magang_anggota')) {
            return;
        }

        $columns = [];

        if (Schema::hasColumn('permintaan_magang_anggota', 'cv_path')) {
            $columns[] = 'cv_path';
        }

        if (Schema::hasColumn('permintaan_magang_anggota', 'surat_pengajuan_path')) {
            $columns[] = 'surat_pengajuan_path';
        }

        if ($columns !== []) {
            Schema::table('permintaan_magang_anggota', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};

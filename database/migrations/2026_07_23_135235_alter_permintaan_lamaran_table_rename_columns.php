<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            // Ubah nama kolom jika kolom lama ada
            if (Schema::hasColumn('permintaan_lamaran', 'no_induk')) {
                $table->renameColumn('no_induk', 'nik');
            } elseif (!Schema::hasColumn('permintaan_lamaran', 'nik')) {
                $table->string('nik', 50)->after('email')->nullable();
            }

            if (Schema::hasColumn('permintaan_lamaran', 'nama_sekolah')) {
                $table->renameColumn('nama_sekolah', 'pendidikan_terakhir');
            } elseif (!Schema::hasColumn('permintaan_lamaran', 'pendidikan_terakhir')) {
                $table->string('pendidikan_terakhir', 255)->after('nik')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (Schema::hasColumn('permintaan_lamaran', 'nik')) {
                $table->renameColumn('nik', 'no_induk');
            }
            if (Schema::hasColumn('permintaan_lamaran', 'pendidikan_terakhir')) {
                $table->renameColumn('pendidikan_terakhir', 'nama_sekolah');
            }
        });
    }
};
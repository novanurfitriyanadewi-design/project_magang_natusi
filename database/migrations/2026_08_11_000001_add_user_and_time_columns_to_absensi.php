<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id_absensi');
            }

            if (! Schema::hasColumn('absensi', 'jam_masuk')) {
                $table->time('jam_masuk')->nullable()->after('jam');
            }

            if (! Schema::hasColumn('absensi', 'jam_keluar')) {
                $table->time('jam_keluar')->nullable()->after('jam_masuk');
            }
        });

        // Tambah foreign key jika kolom user_id ada dan tabel users menggunakan id_user
        if (Schema::hasColumn('absensi', 'user_id')) {
            Schema::table('absensi', function (Blueprint $table) {
                try {
                    $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                } catch (\Exception $e) {
                    // ignore if cannot add FK (different DB setups)
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (Schema::hasColumn('absensi', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('absensi', 'jam_masuk')) {
                $table->dropColumn('jam_masuk');
            }

            if (Schema::hasColumn('absensi', 'jam_keluar')) {
                $table->dropColumn('jam_keluar');
            }
        });
    }
};

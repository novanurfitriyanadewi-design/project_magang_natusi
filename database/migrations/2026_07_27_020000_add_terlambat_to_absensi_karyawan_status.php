<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi_karyawan', function (Blueprint $table) {
            // MySQL requires full enum re-definition when altering enum values
            $table->string('status_temp')->nullable();
        });

        // Copy data to temp column
        DB::statement('UPDATE absensi_karyawan SET status_temp = status');

        // Drop original status column
        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Re-create with new enum values including 'terlambat'
        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('hadir');
        });

        // Copy data back
        DB::statement('UPDATE absensi_karyawan SET status = status_temp');

        // Drop temp column
        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->string('status_temp')->nullable();
        });

        DB::statement('UPDATE absensi_karyawan SET status_temp = status');

        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir');
        });

        DB::statement('UPDATE absensi_karyawan SET status = status_temp');

        Schema::table('absensi_karyawan', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }
};


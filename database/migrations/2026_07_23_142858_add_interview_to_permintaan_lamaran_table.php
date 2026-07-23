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
        // 1) Tambah value 'interview' ke enum status
        DB::statement("
            ALTER TABLE permintaan_lamaran
            MODIFY status ENUM('menunggu', 'interview', 'disetujui', 'ditolak')
            NOT NULL DEFAULT 'menunggu'
        ");

        // 2) Tambah kolom jadwal & lokasi interview
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            $table->dateTime('jadwal_interview')->nullable()->after('status');
            $table->string('lokasi_interview')->nullable()->after('jadwal_interview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            $table->dropColumn(['jadwal_interview', 'lokasi_interview']);
        });

        DB::statement("
            ALTER TABLE permintaan_lamaran
            MODIFY status ENUM('menunggu', 'disetujui', 'ditolak')
            NOT NULL DEFAULT 'menunggu'
        ");
    }
};
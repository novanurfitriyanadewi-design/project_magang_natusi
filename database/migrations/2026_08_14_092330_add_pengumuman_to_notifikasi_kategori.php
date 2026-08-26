<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE notifikasi
            MODIFY kategori ENUM(
                'pengajuan',
                'pembayaran',
                'penugasan',
                'absensi',
                'akun',
                'pengumuman'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE notifikasi
            MODIFY kategori ENUM(
                'pengajuan',
                'pembayaran',
                'penugasan',
                'absensi',
                'akun'
            ) NOT NULL
        ");
    }
};
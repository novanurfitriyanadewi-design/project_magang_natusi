<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan perubahan database.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE notifikasi
            MODIFY COLUMN kategori ENUM(
                'pengajuan',
                'pembayaran',
                'penugasan',
                'absensi',
                'akun',
                'pengumuman',
                'sertifikat',
                'cuti',
                'resign'
            ) NOT NULL
        ");
    }

    /**
     * Kembalikan perubahan database.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE notifikasi
            MODIFY COLUMN kategori ENUM(
                'pengajuan',
                'pembayaran',
                'penugasan',
                'absensi',
                'akun',
                'pengumuman',
                'sertifikat'
            ) NOT NULL
        ");
    }
};
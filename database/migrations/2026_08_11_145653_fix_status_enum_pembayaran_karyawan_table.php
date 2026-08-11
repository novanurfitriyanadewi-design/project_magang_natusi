<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pembayaran_karyawan MODIFY status ENUM('belum_terbayar', 'terbayar') NOT NULL DEFAULT 'belum_terbayar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pembayaran_karyawan MODIFY status ENUM('menunggu', 'lunas', 'ditolak') NOT NULL");
    }
};
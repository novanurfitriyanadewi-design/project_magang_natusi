<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE notifikasi MODIFY kategori VARCHAR(50) NOT NULL DEFAULT 'pengajuan'"
            );
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan otomatis menjadi ENUM agar data kategori tidak terpotong.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','admin_karyawan','admin_peserta','peserta','pelamar','pelamar_karyawan','karyawan') NOT NULL DEFAULT 'pelamar'");
        }
    }

    public function down(): void
    {
        // Role baru sengaja dipertahankan agar akun yang sudah dibuat tidak kehilangan data.
    }
};

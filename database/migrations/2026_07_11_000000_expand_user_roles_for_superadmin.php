<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['superadmin', 'admin', 'peserta', 'pelamar', 'karyawan'])
                    ->default('pelamar');
            });

            return;
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE users MODIFY role ENUM('superadmin','admin','peserta','pelamar','karyawan') NOT NULL DEFAULT 'pelamar'"
            );
        }
    }

    public function down(): void
    {
        //
    }
};

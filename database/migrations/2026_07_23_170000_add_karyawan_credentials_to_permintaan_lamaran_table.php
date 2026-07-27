<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_lamaran', 'password_karyawan')) {
                $table->string('password_karyawan')->nullable()->after('status');
            }
            if (!Schema::hasColumn('permintaan_lamaran', 'username_karyawan')) {
                $table->string('username_karyawan')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (Schema::hasColumn('permintaan_lamaran', 'password_karyawan')) {
                $table->dropColumn('password_karyawan');
            }
            if (Schema::hasColumn('permintaan_lamaran', 'username_karyawan')) {
                $table->dropColumn('username_karyawan');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_lamaran', 'akun_dibuat')) {
                $table->boolean('akun_dibuat')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (Schema::hasColumn('permintaan_lamaran', 'akun_dibuat')) {
                $table->dropColumn('akun_dibuat');
            }
        });
    }
};
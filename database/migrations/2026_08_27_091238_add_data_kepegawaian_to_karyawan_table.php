<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->date('tanggal_bergabung')
                ->nullable()
                ->after('divisi_id');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('tanggal_bergabung');
        });
    }
};
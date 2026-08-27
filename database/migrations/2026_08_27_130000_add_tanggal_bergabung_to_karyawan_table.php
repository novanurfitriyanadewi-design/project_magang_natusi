<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('karyawan', 'tanggal_bergabung')) {
            Schema::table('karyawan', function (Blueprint $table): void {
                $table->date('tanggal_bergabung')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('karyawan', 'tanggal_bergabung')) {
            Schema::table('karyawan', function (Blueprint $table): void {
                $table->dropColumn('tanggal_bergabung');
            });
        }
    }
};

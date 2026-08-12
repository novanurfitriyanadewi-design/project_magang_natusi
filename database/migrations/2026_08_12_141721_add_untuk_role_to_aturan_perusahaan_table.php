<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aturan_perusahaan', function (Blueprint $table) {
            if (! Schema::hasColumn('aturan_perusahaan', 'untuk_role')) {
                $table->string('untuk_role')
                    ->default('semua')
                    ->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aturan_perusahaan', function (Blueprint $table) {
            if (Schema::hasColumn('aturan_perusahaan', 'untuk_role')) {
                $table->dropColumn('untuk_role');
            }
        });
    }
};
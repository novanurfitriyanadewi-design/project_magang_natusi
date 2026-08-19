<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_lamaran', 'bidang_keahlian')) {
                $table->string('bidang_keahlian')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_lamaran', function (Blueprint $table) {
            if (Schema::hasColumn('permintaan_lamaran', 'bidang_keahlian')) {
                $table->dropColumn('bidang_keahlian');
            }
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank', function (Blueprint $table) {
            $table->string('qris_image')->nullable()->after('no_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('bank', function (Blueprint $table) {
            $table->dropColumn('qris_image');
        });
    }
};

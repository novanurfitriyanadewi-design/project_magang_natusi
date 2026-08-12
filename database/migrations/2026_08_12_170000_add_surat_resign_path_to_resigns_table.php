<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resigns', function (Blueprint $table) {
            $table->string('surat_resign_path')->nullable()->after('alasan');
            $table->string('surat_resign_original_name')->nullable()->after('surat_resign_path');
        });
    }

    public function down(): void
    {
        Schema::table('resigns', function (Blueprint $table) {
            $table->dropColumn(['surat_resign_path', 'surat_resign_original_name']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->foreignId('jurusan_id')
                ->nullable()
                ->after('kelas')
                ->constrained('jurusan', 'id_jurusan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jurusan_id');
        });
    }
};

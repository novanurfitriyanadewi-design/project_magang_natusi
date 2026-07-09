<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_mingguan', function (Blueprint $table) {
            $table->id('id_laporan');

            $table->foreignId('peserta_id')
                ->constrained('peserta_magang', 'id_peserta')
                ->cascadeOnDelete();

            $table->unsignedInteger('minggu_ke');
            $table->string('laporan');
            $table->timestamp('dikumpulkan_pada')->nullable();

            $table->timestamps();

            $table->unique([
                'peserta_id',
                'minggu_ke',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_mingguan');
    }
};
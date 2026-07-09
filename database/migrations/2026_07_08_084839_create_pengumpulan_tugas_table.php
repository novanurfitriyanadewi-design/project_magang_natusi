<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id('id_pengumpulan');

            $table->foreignId('tugas_id')
                ->constrained('tugas', 'id_tugas')
                ->cascadeOnDelete();

            $table->foreignId('peserta_id')
                ->constrained('peserta_magang', 'id_peserta')
                ->cascadeOnDelete();

            $table->string('file_jawaban');
            $table->timestamp('dikumpulkan_pada')->nullable();

            $table->enum('status', [
                'terkumpul',
                'telat',
                'dinilai',
            ])->default('terkumpul');

            $table->timestamps();

            $table->unique([
                'tugas_id',
                'peserta_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
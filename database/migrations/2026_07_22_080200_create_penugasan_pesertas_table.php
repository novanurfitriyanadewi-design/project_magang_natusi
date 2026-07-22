<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_peserta', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->foreignId('tugas_id')
                ->constrained('tugas', 'id_tugas')
                ->cascadeOnDelete();
            $table->foreignId('peserta_id')
                ->constrained('peserta_magang', 'id_peserta')
                ->cascadeOnDelete();
            $table->foreignId('template_laporan_id')
                ->nullable()
                ->constrained('template_laporan', 'id_template_laporan')
                ->nullOnDelete();
            $table->dateTime('tersedia_pada')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->enum('status', ['terjadwal', 'aktif', 'dilewati', 'selesai'])
                ->default('terjadwal');
            $table->text('keterangan')->nullable();
            $table->longText('ketentuan_laporan')->nullable();
            $table->timestamps();

            $table->unique(['tugas_id', 'peserta_id']);
            $table->index(['peserta_id', 'status', 'deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_peserta');
    }
};

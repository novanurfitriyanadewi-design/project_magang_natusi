<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id('id_sertifikat');

            $table->foreignId('peserta_id')
                ->constrained('peserta_magang', 'id_peserta')
                ->cascadeOnDelete();

            $table->foreignId('template_id')
                ->constrained('sertifikat_template', 'id_template')
                ->restrictOnDelete();

            $table->string('nomor_sertifikat')->unique();
            $table->string('judul')->default('Sertifikat Magang');
            $table->date('tanggal_terbit');

            $table->foreignId('diterbitkan_oleh')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            $table->enum('status', ['terbit', 'dicabut'])->default('terbit');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};

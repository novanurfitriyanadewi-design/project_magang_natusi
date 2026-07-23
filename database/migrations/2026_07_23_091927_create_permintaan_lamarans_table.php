<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            
            // Relasi ke id_permintaan di tabel permintaan_lamaran
            $table->unsignedBigInteger('permintaan_id')->nullable();
            
            $table->string('nip')->unique()->nullable();
            $table->string('nama_karyawan');
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jabatan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            
            $table->timestamps();

            // Foreign key constraint ke id_permintaan
            $table->foreign('permintaan_id')
                  ->references('id_permintaan')
                  ->on('permintaan_lamaran')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
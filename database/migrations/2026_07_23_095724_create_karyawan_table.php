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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            
            // Relasi ke tabel permintaan_lamaran (nullable jika karyawan diinput tanpa lamaran)
            $table->unsignedBigInteger('permintaan_id')->nullable();
            
            $table->string('nip')->unique()->nullable();
            $table->string('nama_karyawan');
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jabatan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('permintaan_id')
                  ->references('id_permintaan')
                  ->on('permintaan_lamaran')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
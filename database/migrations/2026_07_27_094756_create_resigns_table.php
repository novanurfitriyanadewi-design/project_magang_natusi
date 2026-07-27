<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resigns', function (Blueprint $table) {
            $table->id();
            // Menggunakan foreignId agar otomatis mendeteksi tipe data primary key tabel karyawan
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->text('alasan');
            $table->date('tanggal_efektif');
            $table->string('status')->default('pending'); // pending, diproses, menunggu_approval, disetujui, ditolak
            $table->text('catatan_hrd')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resigns');
    }
};
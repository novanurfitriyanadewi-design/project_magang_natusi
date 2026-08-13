<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('karyawan_id');

            $table->foreign('karyawan_id')
                ->references('id_karyawan')
                ->on('karyawan')
                ->onDelete('cascade');

            $table->enum('jenis_cuti', ['tahunan', 'sakit', 'melahirkan', 'penting', 'lainnya'])->default('tahunan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('alasan');
            $table->string('bukti_pendukung')->nullable();
            $table->string('status')->default('pending');
            $table->text('catatan_hrd')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};
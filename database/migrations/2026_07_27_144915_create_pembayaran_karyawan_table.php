<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran_karyawan', function (Blueprint $table) {
            $table->id('id_pembayaran'); // PK
            $table->unsignedBigInteger('karyawan_id');
            $table->string('periode', 7); // format Y-m
            $table->integer('nominal');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['menunggu', 'lunas', 'ditolak']);
            $table->string('keterangan', 500)->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->timestamps();

            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_karyawan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');

            $table->foreignId('id_bank')
                ->constrained('bank', 'id_bank')
                ->restrictOnDelete();

            $table->foreignId('nominal_id')
                ->constrained('nominal_pembayaran', 'id_nominal')
                ->restrictOnDelete();

            $table->foreignId('peserta_id')
                ->constrained('peserta_magang', 'id_peserta')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('nominal');
            $table->string('bukti_transfer');
            $table->dateTime('tgl_bayar')->nullable();

            $table->enum('status', [
                'menunggu',
                'lunas',
                'ditolak',
            ])->default('menunggu');

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
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

            $table->unsignedBigInteger('karyawan_id');

            $table->foreign('karyawan_id')
                ->references('id_karyawan')
                ->on('karyawan')
                ->onDelete('cascade');

            $table->text('alasan');
            $table->date('tanggal_efektif');
            $table->string('status')->default('pending');
            $table->text('catatan_hrd')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resigns');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman_karyawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengumuman_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->timestamps();

            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->onDelete('cascade');

            $table->foreign('karyawan_id')
                ->references('id_karyawan')
                ->on('karyawan')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman_karyawan');
    }
};
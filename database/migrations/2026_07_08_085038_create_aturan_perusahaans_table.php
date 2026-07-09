<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aturan_perusahaan', function (Blueprint $table) {
            $table->id('id_aturan');
            $table->string('nama');
            $table->string('kategori', 100);
            $table->text('deskripsi');
            $table->enum('status', [
                'aktif',
                'nonaktif',
            ])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_perusahaan');
    }
};
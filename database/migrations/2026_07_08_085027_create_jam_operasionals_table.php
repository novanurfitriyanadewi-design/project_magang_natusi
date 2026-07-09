<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_operasional', function (Blueprint $table) {
            $table->id('id_operasional');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_operasional');
    }
};
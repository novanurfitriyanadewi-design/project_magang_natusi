<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominal_pembayaran', function (Blueprint $table) {
            $table->id('id_nominal');
            $table->unsignedBigInteger('jumlah_nominal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominal_pembayaran');
    }
};
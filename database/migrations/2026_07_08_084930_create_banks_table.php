<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank', function (Blueprint $table) {
            $table->id('id_bank');
            $table->string('nama_bank', 100);
            $table->string('nama_pemilik');
            $table->string('no_rekening', 50);

            $table->timestamps();

            $table->unique([
                'nama_bank',
                'no_rekening',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank');
    }
};
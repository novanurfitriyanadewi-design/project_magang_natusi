<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_metode_pembayaran', function (Blueprint $table) {
            $table->id('id_riwayat');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            $table->string('aksi', 20);
            $table->string('entitas', 50);
            $table->string('deskripsi', 255);
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->timestamps();

            $table->index(['entitas', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_metode_pembayaran');
    }
};

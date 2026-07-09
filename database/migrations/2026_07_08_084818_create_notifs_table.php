<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');

            $table->foreignId('user_id')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('pesan');

            $table->enum('kategori', [
                'pengajuan',
                'pembayaran',
                'penugasan',
                'absensi',
                'akun',
            ]);

            $table->enum('tipe', [
                'info',
                'peringatan',
                'sukses',
            ])->default('info');

            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->boolean('dibaca')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
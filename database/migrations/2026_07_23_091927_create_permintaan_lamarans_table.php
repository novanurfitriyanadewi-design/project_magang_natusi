<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_lamaran', function (Blueprint $table) {
            $table->id('id_permintaan');
            $table->foreignId('user_id')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->string('nama_pemohon');
            $table->string('email');
            $table->string('no_induk', 50);
            $table->string('nama_sekolah');
            $table->string('posisi');
            $table->string('no_hp', 20);
            $table->text('pesan')->nullable();
            $table->date('tanggal_lamar')->nullable();
            $table->enum('status', ['menunggu', 'interview', 'disetujui', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_lamaran');
    }
};

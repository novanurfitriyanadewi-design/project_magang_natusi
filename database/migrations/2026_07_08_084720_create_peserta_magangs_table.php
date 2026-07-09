<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_magang', function (Blueprint $table) {
            $table->id('id_peserta');

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->foreignId('permintaan_id')
                ->nullable()
                ->unique()
                ->constrained('permintaan_magang', 'id_permintaan')
                ->nullOnDelete();

            $table->text('alamat');
            $table->string('tingkat_pendidikan', 100);
            $table->string('kelas', 100)->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('durasi_magang', 100)->nullable();
            $table->string('nama_guru')->nullable();
            $table->string('no_hpguru', 20)->nullable();

            $table->enum('status', [
                'aktif',
                'selesai',
                'dibatalkan',
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_magang');
    }
};
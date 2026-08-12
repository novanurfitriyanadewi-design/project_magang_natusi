<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman_penerima', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pengumuman')
                ->constrained('pengumuman', 'id_pengumuman')
                ->cascadeOnDelete();

            // karyawan / peserta
            $table->enum('tipe_penerima', [
                'karyawan',
                'peserta',
            ]);

            // id dari tabel karyawan atau peserta
            $table->unsignedBigInteger('id_penerima');

            $table->timestamps();

            $table->index(['tipe_penerima', 'id_penerima']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman_penerima');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id('id_tugas');

            $table->foreignId('user_id')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();

            $table->string('judul');
            $table->text('materi')->nullable();

            $table->enum('jenis_tugas', [
                'harian',
                'mingguan',
                'akhir',
            ]);

            $table->unsignedInteger('minggu_ke')->nullable();
            $table->string('file_tugas')->nullable();
            $table->dateTime('pengumpulan')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif',
                'selesai',
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
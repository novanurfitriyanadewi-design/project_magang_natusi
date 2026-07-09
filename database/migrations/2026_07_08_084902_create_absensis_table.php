<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('id_absensi');

            $table->foreignId('peserta_id')
                ->constrained('peserta_magang', 'id_peserta')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam')->nullable();

            $table->enum('status', [
                'hadir',
                'terlambat',
                'izin',
                'sakit',
                'alpa',
            ]);

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('jarak_meter', 10, 2)->nullable();

            $table->string('surat_izin')->nullable();
            $table->string('surat_sakit')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique([
                'peserta_id',
                'tanggal',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
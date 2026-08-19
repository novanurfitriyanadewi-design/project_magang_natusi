<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat_template', function (Blueprint $table) {
            $table->id('id_template');
            $table->string('nama_template');
            $table->string('file_background');
            $table->text('keterangan')->nullable();
            $table->enum('orientasi', ['landscape', 'portrait'])->default('landscape');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('dibuat_oleh')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat_template');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id('id_pengumuman');

            $table->string('judul');
            $table->text('isi');

            $table->enum('kategori', [
                'umum',
                'penting',
                'acara',
            ])->default('umum');

            $table->foreignId('dibuat_oleh')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            $table->boolean('aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
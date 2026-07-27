<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_laporan', function (Blueprint $table) {
            $table->id('id_template_laporan');
            $table->foreignId('user_id')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->enum('instansi', ['universitas', 'sekolah', 'semua']);
            $table->string('judul')->default('Template Laporan Magang');
            $table->string('file_word');
            $table->longText('ketentuan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['instansi', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_laporan');
    }
};

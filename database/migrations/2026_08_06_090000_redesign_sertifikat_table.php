<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropColumn(['token', 'data_hash', 'signature']);
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');

            $table->foreignId('divisi_id')
                ->nullable()
                ->after('peserta_id')
                ->constrained('divisi', 'id_divisi')
                ->nullOnDelete();

            $table->string('predikat')->nullable()->after('judul');
        });

        Schema::dropIfExists('sertifikat_template');
    }

    public function down(): void
    {
        Schema::create('sertifikat_template', function (Blueprint $table) {
            $table->id('id_template');
            $table->string('nama_template');
            $table->string('file_background');
            $table->text('keterangan')->nullable();
            $table->enum('orientasi', ['landscape', 'portrait'])->default('landscape');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropConstrainedForeignId('divisi_id');
            $table->dropColumn('predikat');

            $table->foreignId('template_id')
                ->nullable()
                ->constrained('sertifikat_template', 'id_template')
                ->restrictOnDelete();

            $table->string('token', 64)->nullable()->unique();
            $table->string('data_hash', 64)->nullable();
            $table->text('signature')->nullable();
        });
    }
};

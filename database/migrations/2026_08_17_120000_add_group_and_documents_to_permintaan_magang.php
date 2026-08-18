<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Awalnya peserta_magang.permintaan_id adalah UNIQUE + FOREIGN KEY.
         * Untuk pengajuan kelompok, satu permintaan harus boleh memiliki
         * lebih dari satu peserta. MySQL tidak mengizinkan UNIQUE index
         * penopang foreign key dihapus sebelum constraint FK dilepas.
         */
        Schema::table('peserta_magang', function (Blueprint $table): void {
            $table->dropForeign(['permintaan_id']);
        });

        Schema::table('peserta_magang', function (Blueprint $table): void {
            $table->dropUnique(['permintaan_id']);
            $table->index('permintaan_id');
        });

        Schema::table('peserta_magang', function (Blueprint $table): void {
            $table->foreign('permintaan_id')
                ->references('id_permintaan')
                ->on('permintaan_magang')
                ->nullOnDelete();
        });

        Schema::table('permintaan_magang', function (Blueprint $table): void {
            $table->string('jenjang', 30)->nullable()->after('jurusan');
            $table->string('tipe_pengajuan', 20)->default('individu')->after('jenjang');
            $table->unsignedTinyInteger('jumlah_anggota')->default(1)->after('tipe_pengajuan');
            $table->string('cv_path')->nullable()->after('pesan');
            $table->string('surat_pengajuan_path')->nullable()->after('cv_path');
        });

        Schema::create('permintaan_magang_anggota', function (Blueprint $table): void {
            $table->id('id_anggota');
            $table->foreignId('permintaan_id')
                ->constrained('permintaan_magang', 'id_permintaan')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();
            $table->foreignId('peserta_id')
                ->nullable()
                ->constrained('peserta_magang', 'id_peserta')
                ->nullOnDelete();
            $table->string('nama');
            $table->string('email');
            $table->string('no_induk', 100);
            $table->string('jurusan');
            $table->string('no_hp', 20);
            $table->boolean('is_ketua')->default(false);
            $table->string('username_peserta')->nullable();
            $table->string('password_awal')->nullable();
            $table->timestamps();

            $table->unique(['permintaan_id', 'email']);
            $table->unique(['permintaan_id', 'no_induk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_magang_anggota');

        Schema::table('permintaan_magang', function (Blueprint $table): void {
            $table->dropColumn([
                'jenjang',
                'tipe_pengajuan',
                'jumlah_anggota',
                'cv_path',
                'surat_pengajuan_path',
            ]);
        });

        // Kembalikan peserta_magang.permintaan_id menjadi UNIQUE seperti schema awal.
        Schema::table('peserta_magang', function (Blueprint $table): void {
            $table->dropForeign(['permintaan_id']);
        });

        Schema::table('peserta_magang', function (Blueprint $table): void {
            $table->dropIndex(['permintaan_id']);
            $table->unique('permintaan_id');
        });

        Schema::table('peserta_magang', function (Blueprint $table): void {
            $table->foreign('permintaan_id')
                ->references('id_permintaan')
                ->on('permintaan_magang')
                ->nullOnDelete();
        });
    }
};

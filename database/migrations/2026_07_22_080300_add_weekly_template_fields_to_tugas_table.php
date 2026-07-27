<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('target_peserta', 40)
                ->default('semua')
                ->after('instansi');
            $table->string('hari_tampil', 20)
                ->nullable()
                ->after('target_peserta');
            $table->string('hari_deadline', 20)
                ->nullable()
                ->after('hari_tampil');
            $table->time('jam_deadline')
                ->nullable()
                ->after('hari_deadline');

            $table->index(
                ['target_peserta', 'jenis_tugas', 'status'],
                'tugas_target_template_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropIndex('tugas_target_template_idx');
            $table->dropColumn([
                'target_peserta',
                'hari_tampil',
                'hari_deadline',
                'jam_deadline',
            ]);
        });
    }
};

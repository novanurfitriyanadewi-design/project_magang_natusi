<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('kode_tugas', 80)->nullable()->after('user_id');
            $table->enum('kategori_tugas', ['materi', 'tugas', 'laporan'])
                ->default('tugas')
                ->after('materi');
            $table->unsignedSmallInteger('rilis_hari_ke')->default(1)->after('minggu_ke');
            $table->unsignedSmallInteger('deadline_hari_ke')->nullable()->after('rilis_hari_ke');
            $table->string('hari_mulai')->default('semua')->after('deadline_hari_ke');
            $table->text('keterangan')->nullable()->after('hari_mulai');
            $table->uuid('template_batch')->nullable()->after('keterangan');

            $table->index(['jenis_tugas', 'instansi', 'status'], 'tugas_filter_template_idx');
            $table->unique(
                ['kode_tugas', 'jenis_tugas', 'instansi'],
                'tugas_kode_jenis_instansi_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropUnique('tugas_kode_jenis_instansi_unique');
            $table->dropIndex('tugas_filter_template_idx');
            $table->dropColumn([
                'kode_tugas',
                'kategori_tugas',
                'rilis_hari_ke',
                'deadline_hari_ke',
                'hari_mulai',
                'keterangan',
                'template_batch',
            ]);
        });
    }
};

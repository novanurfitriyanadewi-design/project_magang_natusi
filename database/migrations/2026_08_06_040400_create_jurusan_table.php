<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id('id_jurusan');
            $table->string('nama_jurusan');
            $table->enum('tingkat', ['smk', 'kuliah']);
            $table->string('kode', 20);
            $table->unsignedTinyInteger('durasi_min_bulan');
            $table->unsignedTinyInteger('durasi_max_bulan')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->unique(['nama_jurusan', 'tingkat']);
            $table->unique('kode');
        });

        // Data awal, hasil migrasi dari daftar jurusan yang sebelumnya hardcode.
        DB::table('jurusan')->insert([
            [
                'nama_jurusan' => 'Teknik Informatika',
                'tingkat' => 'kuliah',
                'kode' => 'TI',
                'durasi_min_bulan' => 1,
                'durasi_max_bulan' => 4,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Sistem Informasi',
                'tingkat' => 'kuliah',
                'kode' => 'SI',
                'durasi_min_bulan' => 1,
                'durasi_max_bulan' => 4,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Pend Teknik Informatika',
                'tingkat' => 'kuliah',
                'kode' => 'PTIK',
                'durasi_min_bulan' => 1,
                'durasi_max_bulan' => 4,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'RPL',
                'tingkat' => 'smk',
                'kode' => 'RPL',
                'durasi_min_bulan' => 5,
                'durasi_max_bulan' => null,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'TKJ',
                'tingkat' => 'smk',
                'kode' => 'TKJ',
                'durasi_min_bulan' => 5,
                'durasi_max_bulan' => null,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'SIJA',
                'tingkat' => 'smk',
                'kode' => 'SIJA',
                'durasi_min_bulan' => 5,
                'durasi_max_bulan' => null,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan');
    }
};

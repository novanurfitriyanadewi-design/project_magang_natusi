<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel sudah dibuat sebelumnya,
        // jadi migration ini hanya memastikan foreign key-nya ada.

        Schema::table('pengumuman_karyawan', function (Blueprint $table) {
            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->onDelete('cascade');

            $table->foreign('karyawan_id')
                ->references('id_karyawan')
                ->on('karyawan')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman_karyawan', function (Blueprint $table) {
            $table->dropForeign(['pengumuman_id']);
            $table->dropForeign(['karyawan_id']);
        });
    }
};
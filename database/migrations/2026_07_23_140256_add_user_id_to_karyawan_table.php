<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Tanpa klausa ->after() agar tidak tergantung nama kolom id tertentu
            if (!Schema::hasColumn('karyawan', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
                
                // Menambahkan foreign key ke tabel users (id_user)
                $table->foreign('user_id')
                      ->references('id_user')
                      ->on('users')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('karyawan', 'permintaan_id')) {
                $table->unsignedBigInteger('permintaan_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('karyawan', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('karyawan', 'permintaan_id')) {
                $table->dropColumn('permintaan_id');
            }
        });
    }
};
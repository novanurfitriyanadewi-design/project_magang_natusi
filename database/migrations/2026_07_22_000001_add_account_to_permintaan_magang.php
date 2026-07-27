<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('permintaan_magang', 'username_peserta')) {
            Schema::table('permintaan_magang', function (Blueprint $table): void {
                $table->string('username_peserta')
                    ->nullable()
                    ->after('user_id');
            });
        }

        if (! Schema::hasColumn('permintaan_magang', 'password_awal')) {
            Schema::table('permintaan_magang', function (Blueprint $table): void {
                $table->string('password_awal')
                    ->nullable()
                    ->after('username_peserta');
            });
        }

        if (! Schema::hasColumn('permintaan_magang', 'akun_dibuat')) {
            Schema::table('permintaan_magang', function (Blueprint $table): void {
                $table->boolean('akun_dibuat')
                    ->default(false)
                    ->after('password_awal');
            });
        }
    }

    public function down(): void
    {
        foreach (['akun_dibuat', 'password_awal', 'username_peserta'] as $column) {
            if (Schema::hasColumn('permintaan_magang', $column)) {
                Schema::table('permintaan_magang', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'foto_profil')) {
            Schema::table('users', function (Blueprint $table) {
                $table
                    ->string('foto_profil', 255)
                    ->nullable();
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('users')
            && Schema::hasColumn('users', 'foto_profil')
        ) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('foto_profil');
            });
        }
    }
};

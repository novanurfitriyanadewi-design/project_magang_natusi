<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->string('file_jawaban')->nullable()->change();
            $table->text('catatan')->nullable()->after('file_jawaban');
            $table->string('link_external')->nullable()->after('catatan');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE pengumpulan_tugas MODIFY status ENUM('draft','terkumpul','telat','dinilai') NOT NULL DEFAULT 'terkumpul'"
            );
        }
    }

    public function down(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->dropColumn(['catatan', 'link_external']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE pengumpulan_tugas MODIFY status ENUM('terkumpul','telat','dinilai') NOT NULL DEFAULT 'terkumpul'"
            );
        }
    }
};

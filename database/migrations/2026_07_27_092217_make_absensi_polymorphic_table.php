<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom baru kalau belum ada
        if (! Schema::hasColumn('absensi', 'absentable_id')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->unsignedBigInteger('absentable_id')->nullable()->after('id_absensi');
            });
        }

        if (! Schema::hasColumn('absensi', 'absentable_type')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->string('absentable_type')->nullable()->after('absentable_id');
            });
        }

        if (! Schema::hasColumn('absensi', 'peserta_id')) {
            return; // sudah bersih, tidak ada yang perlu dilakukan lagi
        }

        // 2. Salin data lama (peserta_id -> absentable_id) kalau belum disalin
        DB::table('absensi')
            ->whereNotNull('peserta_id')
            ->whereNull('absentable_id')
            ->update([
                'absentable_id' => DB::raw('peserta_id'),
                'absentable_type' => \App\Models\PesertaMagang::class,
            ]);

        // 3. Drop foreign key di peserta_id kalau masih ada
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'absensi'
              AND COLUMN_NAME = 'peserta_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `absensi` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // 4. Drop index/key biasa (non-FK) yang menempel di peserta_id kalau ada
        $indexes = DB::select("
            SELECT DISTINCT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'absensi'
              AND COLUMN_NAME = 'peserta_id'
              AND INDEX_NAME != 'PRIMARY'
        ");

        foreach ($indexes as $idx) {
            DB::statement("ALTER TABLE `absensi` DROP INDEX `{$idx->INDEX_NAME}`");
        }

        // 5. Baru drop kolomnya, pakai raw SQL (bukan Schema::dropColumn)
        DB::statement("ALTER TABLE `absensi` DROP COLUMN `peserta_id`");
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->unsignedBigInteger('peserta_id')->nullable();
            $table->dropColumn(['absentable_id', 'absentable_type']);
        });
    }
};
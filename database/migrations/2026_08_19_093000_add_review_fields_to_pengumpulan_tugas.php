<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table): void {
            if (! Schema::hasColumn('pengumpulan_tugas', 'status_review')) {
                // Data lama dianggap sudah selesai/disetujui agar progres minggu
                // peserta lama tidak kembali terkunci setelah migration dijalankan.
                $table->string('status_review', 30)
                    ->default('disetujui')
                    ->after('status');
            }

            if (! Schema::hasColumn('pengumpulan_tugas', 'catatan_revisi')) {
                $table->text('catatan_revisi')->nullable()->after('status_review');
            }

            if (! Schema::hasColumn('pengumpulan_tugas', 'revisi_ke')) {
                $table->unsignedSmallInteger('revisi_ke')->default(0)->after('catatan_revisi');
            }

            if (! Schema::hasColumn('pengumpulan_tugas', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('revisi_ke');
            }

            if (! Schema::hasColumn('pengumpulan_tugas', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table): void {
            $columns = collect([
                'status_review',
                'catatan_revisi',
                'revisi_ke',
                'reviewed_at',
                'reviewed_by',
            ])->filter(fn (string $column): bool => Schema::hasColumn('pengumpulan_tugas', $column))->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->date('periode_mulai')->nullable()->after('tgl_bayar');
            $table->date('periode_selesai')->nullable()->after('periode_mulai');
            $table->unsignedTinyInteger('jumlah_bulan')->default(1)->after('periode_selesai');
            $table->index(['peserta_id', 'periode_mulai'], 'pembayaran_peserta_periode_index');
        });

        DB::table('pembayaran')
            ->select(['id_pembayaran', 'tgl_bayar', 'created_at'])
            ->orderBy('id_pembayaran')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $source = $row->tgl_bayar ?: $row->created_at;
                    if (! $source) {
                        continue;
                    }

                    $month = Carbon::parse($source);
                    DB::table('pembayaran')
                        ->where('id_pembayaran', $row->id_pembayaran)
                        ->update([
                            'periode_mulai' => $month->copy()->startOfMonth()->toDateString(),
                            'periode_selesai' => $month->copy()->endOfMonth()->toDateString(),
                            'jumlah_bulan' => 1,
                        ]);
                }
            }, 'id_pembayaran');
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropIndex('pembayaran_peserta_periode_index');
            $table->dropColumn(['periode_mulai', 'periode_selesai', 'jumlah_bulan']);
        });
    }
};

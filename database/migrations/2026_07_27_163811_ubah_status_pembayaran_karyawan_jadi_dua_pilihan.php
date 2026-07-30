<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pembayaran_karyawan')->where('status', 'lunas')->update(['status' => 'terbayar']);
        DB::table('pembayaran_karyawan')->whereIn('status', ['menunggu', 'ditolak'])->update(['status' => 'belum_terbayar']);

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE pembayaran_karyawan MODIFY status VARCHAR(20) NOT NULL DEFAULT 'belum_terbayar'");
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan otomatis agar data tidak terpotong.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('aturan_perusahaan')
            ->where('status', '!=', 'aktif')
            ->update([
                'status' => 'aktif',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // 
    }
};

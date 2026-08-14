<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->unique()->after('nomor_sertifikat');
            $table->string('data_hash', 64)->nullable()->after('token');
            $table->text('signature')->nullable()->after('data_hash');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropColumn(['token', 'data_hash', 'signature']);
        });
    }
};

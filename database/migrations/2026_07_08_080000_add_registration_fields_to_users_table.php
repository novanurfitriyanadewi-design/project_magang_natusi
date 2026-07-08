<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'university')) {
                $table->string('university')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('university');
            }
            if (!Schema::hasColumn('users', 'major')) {
                $table->string('major')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('major');
            }
            if (!Schema::hasColumn('users', 'description')) {
                $table->text('description')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'major')) {
                $table->dropColumn('major');
            }
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('users', 'university')) {
                $table->dropColumn('university');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->boolean('reading_done')->default(false)->after('hands_on_xp_awarded');
        });
    }

    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->dropColumn('reading_done');
        });
    }
};

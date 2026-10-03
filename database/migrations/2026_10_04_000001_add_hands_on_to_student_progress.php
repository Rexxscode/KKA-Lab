<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->unsignedInteger('hands_on_count')->default(0)->after('xp_earned');
            $table->boolean('hands_on_xp_awarded')->default(false)->after('hands_on_count');
        });
    }

    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->dropColumn(['hands_on_count', 'hands_on_xp_awarded']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_models', function (Blueprint $table) {
            $table->json('weights')->nullable()->after('dataset');
        });
        Schema::table('global_models', function (Blueprint $table) {
            $table->json('weights')->nullable()->after('dataset');
        });
    }

    public function down(): void
    {
        Schema::table('global_models', function (Blueprint $table) {
            $table->dropColumn('weights');
        });
        Schema::table('student_models', function (Blueprint $table) {
            $table->dropColumn('weights');
        });
    }
};

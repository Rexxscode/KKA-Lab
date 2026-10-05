<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classroom_module', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable()->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('classroom_module', function (Blueprint $table) {
            $table->dropColumn('due_at');
        });
    }
};

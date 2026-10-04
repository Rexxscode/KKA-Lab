<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->after('module_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('classroom_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['classroom_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_module');
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
        });
        Schema::table('modules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
        });
    }
};

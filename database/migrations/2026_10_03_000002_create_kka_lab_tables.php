<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 6)->unique();
            $table->timestamps();
        });

        Schema::create('classroom_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['classroom_id', 'student_id']);
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('order')->default(0);
            $table->text('description')->nullable();
            $table->unsignedInteger('xp_reward')->default(100);
            $table->timestamps();
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options');
            $table->unsignedTinyInteger('correct_index');
            $table->unsignedInteger('points')->default(20);
            $table->timestamps();
        });

        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('quiz_score')->default(0);
            $table->unsignedInteger('xp_earned')->default(0);
            $table->timestamps();
            $table->unique(['student_id', 'module_id']);
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon_path')->nullable();
            $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['badge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('student_progress');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('classroom_user');
        Schema::dropIfExists('classrooms');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->json('answers');
            $table->boolean('passed')->default(false);
            $table->timestamps();
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_exam_attempts');
    }
};

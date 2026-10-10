<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Batch 4 hardening: satu badge per modul + index komposit untuk
     * query panas (bank soal, riwayat kuis/AI, antrean moderasi, filter peran).
     */
    public function up(): void
    {
        // Dedup defensif: seeder memakai firstOrCreate(module_id) sehingga
        // seharusnya 1 baris per modul — hapus duplikat bila pernah terjadi.
        $dups = DB::table('badges')
            ->select('module_id', DB::raw('MIN(id) as keep_id'))
            ->whereNotNull('module_id')
            ->groupBy('module_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        foreach ($dups as $d) {
            DB::table('badges')
                ->where('module_id', $d->module_id)
                ->where('id', '<>', $d->keep_id)
                ->delete();
        }

        Schema::table('badges', function (Blueprint $table) {
            $table->unique('module_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->index(['module_id', 'teacher_id']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['student_id', 'module_id']);
        });

        Schema::table('final_exam_attempts', function (Blueprint $table) {
            $table->index(['student_id', 'created_at']);
        });

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('student_models', function (Blueprint $table) {
            $table->index(['student_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status']);
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('global_models', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('global_models', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'status']);
        });

        Schema::table('student_models', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'status']);
        });

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('final_exam_attempts', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'created_at']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'module_id']);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['module_id', 'teacher_id']);
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropUnique(['module_id']);
        });
    }
};

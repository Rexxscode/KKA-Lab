<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('origin_id')->nullable()->after('teacher_id')->constrained('quizzes')->nullOnDelete();
        });

        // Backfill: padankan fork guru dengan soal default berteks sama (modul sama, id terkecil).
        foreach (DB::table('quizzes')->whereNotNull('teacher_id')->get(['id', 'module_id', 'question']) as $fork) {
            $asal = DB::table('quizzes')
                ->where('module_id', $fork->module_id)
                ->whereNull('teacher_id')
                ->where('question', $fork->question)
                ->orderBy('id')->first(['id']);
            if ($asal) {
                DB::table('quizzes')->where('id', $fork->id)->update(['origin_id' => $asal->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('origin_id');
        });
    }
};

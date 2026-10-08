<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->string('status', 20)->default('available')->after('quiz_score');
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('last_accessed_at')->nullable()->after('started_at');
            $table->timestamp('material_completed_at')->nullable()->after('last_accessed_at');
            $table->timestamp('completed_at')->nullable()->after('material_completed_at');
            $table->unsignedSmallInteger('best_score')->nullable()->after('completed_at');
            $table->unsignedInteger('attempts')->default(0)->after('best_score');
            $table->index(['student_id', 'status']);
        });

        // Backfill aman untuk data lama (tidak membuat aktivitas palsu
        // selain fallback started_at = created_at).
        DB::table('student_progress')->whereNull('started_at')->update([
            'started_at' => DB::raw('created_at'),
        ]);

        // Agregasi quiz_attempts yang efisien: satu query GROUP BY.
        $agregat = DB::table('quiz_attempts')
            ->selectRaw('student_id, module_id, COUNT(*) AS jml, MAX(score) AS maks')
            ->groupBy('student_id', 'module_id')
            ->get();

        foreach ($agregat as $row) {
            DB::table('student_progress')
                ->where('student_id', $row->student_id)
                ->where('module_id', $row->module_id)
                ->update([
                    'attempts' => (int) $row->jml,
                    'best_score' => $row->maks !== null ? (int) $row->maks : null,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'status']);
            $table->dropColumn(['status', 'started_at', 'last_accessed_at', 'material_completed_at', 'completed_at', 'best_score', 'attempts']);
        });
    }
};

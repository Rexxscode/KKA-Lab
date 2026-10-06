<?php

namespace App\Services;

use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya penulis tracking progres modul siswa.
 * Semua angka (XP, skor, status) dihitung di backend dari DB,
 * tidak pernah dipercaya dari request frontend.
 */
class StudentProgressTracker
{
    /**
     * Siswa membuka modul: catat started_at sekali, selalu update last_accessed_at.
     */
    public function touchOpen(User $user, Module $module): StudentProgress
    {
        $progress = StudentProgress::firstOrCreate(
            ['student_id' => $user->id, 'module_id' => $module->id],
            ['quiz_score' => 0, 'xp_earned' => 0, 'is_completed' => false, 'status' => StudentProgress::STATUS_IN_PROGRESS, 'started_at' => now()]
        );

        $update = ['last_accessed_at' => now()];
        if (! $progress->started_at) {
            $update['started_at'] = $progress->created_at ?? now();
        }
        if ($progress->status === StudentProgress::STATUS_AVAILABLE) {
            $update['status'] = StudentProgress::STATUS_IN_PROGRESS;
        }
        $progress->update($update);

        return $progress->refresh();
    }

    /**
     * Siswa menekan tombol selesai membaca materi.
     */
    public function markReading(User $user, Module $module): StudentProgress
    {
        $progress = $this->touchOpen($user, $module);
        $update = ['reading_done' => true, 'last_accessed_at' => now()];
        if (! $progress->material_completed_at) {
            $update['material_completed_at'] = now();
        }
        if ($progress->status === StudentProgress::STATUS_AVAILABLE) {
            $update['status'] = StudentProgress::STATUS_IN_PROGRESS;
        }
        $progress->update($update);

        return $progress->refresh();
    }

    /**
     * Catat hasil kuis dari backend (dipanggil setelah nilai dihitung server-side).
     * Aman dari balapan via transaction + lock baris.
     */
    public function recordQuiz(User $user, Module $module, int $score, int $xp, bool $passed): StudentProgress
    {
        return DB::transaction(function () use ($user, $module, $score, $xp, $passed) {
            $progress = StudentProgress::where('student_id', $user->id)
                ->where('module_id', $module->id)
                ->lockForUpdate()
                ->first();

            if (! $progress) {
                $progress = StudentProgress::create([
                    'student_id' => $user->id,
                    'module_id' => $module->id,
                    'quiz_score' => 0,
                    'xp_earned' => 0,
                    'is_completed' => false,
                    'status' => StudentProgress::STATUS_IN_PROGRESS,
                    'started_at' => now(),
                ]);
            }

            $attempts = ((int) $progress->attempts) + 1;
            $best = $progress->best_score !== null ? max((int) $progress->best_score, $score) : $score;

            $update = [
                'quiz_score' => $score,
                'xp_earned' => $xp,
                'attempts' => $attempts,
                'best_score' => $best,
                'last_accessed_at' => now(),
            ];
            if (! $progress->started_at) {
                $update['started_at'] = $progress->created_at ?? now();
            }
            if ($passed) {
                $update['is_completed'] = true;
                $update['status'] = StudentProgress::STATUS_PASSED;
                if (! $progress->completed_at) {
                    $update['completed_at'] = now();
                }
            } elseif ($progress->status === StudentProgress::STATUS_AVAILABLE) {
                $update['status'] = StudentProgress::STATUS_IN_PROGRESS;
            }

            $progress->update($update);

            return $progress->refresh();
        });
    }
}

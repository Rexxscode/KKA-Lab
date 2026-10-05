<?php

namespace App\Ai\Tools;

use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Capaian belajar milik siswa yang sedang chat: total XP, badge,
 * dan progres per modul (selesai/belum, nilai kuis).
 * Hanya miliknya sendiri. SENGAJA tanpa email, kode kelas,
 * jawaban kuis, maupun data siswa lain.
 */
class LearningProgressTool implements Tool
{
    public function __construct(private User $user) {}

    public function name(): string
    {
        return 'get_learning_progress';
    }

    public function description(): Stringable|string
    {
        return 'Lihat capaian belajar siswa yang sedang chat: total XP, badge, dan progres tiap modul beserta nilai kuisnya. Hanya data miliknya sendiri.';
    }

    public function handle(Request $request): Stringable|string
    {
        $user = $this->user;
        $baris = [];

        $baris[] = 'Total XP: '.$user->totalXp().'.';
        $badge = $user->badges()->pluck('name')->implode(', ');
        $baris[] = 'Badge: '.($badge ?: 'belum ada').'.';

        $progres = $user->progress()->with('module')->get()->keyBy('module_id');
        foreach (Module::orderBy('order')->get() as $modul) {
            $p = $progres->get($modul->id);
            $status = ($p && $p->is_completed) ? 'selesai' : 'belum selesai';
            $detail = 'Modul "'.$modul->title.'": '.$status;
            if ($p && $p->quiz_score !== null) {
                $terbaik = QuizAttempt::where('student_id', $user->id)
                    ->where('module_id', $modul->id)
                    ->max('score');
                $detail .= ', nilai kuis terakhir '.$p->quiz_score;
                if ($terbaik !== null) {
                    $detail .= ' (terbaik '.$terbaik.')';
                }
            }
            $baris[] = $detail.'.';
        }

        return implode(' ', $baris);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}

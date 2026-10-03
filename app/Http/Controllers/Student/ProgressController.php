<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Module;
use App\Models\StudentProgress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function submitQuiz(Request $request, string $slug)
    {
        $module = Module::where('slug', $slug)->with('quizzes')->firstOrFail();
        $answers = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|integer|min:0|max:10',
        ])['answers'];

        $quizzes = $module->quizzes;
        $totalPoints = $quizzes->sum('points');
        $earned = 0;

        foreach ($quizzes as $i => $quiz) {
            if (isset($answers[$i]) && (int) $answers[$i] === (int) $quiz->correct_index) {
                $earned += $quiz->points;
            }
        }

        $score = $totalPoints > 0 ? (int) round($earned / $totalPoints * 100) : 0;
        $passed = $score >= StudentProgress::PASS_SCORE;
        $xp = $passed ? $module->xp_reward + (int) round($score / 10) : (int) round($score / 10);

        $progress = StudentProgress::updateOrCreate(
            ['student_id' => $request->user()->id, 'module_id' => $module->id],
            ['quiz_score' => $score, 'xp_earned' => $xp, 'is_completed' => $passed]
        );

        if ($passed && $module->badge) {
            $request->user()->badges()->syncWithoutDetaching([$module->badge->id]);
            return back()->with('toast', ['type' => 'success', 'message' => "Hebat! Kamu lulus {$module->title} dengan skor {$score}. +{$xp} XP & badge didapat!"]);
        }

        if ($passed) {
            Badge::firstOrCreate(['module_id' => $module->id], ['name' => 'Badge ' . $module->title]);
            $request->user()->badges()->syncWithoutDetaching([Badge::where('module_id', $module->id)->first()->id]);
            return back()->with('toast', ['type' => 'success', 'message' => "Hebat! Kamu lulus dengan skor {$score}. +{$xp} XP!"]);
        }

        return back()->with('toast', ['type' => 'error', 'message' => "Skor {$score}, belum lulus (minimal 80). Yuk coba lagi!"]);
    }

    /**
     * Dipanggil OTOMATIS oleh JS setiap Uji kamera meyakinkan (bukan tombol manual).
     * Anti-farm: XP +20 hanya diberikan SEKALI saat hitungan mencapai 2.
     */
    public function completeHandsOn(Request $request, string $slug)
    {
        $data = $request->validate([
            'confidence' => 'nullable|integer|min:0|max:100',
        ]);

        $module = Module::where('slug', $slug)->firstOrFail();

        $progress = StudentProgress::firstOrCreate(
            ['student_id' => $request->user()->id, 'module_id' => $module->id],
            ['quiz_score' => 0, 'xp_earned' => 0, 'is_completed' => false]
        );

        // Hanya Uji meyakinkan (keyakinan >= 50) yang dihitung.
        if (($data['confidence'] ?? 0) >= 50) {
            $progress->increment('hands_on_count');
            $progress->refresh();
        }

        $message = 'Uji ke-' . $progress->hands_on_count . ' tercatat. Butuh 2 Uji meyakinkan untuk +20 XP!';

        if ($progress->hands_on_count >= 2 && ! $progress->hands_on_xp_awarded) {
            $progress->update([
                'xp_earned' => $progress->xp_earned + 20,
                'hands_on_xp_awarded' => true,
            ]);
            $message = 'Hebat! 2 Uji meyakinkan tercapai — +20 XP otomatis masuk!';
        } elseif ($progress->hands_on_xp_awarded) {
            $message = 'XP hands-on modul ini sudah diklaim penuh (+20 XP). Lanjut ke kuis untuk XP tambahan!';
        }

        return response()->json([
            'count' => $progress->hands_on_count,
            'awarded' => (bool) $progress->hands_on_xp_awarded,
            'xp_earned' => $progress->xp_earned,
            'message' => $message,
        ]);
    }
}

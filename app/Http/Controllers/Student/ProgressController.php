<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function submitQuiz(Request $request, string $slug, StudentModuleService $modul)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item, 404);

        if ($item['terkunci']) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan modul sebelumnya dulu sesuai urutan!']);
        }

        // Pratinjau admin: boleh lihat soal, tapi nilai tidak disimpan.
        if ($request->user()->isAdmin()) {
            return back()->with('toast', ['type' => 'info', 'message' => 'Mode pratinjau admin: nilai tidak disimpan.']);
        }

        $progress = $item['progress'];

        // Kuis hanya sah jika bahan bacaan sudah ditandai selesai.
        if (! $progress || ! $progress->reading_done) {
            return redirect()
                ->route('siswa.bahan.show', $module->slug)
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan bahan bacaan dulu untuk mengerjakan kuis!']);
        }

        $answers = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|integer|min:0|max:4',
        ])['answers'];

        // Nilai memakai paket acak yang disimpan saat halaman kuis dibuka,
        // sehingga urutan soal & opsi yang diacak tetap konsisten.
        $paket = session('kuis_paket_' . $module->id);
        if (empty($paket)) {
            $quizzes = $modul->soalEfektif($module, $modul->guruKelasAktif($request->user()));
            $paket = $modul->paketAcak($quizzes);
        }
        $tampil = $modul->uraikanPaket($paket);
        abort_if(empty($tampil), 422, 'Bank soal modul ini kosong.');

        $totalPoints = collect($tampil)->sum(fn ($t) => $t['quiz']->points);
        $earned = 0;

        // Rincian per soal untuk pembahasan (jawaban dipertahankan via withInput).
        $detail = [];
        foreach ($tampil as $i => $t) {
            $chosen = isset($answers[$i]) ? (int) $answers[$i] : null;
            $correct = (int) $t['correct'];
            $ok = $chosen !== null && $chosen === $correct;
            if ($ok) {
                $earned += $t['quiz']->points;
            }
            $detail[] = [
                'quiz_id' => $t['quiz']->id,
                'question' => $t['quiz']->question,
                'options' => $t['options'],
                'explanation' => $t['quiz']->explanation,
                'chosen' => $chosen,
                'correct' => $correct,
                'ok' => $ok,
            ];
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
            return redirect()->route('siswa.dashboard')->with('toast', ['type' => 'success', 'message' => "Hebat! Kamu lulus {$module->title} dengan skor {$score}. +{$xp} XP & badge didapat!"]);
        }

        if ($passed) {
            Badge::firstOrCreate(['module_id' => $module->id], ['name' => 'Badge ' . $module->title]);
            $request->user()->badges()->syncWithoutDetaching([Badge::where('module_id', $module->id)->first()->id]);
            return redirect()->route('siswa.dashboard')->with('toast', ['type' => 'success', 'message' => "Hebat! Kamu lulus dengan skor {$score}. +{$xp} XP!"]);
        }

        // Gagal: cabut badge modul bila pernah didapat (mis. lulus lalu mengulang dan gagal),
        // agar badge selalu mencerminkan status lulus saat ini.
        if ($module->badge) {
            $request->user()->badges()->detach($module->badge->id);
        }

        return back()->withInput()->with('quiz_result', ['score' => $score, 'passed' => false, 'detail' => $detail])->with('toast', ['type' => 'error', 'message' => "Skor {$score}, belum lulus (minimal 80). Lihat pembahasan di bawah, lalu coba lagi!"]);
    }

    /**
     * Dipanggil OTOMATIS oleh JS setiap Uji kamera meyakinkan (bukan tombol manual).
     * Anti-farm: XP +20 hanya diberikan SEKALI saat hitungan mencapai 2.
     */
    public function completeHandsOn(Request $request, string $slug, StudentModuleService $modul)
    {
        $data = $request->validate([
            'confidence' => 'nullable|integer|min:0|max:100',
        ]);

        $module = Module::where('slug', $slug)->firstOrFail();

        // Guard sama seperti kuis: modul harus milik siswa & tidak terkunci.
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item, 404);
        if ($item['terkunci']) {
            return response()->json(['message' => 'Selesaikan modul sebelumnya dulu sesuai urutan!'], 403);
        }

        // Pratinjau admin: tidak ada yang dicatat.
        if ($request->user()->isAdmin()) {
            return response()->json([
                'count' => 0,
                'awarded' => false,
                'xp_earned' => 0,
                'message' => 'Mode pratinjau admin: Uji tidak dicatat.',
            ]);
        }

        $progress = StudentProgress::firstOrCreate(
            ['student_id' => $request->user()->id, 'module_id' => $module->id],
            ['quiz_score' => 0, 'xp_earned' => 0, 'is_completed' => false]
        );

        // Hanya Uji meyakinkan (keyakinan >= 50) yang dihitung.
        if (($data['confidence'] ?? 0) >= 50) {
            // Anti-farm: jeda minimal 10 detik antar Uji yang dihitung.
            if ($progress->hands_on_last_at && $progress->hands_on_last_at->gt(now()->subSeconds(10))) {
                return response()->json([
                    'count' => $progress->hands_on_count,
                    'awarded' => (bool) $progress->hands_on_xp_awarded,
                    'xp_earned' => $progress->xp_earned,
                    'message' => 'Terlalu cepat! Tunggu sebentar sebelum Uji berikutnya.',
                ], 429);
            }
            $progress->increment('hands_on_count');
            $progress->update(['hands_on_last_at' => now()]);
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

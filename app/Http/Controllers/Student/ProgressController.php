<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\StudentProgress;
use App\Services\CertificateService;
use App\Services\StudentModuleService;
use App\Services\StudentProgressTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    /**
     * Batas waktu pengerjaan satu paket kuis (server-side).
     */
    public const DURASI_KUIS_MENIT = 30;

    /**
     * Bonus XP praktik hands-on (diberikan sekali per modul).
     */
    public const HANDS_ON_XP_BONUS = 20;

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

        // Bank guru wajib 25–30 soal (cek ulang di POST agar tak diakali via request manual).
        $guruId = $modul->guruKelasAktif($request->user());
        if ($modul->bankGuruKurang($module, $guruId)) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Bank soal modul ini belum lengkap. Minta gurumu melengkapi dulu!']);
        }

        // Nilai memakai paket acak yang disimpan saat halaman kuis dibuka,
        // sehingga urutan soal & opsi yang diacak tetap konsisten.
        $kunciPaket = 'kuis_paket_' . $module->id;
        $kunciMulai = 'kuis_mulai_' . $module->id;
        $paket = session($kunciPaket);
        if (empty($paket)) {
            $quizzes = $modul->soalEfektif($module, $guruId);
            $paket = $modul->paketAcak($quizzes, $modul->jumlahTampilLatihan($module, $guruId));
            session([$kunciPaket => $paket, $kunciMulai => now()->toIso8601String()]);
        } elseif (! session($kunciMulai) || abs(now()->diffInMinutes(Carbon::parse(session($kunciMulai)))) > self::DURASI_KUIS_MENIT) {
            // Waktu habis: paket lama hangus, buat percobaan baru.
            session()->forget([$kunciPaket, $kunciMulai]);

            return redirect()
                ->route('siswa.kuis.show', $module->slug)
                ->with('toast', ['type' => 'error', 'message' => 'Waktu ' . self::DURASI_KUIS_MENIT . ' menit habis! Paket baru dibuat — coba lagi.']);
        }
        $tampil = $modul->uraikanPaket($paket);
        abort_if(empty($tampil), 422, 'Bank soal modul ini kosong.');

        // Jawaban harus lengkap sejumlah soal tampil (tanpa kurang/lebih).
        // Validasi manual agar gagalnya jadi toast ramah + withInput,
        // bukan halaman error (kasus auto-submit saat waktu habis).
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array|size:' . count($tampil),
            'answers.*' => 'required|integer|min:0|max:4',
        ]);
        if ($validator->fails()) {
            $terjawab = is_array($request->input('answers')) ? count(array_filter($request->input('answers'), fn ($a) => $a !== null && $a !== '')) : 0;

            return back()->withInput()->withErrors($validator)->with('toast', [
                'type' => 'error',
                'message' => "Masih ada soal belum dijawab ({$terjawab}/" . count($tampil) . '). Lengkapi semua soal dulu baru kumpulkan!',
            ]);
        }
        $answers = $validator->validated()['answers'];

        // Kunci anti double-submit: kiriman ganda bersamaan diproses sekali.
        $lock = Cache::lock("kuis:{$request->user()->id}:{$module->id}", 10);
        if (! $lock->get()) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Jawabanmu masih diproses, tunggu sebentar!']);
        }

        try {
            return $this->nilai($request, $module, $tampil, $answers, $kunciPaket, $kunciMulai);
        } finally {
            $lock->release();
        }
    }

    private function nilai(Request $request, Module $module, array $tampil, array $answers, string $kunciPaket, string $kunciMulai)
    {

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

        // Bonus hands-on yang sudah diklaim tidak boleh hilang tertimpa
        // nilai kuis: baca ulang dari DB (bukan model lama di memori).
        $bonusHandsOn = (int) (StudentProgress::where('student_id', $request->user()->id)
            ->where('module_id', $module->id)
            ->value('hands_on_xp_awarded') ? self::HANDS_ON_XP_BONUS : 0);
        $xp = $bonusHandsOn + ($passed ? $module->xp_reward + (int) round($score / 10) : (int) round($score / 10));

        // Jejak tiap percobaan (skor terakhir boleh ditimpa, histori tidak).
        QuizAttempt::create([
            'student_id' => $request->user()->id,
            'module_id' => $module->id,
            'score' => $score,
            'answers' => array_values($answers),
            'passed' => $passed,
        ]);

        // Track record terpusat: attempts/best_score/status/completed_at dihitung backend.
        $tracker = app(StudentProgressTracker::class);
        $progress = $tracker->recordQuiz($request->user(), $module, $score, $xp, $passed);

        // Sertifikat otomatis saat modul terakhir lulus (hanya 6 default, transaction, idempoten).
        $sertifikatBaru = null;
        if ($passed) {
            try {
                $sertifikatBaru = app(CertificateService::class)->issueIfEligible($request->user());
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $pesanSertifikat = $sertifikatBaru ? ' Selamat! Kamu telah menyelesaikan seluruh modul KKALAB dan mendapatkan sertifikat.' : '';

        if ($passed && $module->badge) {
            $request->user()->badges()->syncWithoutDetaching([$module->badge->id]);
            session()->forget([$kunciPaket, $kunciMulai]);

            return redirect()->route('siswa.dashboard')->with('toast', ['type' => 'success', 'message' => "Hebat! Kamu lulus {$module->title} dengan skor {$score}. +{$xp} XP & badge didapat!{$pesanSertifikat}"]);
        }

        if ($passed) {
            Badge::firstOrCreate(['module_id' => $module->id], ['name' => 'Badge ' . $module->title]);
            $request->user()->badges()->syncWithoutDetaching([Badge::where('module_id', $module->id)->first()->id]);
            session()->forget([$kunciPaket, $kunciMulai]);

            return redirect()->route('siswa.dashboard')->with('toast', ['type' => 'success', 'message' => "Hebat! Kamu lulus dengan skor {$score}. +{$xp} XP!{$pesanSertifikat}"]);
        }

        // Gagal: cabut badge modul bila pernah didapat (mis. lulus lalu mengulang dan gagal),
        // agar badge selalu mencerminkan status lulus saat ini.
        if ($module->badge) {
            $request->user()->badges()->detach($module->badge->id);
        }

        return back()->withInput()->with('quiz_result', ['score' => $score, 'passed' => false, 'detail' => $detail])->with('toast', ['type' => 'error', 'message' => "Skor {$score}, belum lulus (minimal 80). Lihat pembahasan di bawah, lalu coba lagi!"]);
    }
}

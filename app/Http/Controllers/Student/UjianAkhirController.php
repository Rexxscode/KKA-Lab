<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalExamAttempt;
use App\Services\CertificateService;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class UjianAkhirController extends Controller
{
    /**
     * Halaman info ujian akhir. Terbuka hanya bila 6 modul default sudah lulus.
     */
    public function index(Request $request, CertificateService $sertifikat)
    {
        $user = $request->user();
        if (! $sertifikat->modulLulus($user)) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan dulu 6 modul pembelajaran (nilai ≥80) untuk membuka ujian akhir!']);
        }

        $riwayat = FinalExamAttempt::where('student_id', $user->id)->latest()->take(5)->get();
        $lulus = $sertifikat->finalExamPassed($user);
        $terbaik = $riwayat->max('score');

        return view('siswa.ujian-index', compact('riwayat', 'lulus', 'terbaik'));
    }

    /**
     * Lembar ujian: 40 soal acak dari bank ke-6 modul default.
     */
    public function show(Request $request, StudentModuleService $modul, CertificateService $sertifikat)
    {
        $user = $request->user();
        if (! $sertifikat->modulLulus($user)) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan dulu 6 modul pembelajaran (nilai ≥80) untuk membuka ujian akhir!']);
        }

        if ($request->user()->isAdmin()) {
            return back()->with('toast', ['type' => 'info', 'message' => 'Mode pratinjau admin: nilai ujian tidak disimpan.']);
        }

        $kunciPaket = 'ujian_paket';
        $kunciMulai = 'ujian_mulai';
        if (session()->has('ujian_result') && session()->has($kunciPaket)) {
            $paket = session($kunciPaket);
            session([$kunciMulai => now()->toIso8601String()]);
        } elseif (
            session()->has($kunciPaket) && session()->has($kunciMulai)
            && abs(now()->diffInMinutes(Carbon::parse(session($kunciMulai)))) < StudentModuleService::UJIAN_AKHIR_MENIT
        ) {
            $paket = session($kunciPaket);
        } else {
            $paket = $modul->paketAcak($modul->soalUjianAkhir(), StudentModuleService::UJIAN_AKHIR_JUMLAH);
            session([$kunciPaket => $paket, $kunciMulai => now()->toIso8601String()]);
        }
        $tampil = $modul->uraikanPaket($paket);
        abort_if(empty($tampil), 422, 'Bank soal ujian akhir kosong.');
        $sisaDetik = (int) max(0, StudentModuleService::UJIAN_AKHIR_MENIT * 60 - abs(now()->diffInSeconds(Carbon::parse(session($kunciMulai)))));

        return view('siswa.ujian-show', compact('tampil', 'sisaDetik'));
    }

    public function submit(Request $request, StudentModuleService $modul, CertificateService $sertifikat)
    {
        $user = $request->user();
        if (! $sertifikat->modulLulus($user)) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan dulu 6 modul pembelajaran untuk mengikuti ujian akhir!']);
        }

        if ($user->isAdmin()) {
            return back()->with('toast', ['type' => 'info', 'message' => 'Mode pratinjau admin: nilai ujian tidak disimpan.']);
        }

        $kunciPaket = 'ujian_paket';
        $kunciMulai = 'ujian_mulai';
        $paket = session($kunciPaket);
        if (empty($paket)) {
            $paket = $modul->paketAcak($modul->soalUjianAkhir(), StudentModuleService::UJIAN_AKHIR_JUMLAH);
            session([$kunciPaket => $paket, $kunciMulai => now()->toIso8601String()]);
        } elseif (! session($kunciMulai) || abs(now()->diffInMinutes(Carbon::parse(session($kunciMulai)))) > StudentModuleService::UJIAN_AKHIR_MENIT) {
            session()->forget([$kunciPaket, $kunciMulai]);

            return redirect()
                ->route('siswa.ujian.show')
                ->with('toast', ['type' => 'error', 'message' => 'Waktu ' . StudentModuleService::UJIAN_AKHIR_MENIT . ' menit habis! Paket baru dibuat — coba lagi.']);
        }
        $tampil = $modul->uraikanPaket($paket);
        abort_if(empty($tampil), 422, 'Bank soal ujian akhir kosong.');

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

        $lock = Cache::lock("ujian:{$user->id}", 10);
        if (! $lock->get()) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Jawabanmu masih diproses, tunggu sebentar!']);
        }

        try {
            return $this->nilai($request, $tampil, $answers, $kunciPaket, $kunciMulai, $sertifikat);
        } finally {
            $lock->release();
        }
    }

    private function nilai(Request $request, array $tampil, array $answers, string $kunciPaket, string $kunciMulai, CertificateService $sertifikat)
    {
        $totalPoints = collect($tampil)->sum(fn ($t) => $t['quiz']->points);
        $earned = 0;
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
        $passed = $score >= StudentModuleService::UJIAN_AKHIR_PASS;

        FinalExamAttempt::create([
            'student_id' => $request->user()->id,
            'score' => $score,
            'answers' => array_values($answers),
            'passed' => $passed,
        ]);

        if ($passed) {
            $certificate = null;
            try {
                $certificate = $sertifikat->issueIfEligible($request->user());
            } catch (\Throwable $e) {
                report($e);
            }
            session()->forget([$kunciPaket, $kunciMulai]);
            $pesan = "Luar biasa! Kamu lulus ujian akhir dengan skor {$score}.";
            $pesan .= $certificate
                ? ' Selamat! Kamu telah menyelesaikan seluruh modul KobotAI Lab dan mendapatkan sertifikat.'
                : ' Sertifikatmu segera terbit di halaman sertifikat.';

            return redirect()->route('siswa.dashboard')->with('toast', ['type' => 'success', 'message' => $pesan]);
        }

        return back()->withInput()->with('ujian_result', ['score' => $score, 'passed' => false, 'detail' => $detail])->with('toast', ['type' => 'error', 'message' => "Skor {$score}, belum lulus (minimal 80). Pelajari pembahasan di bawah, lalu coba lagi!"]);
    }
}

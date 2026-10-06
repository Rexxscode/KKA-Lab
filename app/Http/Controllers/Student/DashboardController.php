<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, StudentModuleService $modul, \App\Services\CertificateService $sertifikat)
    {
        $user = $request->user();
        $daftar = $modul->denganStatus($user);
        $totalXp = $user->totalXp();
        $badges = $user->badges()->with('module')->get();
        $classrooms = $user->classrooms()->with('teacher')->get();
        $kelasAktif = $modul->kelasAktif($user);
        // Banner permanen: hasil pengerjaan terakhir (tidak hilang saat pindah halaman).
        $terakhir = RaportController::terakhir($user->id);
        // 5 percobaan kuis terakhir.
        $riwayat = \App\Models\QuizAttempt::with('module')->where('student_id', $user->id)->latest()->take(5)->get();

        // Report belajar: SATU sumber data dari RaportController::ringkasan agar
        // angka dashboard selalu sama dengan halaman raport.
        $rapor = (new RaportController)->ringkasan($user, $modul);
        $selesai = collect($daftar)->where(fn ($d) => $d['progress'] && $d['progress']->is_completed)->count();
        $totalModul = count($daftar);
        $persen = $totalModul ? (int) round($selesai / $totalModul * 100) : 0;

        // Lanjutkan belajar: modul in_progress pertama, else modul available berikutnya.
        $lanjut = collect($daftar)->first(fn ($d) => ! $d['terkunci'] && $d['progress'] && ! $d['progress']->is_completed && ($d['progress']->reading_done || $d['progress']->quiz_score > 0))
            ?? collect($daftar)->first(fn ($d) => ! $d['terkunci'] && (! $d['progress'] || ! $d['progress']->is_completed));

        $nilaiList = collect($daftar)->map(fn ($d) => $d['progress'] ? (int) $d['progress']->quiz_score : 0)->filter(fn ($n) => $n > 0);
        $rataNilai = $nilaiList->count() ? (int) round($nilaiList->avg()) : 0;
        $nilaiTerbaik = $nilaiList->count() ? (int) $nilaiList->max() : 0;
        $totalKuis = \App\Models\QuizAttempt::where('student_id', $user->id)->count();

        $sertifikatAda = \App\Models\Certificate::where('user_id', $user->id)->exists();
        $sertifikatEligible = $sertifikat->isEligible($user);

        return view('siswa.dashboard', compact('daftar', 'totalXp', 'badges', 'classrooms', 'kelasAktif', 'terakhir', 'riwayat', 'rapor', 'selesai', 'totalModul', 'persen', 'lanjut', 'rataNilai', 'nilaiTerbaik', 'totalKuis', 'sertifikatAda', 'sertifikatEligible'));
    }

    /**
     * Papan peringkat kelas aktif berdasar total XP.
     */
    public function leaderboard(Request $request, StudentModuleService $modul)
    {
        $user = $request->user();
        $kelas = $modul->kelasAktif($user);
        $papan = $kelas
            ? $kelas->students()->withSum('progress as xp', 'xp_earned')->orderByDesc('xp')->take(20)->get()
            : collect();

        // Peringkat user di kelasnya (di luar 20 besar pun tetap ketahuan).
        $peringkatSaya = null;
        if ($kelas) {
            $urutan = $kelas->students()->withSum('progress as xp', 'xp_earned')->orderByDesc('xp')->pluck('users.id');
            $pos = $urutan->search($user->id);
            $peringkatSaya = $pos === false ? null : $pos + 1;
        }

        return view('siswa.papan-peringkat', compact('kelas', 'papan', 'peringkatSaya'));
    }
}

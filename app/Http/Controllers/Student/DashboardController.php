<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, StudentModuleService $modul)
    {
        $user = $request->user();
        $daftar = $modul->denganStatus($user);
        $totalXp = $user->totalXp();
        $badges = $user->badges()->with('module')->get();
        $classrooms = $user->classrooms()->with('teacher')->get();
        $kelasAktif = $modul->kelasAktif($user);
        // Banner permanen: hasil pengerjaan terakhir (tidak hilang saat pindah halaman).
        $terakhir = RaportController::terakhir($user->id);

        return view('siswa.dashboard', compact('daftar', 'totalXp', 'badges', 'classrooms', 'kelasAktif', 'terakhir'));
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

        return view('siswa.papan-peringkat', compact('kelas', 'papan'));
    }
}

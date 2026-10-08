<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\ProgressController;
use App\Models\Module;
use App\Services\StudentModuleService;
use App\Services\StudentProgressTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KuisController extends Controller
{
    public function index(Request $request, StudentModuleService $modul)
    {
        $daftar = $modul->denganStatus($request->user());

        return view('siswa.kuis-index', compact('daftar'));
    }

    public function show(Request $request, string $slug, StudentModuleService $modul, StudentProgressTracker $tracker)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item, 404);

        if ($item['terkunci']) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan modul sebelumnya dulu sesuai urutan!']);
        }

        $progress = $item['progress'];

        if (! $progress || ! $progress->reading_done) {
            return redirect()
                ->route('siswa.bahan.show', $module->slug)
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan bahan bacaan dulu untuk membuka kuis ini!']);
        }

        if (! $request->user()->isAdmin()) {
            $tracker->touchOpen($request->user(), $module);
        }

        // Bank guru wajib 25–30 soal; bila belum lengkap, latihan diblokir
        // sampai gurunya melengkapi (bank default admin tidak diblokir).
        $guruId = $modul->guruKelasAktif($request->user());
        if ($modul->bankGuruKurang($module, $guruId)) {
            $ada = $module->quizzes()->where('teacher_id', $guruId)->count();

            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Bank soal modul ini belum lengkap (' . $ada . '/' . StudentModuleService::BANK_GURU_MIN . '). Minta gurumu melengkapi dulu!']);
        }

        // Paket acak baru tiap percobaan (urutan soal + opsi diacak:
        // 20 soal untuk bank default, 15 soal untuk bank guru). Disimpan
        // di sesi agar penilaian POST memakai susunan yang sama persis.
        // - Sehabis submit gagal (ada quiz_result): pakai paket yang sama
        //   agar pembahasan sejajar, waktu diperpanjang dari sekarang.
        // - Refresh dalam jendela waktu: pakai paket yang sama (anti
        //   farming: refresh tak bisa berburu soal mudah).
        $kunciPaket = 'kuis_paket_' . $module->id;
        $kunciMulai = 'kuis_mulai_' . $module->id;
        if (session()->has('quiz_result') && session()->has($kunciPaket)) {
            $paket = session($kunciPaket);
            session([$kunciMulai => now()->toIso8601String()]);
        } elseif (
            session()->has($kunciPaket) && session()->has($kunciMulai)
            && abs(now()->diffInMinutes(Carbon::parse(session($kunciMulai)))) < ProgressController::DURASI_KUIS_MENIT
        ) {
            $paket = session($kunciPaket);
        } else {
            $quizzes = $modul->soalEfektif($module, $guruId);
            $paket = $modul->paketAcak($quizzes, $modul->jumlahTampilLatihan($module, $guruId));
            session([$kunciPaket => $paket, $kunciMulai => now()->toIso8601String()]);
        }
        $tampil = $modul->uraikanPaket($paket);
        $sisaDetik = (int) max(0, ProgressController::DURASI_KUIS_MENIT * 60 - abs(now()->diffInSeconds(Carbon::parse(session($kunciMulai)))));

        return view('siswa.kuis-show', compact('module', 'progress', 'tampil', 'sisaDetik'));
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    public function index(Request $request, StudentModuleService $modul)
    {
        $daftar = $modul->denganStatus($request->user());

        return view('siswa.kuis-index', compact('daftar'));
    }

    public function show(Request $request, string $slug, StudentModuleService $modul)
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

        // Paket acak baru tiap kunjungan (urutan soal + opsi diacak,
        // maksimal 15 soal bila bank lebih besar). Disimpan di sesi
        // agar penilaian POST memakai susunan yang sama persis.
        // Pengecualian: sehabis submit gagal (ada quiz_result), pakai
        // paket yang sama agar pembahasan sejajar dengan jawaban.
        $kunciPaket = 'kuis_paket_' . $module->id;
        if (session()->has('quiz_result') && session()->has($kunciPaket)) {
            $paket = session($kunciPaket);
        } else {
            $quizzes = $modul->soalEfektif($module, $modul->guruKelasAktif($request->user()));
            $paket = $modul->paketAcak($quizzes);
            session([$kunciPaket => $paket]);
        }
        $tampil = $modul->uraikanPaket($paket);

        return view('siswa.kuis-show', compact('module', 'progress', 'tampil'));
    }
}

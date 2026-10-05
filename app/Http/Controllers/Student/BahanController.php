<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    /**
     * Jeda baca minimal (detik) antara membuka bahan dan boleh tandai
     * selesai — server-side, agar gerbang scroll client-side tidak
     * diakali sekali-klik/curl instan.
     */
    public const BACA_MIN_DETIK = 30;
    public function index(Request $request, StudentModuleService $modul)
    {
        $daftar = $modul->denganStatus($request->user());

        return view('siswa.bahan-index', compact('daftar'));
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

        // Catat kapan bahan pertama dibuka (sesi) untuk gerbang jeda baca.
        $kunciBuka = 'baca_mulai_' . $module->id;
        if (! session()->has($kunciBuka)) {
            session([$kunciBuka => now()->toIso8601String()]);
        }

        return view('siswa.bahan-show', compact('module', 'progress'));
    }

    public function markRead(Request $request, string $slug, StudentModuleService $modul)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item && ! $item['terkunci'], 404);

        // Pratinjau admin: boleh lihat, tapi progres tidak disimpan.
        if ($request->user()->isAdmin()) {
            return redirect()
                ->route('siswa.kuis.show', $module->slug)
                ->with('toast', ['type' => 'info', 'message' => 'Mode pratinjau admin: progres tidak disimpan.']);
        }

        // Tenggat kelas: modul yang belum lulus tak bisa diprogress lagi.
        if ($modul->tenggatLewat($request->user(), $module, $item['progress'])) {
            $tenggat = $modul->tenggatUntuk($request->user(), $module);

            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Tenggat modul ini sudah lewat (' . $tenggat->format('d M Y H:i') . '). Minta gurumu membuka kembali tenggatnya!']);
        }

        $kunciBuka = 'baca_mulai_' . $module->id;
        $dibuka = session($kunciBuka);
        // Carbon 3: diffInSeconds bertanda (negatif untuk waktu lampau),
        // jadi pakai abs() agar jeda selalu bernilai positif.
        if (! $dibuka || abs(now()->diffInSeconds(\Illuminate\Support\Carbon::parse($dibuka))) < self::BACA_MIN_DETIK) {
            return redirect()
                ->route('siswa.bahan.show', $module->slug)
                ->with('toast', ['type' => 'error', 'message' => 'Baca dulu materinya sampai akhir (' . self::BACA_MIN_DETIK . ' detik), tombol selesai aktif setelah itu!']);
        }
        session()->forget($kunciBuka);

        $progress = StudentProgress::firstOrCreate(
            ['student_id' => $request->user()->id, 'module_id' => $module->id],
            ['quiz_score' => 0, 'xp_earned' => 0, 'is_completed' => false]
        );
        $progress->update(['reading_done' => true]);

        return redirect()
            ->route('siswa.kuis.show', $module->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Bacaan selesai! Kuis ' . $module->title . ' terbuka. Selamat mengerjakan — murni pemahamanmu ya!']);
    }
}

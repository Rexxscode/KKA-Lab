<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class BahanController extends Controller
{
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

        return view('siswa.bahan-show', compact('module', 'progress'));
    }

    public function markRead(Request $request, string $slug, StudentModuleService $modul)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item && ! $item['terkunci'], 404);

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

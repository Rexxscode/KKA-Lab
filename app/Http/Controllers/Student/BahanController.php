<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\StudentProgress;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $modules = Module::orderBy('order')->withCount('quizzes')->get();
        $progress = $request->user()->progress()->get()->keyBy('module_id');

        return view('siswa.bahan-index', compact('modules', 'progress'));
    }

    public function show(Request $request, string $slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $progress = $request->user()->progress()->where('module_id', $module->id)->first();

        return view('siswa.bahan-show', compact('module', 'progress'));
    }

    public function markRead(Request $request, string $slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();

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

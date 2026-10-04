<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    public function index(Request $request)
    {
        $modules = Module::orderBy('order')->withCount('quizzes')->get();
        $progress = $request->user()->progress()->get()->keyBy('module_id');

        return view('siswa.kuis-index', compact('modules', 'progress'));
    }

    public function show(Request $request, string $slug)
    {
        $module = Module::where('slug', $slug)->with('quizzes')->firstOrFail();
        $progress = $request->user()->progress()->where('module_id', $module->id)->first();

        if (! $progress || ! $progress->reading_done) {
            return redirect()
                ->route('siswa.bahan.show', $module->slug)
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan bahan bacaan dulu untuk membuka kuis ini!']);
        }

        return view('siswa.kuis-show', compact('module', 'progress'));
    }
}

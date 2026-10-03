<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $module = Module::where('slug', $slug)->with('quizzes')->firstOrFail();
        $progress = $request->user()->progress()->where('module_id', $module->id)->first();

        return view('siswa.modul.show', compact('module', 'progress'));
    }
}

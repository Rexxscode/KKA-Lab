<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $modules = Module::orderBy('order')->get();
        $progress = $user->progress()->with('module')->get()->keyBy('module_id');
        $totalXp = $user->totalXp();
        $badges = $user->badges()->with('module')->get();
        $classrooms = $user->classrooms()->with('teacher')->get();

        return view('siswa.dashboard', compact('modules', 'progress', 'totalXp', 'badges', 'classrooms'));
    }
}

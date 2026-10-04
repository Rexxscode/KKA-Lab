<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\StudentProgress;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Sertifikat hanya dari modul default (milik admin).
        $modules = Module::whereNull('teacher_id')->orderBy('order')->get();
        $progress = $user->progress()->get()->keyBy('module_id');

        $eligible = $modules->count() > 0 && $modules->every(function ($m) use ($progress) {
            return isset($progress[$m->id]) && $progress[$m->id]->is_completed && $progress[$m->id]->quiz_score >= StudentProgress::PASS_SCORE;
        });

        $totalXp = $user->totalXp();
        $code = 'KKA-' . strtoupper(substr(md5($user->id . $user->email), 0, 8));

        return view('siswa.sertifikat', compact('modules', 'progress', 'eligible', 'totalXp', 'code'));
    }
}

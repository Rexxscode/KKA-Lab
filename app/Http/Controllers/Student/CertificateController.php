<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Module;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // Kode acak tersimpan: stabil tiap kunjungan & bisa diverifikasi publik.
        $certificate = $eligible
            ? Certificate::firstOrCreate(
                ['user_id' => $user->id],
                ['code' => $this->kodeUnik(), 'issued_at' => now()]
            )
            : null;

        return view('siswa.sertifikat', compact('modules', 'progress', 'eligible', 'totalXp', 'certificate'));
    }

    private function kodeUnik(): string
    {
        do {
            $code = 'KKA-' . Str::upper(Str::random(8));
        } while (Certificate::where('code', $code)->exists());

        return $code;
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\RaportController;
use App\Models\Classroom;
use App\Models\Feedback;
use App\Models\User;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class StudentReportController extends Controller
{
    public function show(Request $request, Classroom $classroom, User $siswa, StudentModuleService $modul)
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);
        abort_unless($siswa->isSiswa() && $classroom->students()->where('users.id', $siswa->id)->exists(), 404);

        $rapor = (new RaportController)->ringkasan($siswa, $modul);
        $feedbacks = Feedback::with('module')->where('classroom_id', $classroom->id)
            ->where('student_id', $siswa->id)->latest()->get();

        // Track record kronologis dari database (bukan dummy).
        $track = \App\Models\StudentProgress::with('module')->where('student_id', $siswa->id)->orderBy('module_id')->get()->keyBy('module_id');
        $attempts = \App\Models\QuizAttempt::where('student_id', $siswa->id)
            ->selectRaw('module_id, COUNT(*) AS jml, MAX(score) AS maks')->groupBy('module_id')->get()->keyBy('module_id');
        $sertifikat = \App\Models\Certificate::where('user_id', $siswa->id)->first();
        $badges = $siswa->badges()->count();

        return view('guru.siswa-rapor', ['classroom' => $classroom, 'siswa' => $siswa, 'feedbacks' => $feedbacks, 'track' => $track, 'attempts' => $attempts, 'sertifikat' => $sertifikat, 'badgeCount' => $badges] + $rapor);
    }

    public function feedback(Request $request, Classroom $classroom, User $siswa)
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);
        abort_unless($siswa->isSiswa() && $classroom->students()->where('users.id', $siswa->id)->exists(), 404);

        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'body' => 'required|string|max:2000',
        ]);
        abort_unless($classroom->modules()->where('modules.id', $data['module_id'])->exists(), 404);

        Feedback::updateOrCreate(
            ['classroom_id' => $classroom->id, 'student_id' => $siswa->id, 'module_id' => $data['module_id']],
            ['body' => $data['body']]
        );

        return back()->with('toast', ['type' => 'success', 'message' => 'Umpan balik tersimpan, siswa bisa membacanya di rapor!']);
    }
}

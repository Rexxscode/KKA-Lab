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

        return view('guru.siswa-rapor', ['classroom' => $classroom, 'siswa' => $siswa, 'feedbacks' => $feedbacks] + $rapor);
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

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\RaportController;
use App\Models\Classroom;
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

        return view('guru.siswa-rapor', ['classroom' => $classroom, 'siswa' => $siswa] + $rapor);
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $classrooms = $user->isAdmin()
            ? Classroom::with('teacher')->withCount('students')->latest()->get()
            : $user->taughtClassrooms()->withCount('students')->get();
        $modules = Module::orderBy('order')->get();

        return view('guru.dashboard', compact('classrooms', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Classroom::where('code', $code)->exists());

        $classroom = Classroom::create([
            'teacher_id' => $request->user()->id,
            'name' => $request->name,
            'code' => $code,
        ]);

        return redirect()->route('guru.kelas.show', $classroom)->with('toast', ['type' => 'success', 'message' => "Kelas {$classroom->name} dibuat! Kode: {$code}"]);
    }

    public function show(Request $request, Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $classroom->load(['students.progress.module']);
        $modules = Module::orderBy('order')->get();

        return view('guru.kelas-show', compact('classroom', 'modules'));
    }
}

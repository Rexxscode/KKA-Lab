<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomJoinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $classroom = Classroom::where('code', $request->code)->first();

        if (! $classroom) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Kode kelas salah. Coba tanya ke gurumu ya!']);
        }

        $request->user()->classrooms()->syncWithoutDetaching([$classroom->id]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Berhasil gabung kelas ' . $classroom->name . '!']);
    }
}

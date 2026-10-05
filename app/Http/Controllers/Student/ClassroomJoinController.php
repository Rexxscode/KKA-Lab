<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class ClassroomJoinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $classroom = Classroom::where('code', $request->code)->first();

        if (! $classroom || $classroom->status === 'arsip') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Kode kelas salah. Coba tanya ke gurumu ya!']);
        }

        $request->user()->classrooms()->syncWithoutDetaching([$classroom->id]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Berhasil gabung kelas ' . $classroom->name . '!']);
    }

    /**
     * Keluar dari kelas aktif. Progres belajar tetap tersimpan.
     */
    public function destroy(Request $request, StudentModuleService $modul)
    {
        $kelas = $modul->kelasAktif($request->user());

        if (! $kelas) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Kamu belum gabung kelas mana pun.']);
        }

        $request->user()->classrooms()->detach($kelas->id);

        return back()->with('toast', ['type' => 'success', 'message' => 'Keluar dari kelas ' . $kelas->name . '. Progres belajarmu tetap tersimpan!']);
    }
}

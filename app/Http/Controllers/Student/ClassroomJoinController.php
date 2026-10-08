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
        // Normalisasi: buang spasi (mis. "482 913") agar tidak gagal validasi diam-diam.
        $request->merge(['code' => preg_replace('/\s+/', '', (string) $request->input('code', ''))]);

        $request->validate(
            ['code' => 'required|string|size:6'],
            [
                'code.required' => 'Isi dulu kode kelas 6 digit dari gurumu!',
                'code.size' => 'Kode kelas harus tepat 6 digit angka. Periksa lagi kode dari gurumu!',
            ]
        );

        $classroom = Classroom::where('code', $request->code)->first();

        if (! $classroom || $classroom->status === 'arsip') {
            return back()->withInput()->with('toast', ['type' => 'error', 'message' => 'Kode kelas salah atau kelas sudah diarsip. Coba tanya kode terbaru ke gurumu ya!']);
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

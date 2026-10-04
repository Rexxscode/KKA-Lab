<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    /**
     * Dasbor: ringkasan data spesifik (tanpa daftar kelas).
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $kelasQuery = $user->isAdmin() ? Classroom::query() : $user->taughtClassrooms();
        $kelasIds = (clone $kelasQuery)->pluck('classrooms.id');

        $totalKelas = (clone $kelasQuery)->count();
        $siswaIds = \DB::table('classroom_user')->whereIn('classroom_id', $kelasIds)->distinct()->pluck('student_id');
        $totalSiswa = $siswaIds->count();
        if ($user->isAdmin()) {
            $totalModul = Module::count();
            $totalSoal = \App\Models\Quiz::count();
        } else {
            $totalModul = Module::whereNull('teacher_id')->orWhere('teacher_id', $user->id)->count();
            $totalSoal = \App\Models\Quiz::whereNull('teacher_id')->orWhere('teacher_id', $user->id)->count();
        }
        $siswaTuntas = ($totalModul > 0 && $siswaIds->isNotEmpty())
            ? \DB::table('student_progress')->whereIn('student_id', $siswaIds)->where('is_completed', true)
                ->select('student_id')->groupBy('student_id')->havingRaw('COUNT(*) >= ?', [$totalModul])->get()->count()
            : 0;
        $kelasBaru = (clone $kelasQuery)->with('teacher')->withCount('students')->latest()->take(5)->get();
        $siswaAktif = \App\Models\User::where('role', 'siswa')
            ->whereIn('id', $siswaIds)
            ->withSum('progress as xp', 'xp_earned')
            ->orderByDesc('xp')->take(5)->get();

        return view('guru.dashboard', compact('totalKelas', 'totalSiswa', 'totalModul', 'totalSoal', 'siswaTuntas', 'kelasBaru', 'siswaAktif'));
    }

    /**
     * Halaman Kelas Saya terpisah: daftar + buat kelas.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $classrooms = $user->isAdmin()
            ? Classroom::with('teacher')->withCount('students')->latest()->get()
            : $user->taughtClassrooms()->withCount('students')->get();
        if ($user->isAdmin()) {
            $modules = Module::orderBy('order')->withCount('quizzes')->get();
            foreach ($modules as $m) {
                $m->soal = $m->quizzes_count;
            }
        } else {
            $modules = Module::whereNull('teacher_id')->orderBy('order')
                ->withCount(['quizzes as soal' => fn ($q) => $q->whereNull('teacher_id')])->get()
                ->concat(
                    Module::where('teacher_id', $user->id)->orderBy('order')
                        ->withCount(['quizzes as soal' => fn ($q) => $q->where('teacher_id', $user->id)])->get()
                );
        }

        return view('guru.kelas-index', compact('classrooms', 'modules'));
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

        // Susunan awal: modul default (1-4) + modul custom guru.
        $pos = 1;
        foreach (Module::whereNull('teacher_id')->orderBy('order')->get() as $dm) {
            $classroom->modules()->attach($dm->id, ['position' => $pos++]);
        }
        foreach (Module::where('teacher_id', $request->user()->id)->orderBy('order')->get() as $cm) {
            $classroom->modules()->attach($cm->id, ['position' => $pos++]);
        }

        return redirect()->route('guru.kelas.show', $classroom)->with('toast', ['type' => 'success', 'message' => "Kelas {$classroom->name} dibuat! Kode: {$code}"]);
    }

    public function show(Request $request, Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $classroom->load(['students.progress.module', 'modules']);
        $modules = $classroom->modules;

        $user = $request->user();
        $terpasang = $modules->pluck('id')->all();
        $modulTersedia = Module::where(function ($q) use ($user) {
                $q->whereNull('teacher_id');
                if (! $user->isAdmin()) {
                    $q->orWhere('teacher_id', $user->id);
                }
            })->whereNotIn('id', $terpasang)->orderBy('order')->get();

        return view('guru.kelas-show', compact('classroom', 'modules', 'modulTersedia'));
    }
}

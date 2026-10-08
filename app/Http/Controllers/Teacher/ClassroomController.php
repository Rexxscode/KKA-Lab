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

        // Blok "Perlu Perhatian": per kelas, siapa belum mulai & siapa gagal/tertinggal,
        // agar guru tidak perlu membuka tiap kelas satu per satu.
        $perhatian = [];
        $daftarKelas = (clone $kelasQuery)->with(['students.progress.module', 'modules'])->withCount('students')->latest()->take(10)->get();
        foreach ($daftarKelas as $kelas) {
            $modulIds = $kelas->modules->pluck('id')->all();
            if (empty($modulIds)) {
                continue;
            }
            $belumMulai = [];
            $gagal = [];
            foreach ($kelas->students as $siswa) {
                $byModul = $siswa->progress->keyBy('module_id');
                $punyaAktivitas = false;
                $skorTerendah = null;
                $modulGagal = null;
                foreach ($modulIds as $mid) {
                    $p = $byModul->get($mid);
                    if ($p && ($p->reading_done || $p->quiz_score > 0 || $p->hands_on_count > 0)) {
                        $punyaAktivitas = true;
                    }
                    if ($p && ! $p->is_completed && ($p->quiz_score > 0 || $p->reading_done)) {
                        if ($skorTerendah === null || $p->quiz_score < $skorTerendah) {
                            $skorTerendah = $p->quiz_score;
                            $modulGagal = $p->module->title ?? 'modul';
                        }
                    }
                }
                if (! $punyaAktivitas) {
                    $belumMulai[] = $siswa;
                } elseif ($skorTerendah !== null) {
                    $gagal[] = ['siswa' => $siswa, 'skor' => $skorTerendah, 'modul' => $modulGagal];
                }
            }
            // Urutkan yang paling lemah dulu, ambil 5 teratas per kategori.
            usort($gagal, fn ($a, $b) => $a['skor'] <=> $b['skor']);
            if (count($belumMulai) > 0 || count($gagal) > 0) {
                $perhatian[] = [
                    'kelas' => $kelas,
                    'belumMulai' => array_slice($belumMulai, 0, 5),
                    'belumMulaiTotal' => count($belumMulai),
                    'gagal' => array_slice($gagal, 0, 5),
                    'gagalTotal' => count($gagal),
                ];
            }
            if (count($perhatian) >= 5) {
                break;
            }
        }

        return view('guru.dashboard', compact('totalKelas', 'totalSiswa', 'totalModul', 'totalSoal', 'siswaTuntas', 'kelasBaru', 'siswaAktif', 'perhatian'));
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

        // Susunan awal: semua modul default + modul custom guru.
        $pos = 1;
        foreach (Module::whereNull('teacher_id')->orderBy('order')->get() as $dm) {
            $classroom->modules()->attach($dm->id, ['position' => $pos++]);
        }
        foreach (Module::where('teacher_id', $request->user()->id)->orderBy('order')->get() as $cm) {
            $classroom->modules()->attach($cm->id, ['position' => $pos++]);
        }

        return redirect()->route('guru.kelas.show', $classroom)->with('toast', ['type' => 'success', 'message' => "Kelas {$classroom->name} dibuat! Kode: {$code}"]);
    }

    /**
     * Regenerasi kode gabung (kode lama langsung hangus).
     */
    public function kodeBaru(Request $request, Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);

        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Classroom::where('code', $code)->exists());

        $classroom->update(['code' => $code]);
        \Log::info('guru.kode_baru', ['actor' => $request->user()->id, 'kelas' => $classroom->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Kode baru {$classroom->name}: {$code}. Kode lama sudah hangus!"]);
    }

    public function show(Request $request, Classroom $classroom)
    {        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);

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

    /**
     * Lihat modul yang terpasang di kelas (murni read-only, tanpa form ubah).
     * Admin memakai jalur ini untuk melihat modul buatan guru:
     * hanya modul yang terpasang di kelas ini yang bisa dibuka.
     */
    public function modulShow(Request $request, Classroom $classroom, \App\Models\Module $module)
    {
        $this->milik($request, $classroom);
        abort_unless($classroom->modules()->where('modules.id', $module->id)->exists(), 404);

        // Bank yang ditampilkan mengikuti versi efektif (milik guru bila ada,
        // selain itu default) — sama seperti yang dilihat siswa. Tanpa form ubah.
        $user = $request->user();
        if ($module->isDefault()) {
            $soal = $user->isAdmin()
                ? $module->quizzes()->whereNull('teacher_id')->orderBy('id')->get()
                : $module->quizzes()->where('teacher_id', $user->id)->orderBy('id')->get();
            if ($soal->isEmpty()) {
                $soal = $module->quizzes()->whereNull('teacher_id')->orderBy('id')->get();
            }
        } else {
            $soal = $module->quizzes()->where('teacher_id', $module->teacher_id)->orderBy('id')->get();
        }

        return view('guru.kelas-modul-show', compact('classroom', 'module', 'soal'));
    }

    /**
     * Keluarkan siswa dari kelas (progresnya tetap tersimpan).
     */
    public function kick(Request $request, Classroom $classroom, \App\Models\User $user)
    {
        $this->milik($request, $classroom);
        abort_unless($classroom->students()->where('users.id', $user->id)->exists(), 404);

        $classroom->students()->detach($user->id);
        \Log::info('guru.kick_siswa', ['actor' => $request->user()->id, 'kelas' => $classroom->id, 'target' => $user->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "{$user->name} dikeluarkan dari {$classroom->name}."]);
    }

    /**
     * Ulangi progres siswa di modul-modul kelas ini dari nol.
     */
    public function resetProgres(Request $request, Classroom $classroom, \App\Models\User $user)
    {
        $this->milik($request, $classroom);
        abort_unless($classroom->students()->where('users.id', $user->id)->exists(), 404);

        $modulIds = $classroom->modules()->pluck('modules.id');
        \App\Models\StudentProgress::where('student_id', $user->id)->whereIn('module_id', $modulIds)->delete();
        // Bersihkan jejak turunan agar rapor & riwayat konsisten dengan progres nol.
        \App\Models\QuizAttempt::where('student_id', $user->id)->whereIn('module_id', $modulIds)->delete();
        $badgeIds = \App\Models\Badge::whereIn('module_id', $modulIds)->pluck('id');
        $user->badges()->detach($badgeIds);
        \App\Models\Feedback::where('student_id', $user->id)->where('classroom_id', $classroom->id)->whereIn('module_id', $modulIds)->delete();
        \Log::info('guru.reset_progres', ['actor' => $request->user()->id, 'kelas' => $classroom->id, 'target' => $user->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Progres {$user->name} di {$classroom->name} diulang dari nol."]);
    }

    /**
     * Arsipkan / buka lagi kelas. Kelas arsip tak bisa dimasuki & tak aktif.
     */
    public function arsip(Request $request, Classroom $classroom)
    {
        $this->milik($request, $classroom);

        $classroom->update(['status' => $classroom->status === 'arsip' ? 'buka' : 'arsip']);
        \Log::info('guru.arsip_kelas', ['actor' => $request->user()->id, 'kelas' => $classroom->id, 'status' => $classroom->status]);

        return back()->with('toast', ['type' => 'success', 'message' => $classroom->status === 'arsip' ? "Kelas {$classroom->name} diarsipkan." : "Kelas {$classroom->name} dibuka lagi."]);
    }

    private function milik(Request $request, Classroom $classroom): void
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);
    }

}

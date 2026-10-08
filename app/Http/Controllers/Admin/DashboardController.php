<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'admin' => User::where('role', 'admin')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'kelas' => Classroom::count(),
            'modul' => Module::count(),
        ];

        $query = User::query()->orderByDesc('id');
        if ($request->filled('role') && in_array($request->role, ['admin', 'guru', 'siswa'], true)) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $query->where(function ($w) use ($request) {
                $w->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }
        $users = $query->with(['taughtClassrooms' => fn ($q) => $q->withCount('students'), 'progress'])
            ->withCount(['progress as lulus_count' => fn ($q) => $q->where('is_completed', true)])
            ->paginate(15)->withQueryString();
        $classrooms = Classroom::with(['teacher'])->withCount('students')->latest()->take(10)->get();
        $modules = Module::orderBy('order')->withCount('quizzes')->get();

        // Progres belajar tiap siswa: modul, skor, XP, badge, kelas.
        $siswaQuery = User::where('role', 'siswa')->orderBy('name');
        if ($request->filled('q')) {
            $siswaQuery->where(function ($w) use ($request) {
                $w->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }
        $siswaList = $siswaQuery
            ->with(['progress.module', 'classrooms', 'badges'])
            ->paginate(15, ['*'], 'siswa_page')
            ->withQueryString();

        // Pengajuan akun guru yang menunggu verifikasi NPK (arsip data lama; pendaftar baru dihentikan).
        $pengajuan = User::where('role', 'guru')->where('status', 'pending')->latest()->get();

        // Statistik global belajar (reuse tabel existing, tanpa query berat).
        $modulDefaultIds = Module::whereNull('teacher_id')->pluck('id');
        $global = [
            'modul_selesai' => \App\Models\StudentProgress::where('is_completed', true)->count(),
            'rata_nilai' => (int) round((float) (\App\Models\StudentProgress::where('quiz_score', '>', 0)->avg('quiz_score') ?? 0)),
            'sertifikat' => \App\Models\Certificate::count(),
            'siswa_aktif' => \App\Models\StudentProgress::where('last_accessed_at', '>=', now()->subDays(7))->distinct('student_id')->count('student_id'),
        ];
        $global['rata_progress'] = $stats['siswa'] > 0 && $modulDefaultIds->count() > 0
            ? (int) round($global['modul_selesai'] / ($stats['siswa'] * $modulDefaultIds->count()) * 100)
            : 0;

        return view('admin.dashboard', compact('stats', 'users', 'classrooms', 'modules', 'siswaList', 'pengajuan', 'global'));
    }
}

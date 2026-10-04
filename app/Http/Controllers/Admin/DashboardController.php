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
        $users = $query->with(['taughtClassrooms' => fn ($q) => $q->withCount('students')])->paginate(15)->withQueryString();
        $classrooms = Classroom::with(['teacher'])->withCount('students')->latest()->take(10)->get();
        $modules = Module::orderBy('order')->withCount('quizzes')->get();

        return view('admin.dashboard', compact('stats', 'users', 'classrooms', 'modules'));
    }
}

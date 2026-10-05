<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiChat;
use App\Models\Module;
use Illuminate\Http\Request;

class AiLogController extends Controller
{
    /**
     * Guru: log AI milik siswa di kelas-kelasnya.
     */
    public function guru(Request $request)
    {
        $user = $request->user();
        $siswaIds = \DB::table('classroom_user')
            ->whereIn('classroom_id', $user->taughtClassrooms()->pluck('classrooms.id'))
            ->distinct()->pluck('student_id');

        [$logs, $modules, $filter] = $this->daftar($request, $siswaIds);

        return view('guru.ai-logs', compact('logs', 'modules', 'filter'));
    }

    /**
     * Admin: seluruh log AI.
     */
    public function admin(Request $request)
    {
        [$logs, $modules, $filter] = $this->daftar($request, null);

        return view('admin.ai-logs', compact('logs', 'modules', 'filter'));
    }

    private function daftar(Request $request, $siswaIds): array
    {
        $filter = $request->validate([
            'modul' => 'nullable|string|max:100',
            'cari' => 'nullable|string|max:100',
        ]);

        $q = AiChat::with('user')->latest();
        if ($siswaIds !== null) {
            $q->whereIn('user_id', $siswaIds);
        }
        if (! empty($filter['modul'])) {
            $q->where('module_slug', $filter['modul']);
        }
        if (! empty($filter['cari'])) {
            $cari = '%' . $filter['cari'] . '%';
            $q->whereHas('user', fn ($u) => $u->where('name', 'like', $cari)->orWhere('email', 'like', $cari));
        }

        return [$q->paginate(20)->withQueryString(), Module::orderBy('order')->get(), $filter];
    }
}

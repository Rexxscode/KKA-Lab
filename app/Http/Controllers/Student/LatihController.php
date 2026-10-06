<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class LatihController extends Controller
{
    public function index(Request $request, StudentModuleService $modul)
    {
        $module = Module::where('slug', 'latih-ai-mu')->firstOrFail();
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item, 404);

        if ($item['terkunci']) {
            return redirect()
                ->route('siswa.bahan.index')
                ->with('toast', ['type' => 'error', 'message' => 'Selesaikan modul sebelumnya dulu sesuai urutan!']);
        }

        return view('siswa.latih-model', ['module' => $module, 'progress' => $item['progress']]);
    }
}

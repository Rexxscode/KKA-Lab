<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GlobalModel;
use App\Models\Module;
use App\Models\StudentModel;
use App\Models\StudentProgress;
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

    /**
     * Coba model global aktif (hasil kurasi guru).
     */
    public function global(Request $request)
    {
        return view('siswa.model-global', ['global' => GlobalModel::aktif()]);
    }

    /**
     * Kirim model KNN karya siswa ke antre moderasi guru.
     * Dataset dihitung ulang di server (batas 20/label, maks 2MB) — client tak dipercaya.
     */
    public function publish(Request $request, StudentModuleService $modul)
    {
        $data = $request->validate([
            'name' => 'required|string|max:60',
            'labels' => 'required|array|min:2|max:5',
            'labels.*' => 'required|string|max:30|distinct',
            'dataset' => 'required|array',
            'dataset.labels' => 'required|array|min:2|max:5',
            'dataset.contoh' => 'required|array',
        ]);

        $module = Module::where('slug', 'latih-ai-mu')->firstOrFail();
        $item = collect($modul->denganStatus($request->user()))->firstWhere(fn ($d) => $d['module']->id === $module->id);
        abort_unless($item, 404);
        if ($item['terkunci']) {
            return response()->json(['message' => 'Selesaikan modul sebelumnya dulu sesuai urutan!'], 403);
        }
        if ($request->user()->isAdmin()) {
            return response()->json(['message' => 'Mode pratinjau admin: model tidak dikirim.'], 403);
        }
        $progressAda = StudentProgress::where('student_id', $request->user()->id)->where('module_id', $module->id)->first();
        if ($modul->tenggatLewat($request->user(), $module, $progressAda)) {
            return response()->json(['message' => 'Tenggat modul ini sudah lewat. Minta gurumu membuka kembali tenggatnya!'], 403);
        }
        if (StudentModel::where('student_id', $request->user()->id)->where('status', StudentModel::STATUS_PENDING)->count() >= StudentModel::MAKS_PENDING) {
            return response()->json(['message' => 'Antrean penuh (maks 5 menunggu). Tunggu guru memeriksa dulu!'], 429);
        }

        // Validasi ulang isi dataset di server.
        $contoh = $data['dataset']['contoh'] ?? [];
        if (strlen(json_encode($data['dataset'])) > StudentModel::MAKS_BYTE) {
            return response()->json(['errors' => ['dataset' => ['Dataset melebihi 2MB. Kurangi contoh per label.']]], 422);
        }
        foreach ($data['labels'] as $label) {
            $n = isset($contoh[$label]) && is_array($contoh[$label]) ? count($contoh[$label]) : 0;
            if ($n < 20) {
                return response()->json(['errors' => ['dataset' => ["Label \"{$label}\" baru {$n}/20 contoh."]]], 422);
            }
        }

        $model = StudentModel::create([
            'student_id' => $request->user()->id,
            'name' => $data['name'],
            'labels' => array_values($data['labels']),
            'dataset' => $data['dataset'],
            'backbone' => $data['dataset']['backbone'] ?? 'mobilenet',
            'status' => StudentModel::STATUS_PENDING,
        ]);

        return response()->json([
            'id' => $model->id,
            'message' => 'Model "' . $model->name . '" masuk antre! Gurumu akan memeriksa sebelum tayang global.',
        ], 201);
    }
}

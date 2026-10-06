<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\GlobalModel;
use App\Models\Module;
use App\Models\StudentModel;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ModelModerasiController extends Controller
{
    public const LATIH_XP_BONUS = 30;

    public function index(Request $request)
    {
        $user = $request->user();
        $siswaIds = $user->isAdmin()
            ? null
            : \DB::table('classroom_user')
                ->whereIn('classroom_id', $user->taughtClassrooms()->pluck('classrooms.id'))
                ->distinct()->pluck('student_id');

        $q = StudentModel::with('student')->latest();
        if ($siswaIds !== null) {
            $q->whereIn('student_id', $siswaIds);
        }
        $antre = (clone $q)->where('status', StudentModel::STATUS_PENDING)->paginate(15, ['*'], 'antre')->withQueryString();
        $riwayat = (clone $q)->where('status', '!=', StudentModel::STATUS_PENDING)->paginate(15, ['*'], 'riwayat')->withQueryString();
        $global = GlobalModel::aktif();

        return view('guru.model-moderasi', compact('antre', 'riwayat', 'global'));
    }

    public function setujui(Request $request, StudentModel $model)
    {
        $this->milik($request, $model);

        $data = $request->validate(['accuracy' => 'nullable|numeric|min:0|max:100']);

        $model->update(['status' => StudentModel::STATUS_APPROVED, 'accuracy' => $data['accuracy'] ?? null, 'note' => null]);

        // Promote ke model global aktif (nonaktifkan yang lama).
        GlobalModel::where('is_active', true)->update(['is_active' => false]);
        $slug = Str::slug($model->name);
        $versi = (int) (GlobalModel::where('slug', 'like', $slug . '%')->max('version') ?? 0) + 1;
        GlobalModel::create([
            'name' => $model->name,
            'slug' => $versi > 1 ? $slug . '-v' . $versi : $slug,
            'version' => $versi,
            'backbone' => $model->backbone,
            'labels' => $model->labels,
            'dataset' => $model->dataset,
            'student_model_id' => $model->id,
            'approved_by' => $request->user()->id,
            'is_active' => true,
            'accuracy' => $data['accuracy'] ?? null,
        ]);

        // Bonus XP sekali untuk pembuat (modul latih-ai-mu).
        $modul = Module::where('slug', 'latih-ai-mu')->first();
        if ($modul) {
            $progress = StudentProgress::firstOrCreate(
                ['student_id' => $model->student_id, 'module_id' => $modul->id],
                ['quiz_score' => 0, 'xp_earned' => 0, 'is_completed' => false]
            );
            if (! $progress->training_xp_awarded) {
                $progress->update([
                    'xp_earned' => $progress->xp_earned + self::LATIH_XP_BONUS,
                    'training_xp_awarded' => true,
                ]);
            }
        }

        Log::info('guru.model_setujui', ['actor' => $request->user()->id, 'model' => $model->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Model \"{$model->name}\" disetujui & tayang global! Pembuat dapat +" . self::LATIH_XP_BONUS . ' XP.']);
    }

    public function tolak(Request $request, StudentModel $model)
    {
        $this->milik($request, $model);

        $data = $request->validate(['note' => 'required|string|max:500']);

        $model->update(['status' => StudentModel::STATUS_REJECTED, 'note' => $data['note']]);
        Log::info('guru.model_tolak', ['actor' => $request->user()->id, 'model' => $model->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Model \"{$model->name}\" ditolak dengan catatan."]);
    }

    private function milik(Request $request, StudentModel $model): void
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            return;
        }
        abort_unless(
            \DB::table('classroom_user')
                ->whereIn('classroom_id', $user->taughtClassrooms()->pluck('classrooms.id'))
                ->where('student_id', $model->student_id)
                ->exists(),
            403
        );
    }
}

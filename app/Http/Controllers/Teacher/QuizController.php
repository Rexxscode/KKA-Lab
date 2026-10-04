<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Bank soal: modul milik sendiri (+ default sebagai referensi).
     * Admin hanya mengelola modul/soal default.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $milik = Module::whereNull('teacher_id')->orderBy('order')
                ->withCount(['quizzes as soal' => fn ($q) => $q->whereNull('teacher_id')])->get();
            $default = collect();
        } else {
            $milik = Module::where('teacher_id', $user->id)->orderBy('order')->withCount('quizzes')->get();
            foreach ($milik as $m) {
                $m->soal = $m->quizzes_count;
            }
            $default = Module::whereNull('teacher_id')->orderBy('order')
                ->withCount(['quizzes as soal' => fn ($q) => $q->whereNull('teacher_id')])->get();
        }

        return view('guru.quiz-index', compact('milik', 'default'));
    }

    public function module(Request $request, Module $module)
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() ? $module->isDefault() : ($module->isDefault() || $module->teacher_id === $user->id),
            403
        );

        if ($user->isAdmin() || $module->teacher_id === $user->id) {
            $soal = $module->quizzes()->where('teacher_id', $user->isAdmin() ? null : $user->id)->orderBy('id')->get();
            $milikSendiri = true;
        } else {
            // Modul default: tampilkan pratinjau set milik guru bila sudah disalin.
            $soal = $module->quizzes()->where('teacher_id', $user->id)->orderBy('id')->get();
            $milikSendiri = $soal->isNotEmpty();
            if (! $milikSendiri) {
                $soal = $module->quizzes()->whereNull('teacher_id')->orderBy('id')->get();
            }
        }
        $module->setRelation('quizzes', $soal);

        return view('guru.quiz-module', compact('module', 'milikSendiri'));
    }

    /**
     * Salin seluruh soal default menjadi milik guru (sekali saja).
     */
    public function salin(Request $request, Module $module)
    {
        abort_unless($module->isDefault(), 403);
        $user = $request->user();
        abort_if($user->isAdmin(), 403);

        if ($module->quizzes()->where('teacher_id', $user->id)->exists()) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Kamu sudah punya salinan soal modul ini.']);
        }

        foreach ($module->quizzes()->whereNull('teacher_id')->orderBy('id')->get() as $q) {
            $module->quizzes()->create([
                'teacher_id' => $user->id,
                'question' => $q->question,
                'options' => $q->options,
                'correct_index' => $q->correct_index,
                'explanation' => $q->explanation,
                'points' => $q->points,
            ]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal default disalin! Sekarang edit bebas tanpa mengganggu soal admin.']);
    }

    public function store(Request $request, Module $module)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            abort_unless($module->isDefault(), 403);
            $pemilik = null;
        } else {
            abort_unless($module->teacher_id === $user->id, 403, 'Tambah soal hanya di modul milikmu (salin dulu dari default bila perlu).');
            $pemilik = $user->id;
        }

        $options = $this->bersihkanOpsi($request);

        $data = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:255',
            'correct_index' => 'required|integer|min:0|max:' . (count($options) - 1),
        ]);

        $data['points'] = 20;
        $data['teacher_id'] = $pemilik;

        $module->quizzes()->create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil ditambah ke ' . $module->title . '!']);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() ? $quiz->isDefault() : $quiz->teacher_id === $user->id,
            403,
            'Soal default hanya bisa diubah admin.'
        );

        $options = $this->bersihkanOpsi($request);

        $data = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:255',
            'correct_index' => 'required|integer|min:0|max:' . (count($options) - 1),
        ]);

        $quiz->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil diperbarui!']);
    }

    public function destroy(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() ? $quiz->isDefault() : $quiz->teacher_id === $user->id,
            403,
            'Soal default hanya bisa dihapus admin.'
        );

        $quiz->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal dihapus.']);
    }

    private function bersihkanOpsi(Request $request): array
    {
        $options = array_values(array_filter(
            $request->input('options', []),
            fn ($o) => trim((string) $o) !== ''
        ));
        $request->merge(['options' => $options]);

        return $options;
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $modules = Module::withCount('quizzes')->orderBy('order')->get();

        return view('guru.quiz-index', compact('modules'));
    }

    public function module(Module $module)
    {
        $module->load(['quizzes' => fn ($q) => $q->orderBy('id')]);

        return view('guru.quiz-module', compact('module'));
    }

    public function store(Request $request, Module $module)
    {
        // Opsi kosong (C/D opsional) dibuang dulu agar lolos validasi.
        $options = array_values(array_filter(
            $request->input('options', []),
            fn ($o) => trim((string) $o) !== ''
        ));
        $request->merge(['options' => $options]);

        $data = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:255',
            'correct_index' => 'required|integer|min:0|max:' . (count($options) - 1),
        ]);

        $data['points'] = 20;

        $module->quizzes()->create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil ditambah ke ' . $module->title . '!']);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $options = array_values(array_filter(
            $request->input('options', []),
            fn ($o) => trim((string) $o) !== ''
        ));
        $request->merge(['options' => $options]);

        $data = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:255',
            'correct_index' => 'required|integer|min:0|max:' . (count($options) - 1),
        ]);

        $quiz->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil diperbarui!']);
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal dihapus.']);
    }
}

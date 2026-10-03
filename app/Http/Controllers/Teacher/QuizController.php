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
        $modules = Module::with('quizzes')->orderBy('order')->get();

        return view('guru.quiz-index', compact('modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:255',
            'correct_index' => 'required|integer|min:0',
        ]);

        $data['points'] = 20;

        Quiz::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil ditambah!']);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:255',
            'correct_index' => 'required|integer|min:0',
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

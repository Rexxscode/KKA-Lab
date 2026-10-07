<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        } else {
            // Modul default: tampilkan set efektif (milik guru bila sudah ada fork, selain itu pratinjau default).
            $soal = $module->quizzes()->where('teacher_id', $user->id)->orderBy('id')->get();
            if ($soal->isEmpty()) {
                $soal = $module->quizzes()->whereNull('teacher_id')->orderBy('id')->get();
            }
        }
        $module->setRelation('quizzes', $soal);

        return view('guru.quiz-module', compact('module'));
    }

    /**
     * Pastikan guru punya fork lengkap dari soal default modul ini.
     * Dipanggil otomatis saat guru pertama kali menulis (tambah/ubah/hapus),
     * sehingga soal default admin tidak pernah tersentuh.
     */
    private function pastikanSalinan(Module $module, \App\Models\User $user): void
    {
        if (! $module->isDefault()) {
            return;
        }
        if ($module->quizzes()->where('teacher_id', $user->id)->exists()) {
            return;
        }
        foreach ($module->quizzes()->whereNull('teacher_id')->orderBy('id')->get() as $q) {
            $module->quizzes()->create([
                'teacher_id' => $user->id,
                'origin_id' => $q->id,
                'question' => $q->question,
                'options' => $q->options,
                'correct_index' => $q->correct_index,
                'explanation' => $q->explanation,
                'points' => $q->points,
            ]);
        }
    }

    /**
     * Cari baris milik guru yang bersesuaian dengan soal default asal.
     * Cocok via origin_id; fallback teks untuk data lama sebelum backfill.
     */
    private function padananMilik(Module $module, \App\Models\User $user, Quiz $asal): ?Quiz
    {
        return $module->quizzes()
            ->where('teacher_id', $user->id)
            ->where(fn ($q) => $q->where('origin_id', $asal->id)->orWhere('question', $asal->question))
            ->orderBy('id')->first();
    }

    public function store(Request $request, Module $module)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            abort_unless($module->isDefault(), 403);
            $pemilik = null;
        } else {
            // Modul default ikut ter-fork otomatis: tulisan guru hanya
            // berlaku untuk kelas-kelasnya sendiri.
            abort_unless($module->isDefault() || $module->teacher_id === $user->id, 403);
            $this->pastikanSalinan($module, $user);
            $pemilik = $user->id;
        }

        $options = $this->bersihkanOpsi($request);

        $data = $request->validate([
            'question' => 'required|string|max:5000',
            'options' => 'required|array|min:5|max:5',
            'options.*' => 'required|string|max:255',
            'correct_letter' => 'required|in:A,B,C,D,E',
        ]);

        $data['correct_index'] = ord($data['correct_letter']) - 65;
        unset($data['correct_letter']);
        $data['points'] = 20;
        $data['teacher_id'] = $pemilik;

        $module->quizzes()->create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil ditambah ke ' . $module->title . '!']);
    }

    /**
     * Simpan BANYAK soal sekaligus (maks 20 per submit) dalam 1 transaction.
     * Form bulk mengirim `soal[i][question]`, `soal[i][options][0..4]`,
     * `soal[i][correct_letter]`, `soal[i][explanation]` (opsional).
     */
    public function storeBulk(Request $request, Module $module)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            abort_unless($module->isDefault(), 403);
            $pemilik = null;
        } else {
            abort_unless($module->isDefault() || $module->teacher_id === $user->id, 403);
            $this->pastikanSalinan($module, $user);
            $pemilik = $user->id;
        }

        $data = $request->validate([
            'soal' => 'required|array|min:1|max:20',
            'soal.*.question' => 'required|string|max:5000',
            'soal.*.options' => 'required|array|min:5|max:5',
            'soal.*.options.*' => 'required|string|max:255',
            'soal.*.correct_letter' => 'required|in:A,B,C,D,E',
            'soal.*.explanation' => 'nullable|string|max:5000',
        ], [
            'soal.max' => 'Maksimal 20 soal per sekali simpan. Bagi menjadi beberapa batch bila lebih.',
        ]);

        $dibuat = 0;
        DB::transaction(function () use ($module, $data, $pemilik, &$dibuat) {
            foreach ($data['soal'] as $baris) {
                // Lewati blok kosong (mis. guru tambah blok lalu tidak diisi).
                if (trim((string) ($baris['question'] ?? '')) === '') {
                    continue;
                }
                $module->quizzes()->create([
                    'teacher_id' => $pemilik,
                    'question' => $baris['question'],
                    'options' => array_values($baris['options']),
                    'correct_index' => ord($baris['correct_letter']) - 65,
                    'explanation' => $baris['explanation'] ?? null,
                    'points' => 20,
                ]);
                $dibuat++;
            }
        });

        if ($dibuat === 0) {
            return back()->withInput()->with('toast', ['type' => 'error', 'message' => 'Tidak ada soal tersimpan — isi minimal 1 blok soal dulu!']);
        }

        return back()->with('toast', ['type' => 'success', 'message' => $dibuat . ' soal berhasil ditambah ke ' . $module->title . '!']);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        // Guru mengedit soal default -> fork otomatis jadi miliknya.
        if (! $user->isAdmin() && $quiz->isDefault()) {
            $this->pastikanSalinan($quiz->module, $user);
            $quiz = $this->padananMilik($quiz->module, $user, $quiz) ?? $quiz->module->quizzes()->create([
                'teacher_id' => $user->id,
                'origin_id' => $quiz->id,
                'question' => $quiz->question,
                'options' => $quiz->options,
                'correct_index' => $quiz->correct_index,
                'explanation' => $quiz->explanation,
                'points' => $quiz->points,
            ]);
        }

        abort_unless(
            $user->isAdmin() ? $quiz->isDefault() : $quiz->teacher_id === $user->id,
            403,
            'Soal default hanya bisa diubah admin.'
        );

        $options = $this->bersihkanOpsi($request);

        $data = $request->validate([
            'question' => 'required|string|max:5000',
            'options' => 'required|array|min:5|max:5',
            'options.*' => 'required|string|max:255',
            'correct_letter' => 'required|in:A,B,C,D,E',
        ]);

        $data['correct_index'] = ord($data['correct_letter']) - 65;
        unset($data['correct_letter']);

        $quiz->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Soal berhasil diperbarui!']);
    }

    public function destroy(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        // Guru menghapus soal default -> fork otomatis lalu hapus
        // padanannya, sehingga hanya hilang dari kelasnya sendiri.
        if (! $user->isAdmin() && $quiz->isDefault()) {
            $this->pastikanSalinan($quiz->module, $user);
            if ($padanan = $this->padananMilik($quiz->module, $user, $quiz)) {
                $padanan->delete();
            }

            return back()->with('toast', ['type' => 'success', 'message' => 'Soal dihapus dari bank soal kelasmu. Soal default admin tetap aman.']);
        }

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

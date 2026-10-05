<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /**
     * Daftar modul: milik sendiri (+ default sebagai referensi baca).
     * Admin melihat & mengelola modul default.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $milik = Module::whereNull('teacher_id')->orderBy('order')->withCount(['quizzes as soal' => fn ($q) => $q->whereNull('teacher_id')])->get();
            $default = collect();
        } else {
            $milik = Module::where('teacher_id', $user->id)->orderBy('order')->withCount('quizzes')->get();
            $default = Module::whereNull('teacher_id')->orderBy('order')->withCount(['quizzes as soal' => fn ($q) => $q->whereNull('teacher_id')])->get();
        }

        return view('guru.modul-index', compact('milik', 'default'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'materi' => 'required|string|max:50000',
            'xp_reward' => 'nullable|integer|min:0|max:1000',
        ]);

        $user = $request->user();
        $slug = Str::slug($data['title']) . '-' . substr(md5(uniqid((string) rand(), true)), 0, 6);

        Module::create([
            'teacher_id' => $user->isAdmin() ? null : $user->id,
            'title' => $data['title'],
            'slug' => $slug,
            'order' => (int) (Module::where('teacher_id', $user->isAdmin() ? null : $user->id)->max('order') ?? 0) + 1,
            'description' => $data['description'] ?? null,
            'materi' => $data['materi'] ?? null,
            'xp_reward' => $data['xp_reward'] ?? 100,
        ]);

        // Otomatis ditempel ke semua kelas milik guru (posisi akhir).
        if (! $user->isAdmin()) {
            foreach ($user->taughtClassrooms as $kelas) {
                if (! $kelas->modules()->where('modules.id', Module::where('slug', $slug)->first()->id)->exists()) {
                    $pos = (int) ($kelas->modules()->max('classroom_module.position') ?? 0) + 1;
                    $kelas->modules()->attach(Module::where('slug', $slug)->first()->id, ['position' => $pos]);
                }
            }
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Modul "' . $data['title'] . '" berhasil dibuat!']);
    }

    public function update(Request $request, Module $module)
    {
        $this->izinkan($request, $module);

        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'materi' => 'nullable|string|max:50000',
            'xp_reward' => 'nullable|integer|min:0|max:1000',
        ]);

        $module->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Modul berhasil diperbarui!']);
    }

    public function destroy(Request $request, Module $module)
    {
        $this->izinkan($request, $module);

        $judul = $module->title;
        $module->delete();

        return redirect()->route('guru.modul.index')->with('toast', ['type' => 'success', 'message' => "Modul \"{$judul}\" beserta soalnya dihapus."]);
    }

    /**
     * Tempel modul (milik sendiri/default) ke salah satu kelas guru.
     */
    public function tempel(Request $request, Classroom $classroom)
    {
        $this->kelasMilik($request, $classroom);

        $data = $request->validate(['module_id' => 'required|exists:modules,id']);
        $module = Module::findOrFail($data['module_id']);

        abort_unless(
            $module->isDefault() || $module->teacher_id === $request->user()->id || $request->user()->isAdmin(),
            403
        );

        if (! $classroom->modules()->where('modules.id', $module->id)->exists()) {
            $pos = (int) ($classroom->modules()->max('classroom_module.position') ?? 0) + 1;
            $classroom->modules()->attach($module->id, ['position' => $pos]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => "Modul \"{$module->title}\" ditempel ke {$classroom->name}."]);
    }

    public function lepas(Request $request, Classroom $classroom, Module $module)
    {
        $this->kelasMilik($request, $classroom);
        $this->modulTerpasang($classroom, $module);
        $classroom->modules()->detach($module->id);

        return back()->with('toast', ['type' => 'success', 'message' => "Modul \"{$module->title}\" dilepas dari {$classroom->name}."]);
    }

    public function naik(Request $request, Classroom $classroom, Module $module)
    {
        return $this->geser($request, $classroom, $module, -1);
    }

    public function turun(Request $request, Classroom $classroom, Module $module)
    {
        return $this->geser($request, $classroom, $module, 1);
    }

    private function geser(Request $request, Classroom $classroom, Module $module, int $arah)
    {
        $this->kelasMilik($request, $classroom);
        $this->modulTerpasang($classroom, $module);

        $daftar = $classroom->modules()->orderByPivot('position')->get();
        $idx = $daftar->search(fn ($m) => $m->id === $module->id);
        $tukar = $idx + $arah;

        if ($idx === false || ! isset($daftar[$tukar])) {
            return back();
        }

        $posA = $daftar[$idx]->pivot->position;
        $posB = $daftar[$tukar]->pivot->position;
        $classroom->modules()->updateExistingPivot($module->id, ['position' => $posB]);
        $classroom->modules()->updateExistingPivot($daftar[$tukar]->id, ['position' => $posA]);

        return back();
    }

    private function izinkan(Request $request, Module $module): void
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() ? $module->isDefault() : $module->teacher_id === $user->id,
            403,
            'Modul default hanya bisa diubah admin.'
        );
    }

    private function kelasMilik(Request $request, Classroom $classroom): void
    {
        abort_unless(
            $classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(),
            403
        );
    }

    private function modulTerpasang(Classroom $classroom, Module $module): void
    {
        abort_unless(
            $classroom->modules()->where('modules.id', $module->id)->exists(),
            404,
            'Modul tidak terpasang di kelas ini.'
        );
    }
}

<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Module;
use App\Models\User;

/**
 * Menentukan modul apa yang dilihat siswa (berdasar kelas aktifnya),
 * urutannya, status terkunci sekuensial, dan set soal efektif.
 *
 * Aturan:
 * - Siswa tanpa kelas: 4 modul default (teacher_id NULL) urut `order`.
 * - Siswa berkelas: modul kelas aktifnya (pivot classroom_module.position).
 * - Modul posisi N terkunci sampai posisi N-1 selesai (is_completed).
 * - Soal efektif: milik guru kelas aktif bila ada, selain itu default.
 * - Sertifikat selalu dari modul default saja.
 */
class StudentModuleService
{
    public function kelasAktif(User $user): ?Classroom
    {
        return $user->classrooms()->orderByDesc('classroom_user.id')->first();
    }

    public function guruKelasAktif(User $user): ?int
    {
        return $this->kelasAktif($user)?->teacher_id;
    }

    public function daftarModul(User $user)
    {
        $kelas = $this->kelasAktif($user);

        if ($kelas) {
            return $kelas->modules()->get();
        }

        return Module::whereNull('teacher_id')->orderBy('order')->get();
    }

    /**
     * @return array<int, array{module: Module, progress: ?\App\Models\StudentProgress, terkunci: bool, posisi: int, jumlah_soal: int}>
     */
    public function denganStatus(User $user): array
    {
        $modules = $this->daftarModul($user);
        $progress = $user->progress()->get()->keyBy('module_id');
        $guruId = $this->guruKelasAktif($user);

        $hasil = [];
        $sebelumSelesai = true;
        foreach ($modules->values() as $i => $m) {
            $p = $progress->get($m->id);
            $terkunci = $i > 0 && ! $sebelumSelesai;
            $hasil[] = [
                'module' => $m,
                'progress' => $p,
                'terkunci' => $terkunci,
                'posisi' => $i + 1,
                'jumlah_soal' => $this->jumlahSoalEfektif($m, $guruId),
            ];
            $sebelumSelesai = $p && $p->is_completed;
        }

        return $hasil;
    }

    public function soalEfektif(Module $module, ?int $teacherId = null)
    {
        if ($teacherId) {
            $milik = $module->quizzes()->where('teacher_id', $teacherId)->orderBy('id')->get();
            if ($milik->isNotEmpty()) {
                return $milik;
            }
        }

        return $module->quizzes()->whereNull('teacher_id')->orderBy('id')->get();
    }

    public function jumlahSoalEfektif(Module $module, ?int $teacherId = null): int
    {
        if ($teacherId) {
            $n = $module->quizzes()->where('teacher_id', $teacherId)->count();
            if ($n > 0) {
                return $n;
            }
        }

        return $module->quizzes()->whereNull('teacher_id')->count();
    }

    public function modulDefault()
    {
        return Module::whereNull('teacher_id')->orderBy('order')->get();
    }
}

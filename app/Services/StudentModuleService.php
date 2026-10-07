<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Module;
use App\Models\Quiz;
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
    /**
     * Jumlah soal tampil per percobaan (legacy, dipertahankan untuk kompatibilitas).
     * Aturan baru per jenis bank ada di konstanta LATIHAN_*_TAMPIL di bawah.
     */
    public const SOAL_PERCOBAAN = 15;

    /**
     * Aturan bank & sampling latihan/ujian (sumber kebenaran tunggal).
     * - Modul default admin: target bank 30, tiap latihan keluar 15 acak.
     * - Modul buatan guru: bank wajib 25–30, tiap latihan keluar 15 acak.
     * - Ujian akhir: 40 soal acak dari ke-6 modul default, lulus jika >= 80.
     */
    public const BANK_DEFAULT_TARGET = 30;

    public const LATIHAN_DEFAULT_TAMPIL = 15;

    public const BANK_GURU_MIN = 25;

    public const BANK_GURU_MAX = 30;

    public const LATIHAN_GURU_TAMPIL = 15;

    public const UJIAN_AKHIR_JUMLAH = 40;

    public const UJIAN_AKHIR_PASS = 80;

    public const UJIAN_AKHIR_MENIT = 60;
    public function kelasAktif(User $user): ?Classroom
    {
        return $user->classrooms()->where('classrooms.status', 'buka')->orderByDesc('classroom_user.id')->first();
    }

    public function guruKelasAktif(User $user): ?int
    {
        return $this->kelasAktif($user)?->teacher_id;
    }

    /**
     * Tenggat modul di kelas aktif siswa (null bila tak ada tenggat).
     * Dipakai menegakkan due_at di sisi server.
     */
    public function tenggatUntuk(User $user, Module $module): ?\Illuminate\Support\Carbon
    {
        $kelas = $this->kelasAktif($user);
        if (! $kelas) {
            return null;
        }
        $pivot = $kelas->modules()->where('modules.id', $module->id)->first()?->pivot;

        return $pivot?->due_at ? \Illuminate\Support\Carbon::parse($pivot->due_at) : null;
    }

    /**
     * True bila tenggat lewat dan modul belum lulus.
     */
    public function tenggatLewat(User $user, Module $module, $progress = null): bool
    {
        $tenggat = $this->tenggatUntuk($user, $module);
        if (! $tenggat || ! $tenggat->isPast()) {
            return false;
        }

        return ! ($progress && $progress->is_completed);
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

        // Hitung soal per modul sekaligus (2 query) agar tidak N+1.
        $ids = $modules->pluck('id');
        $milik = $guruId
            ? Quiz::whereIn('module_id', $ids)->where('teacher_id', $guruId)
                ->selectRaw('module_id, COUNT(*) AS n')->groupBy('module_id')->pluck('n', 'module_id')
            : collect();
        $default = Quiz::whereIn('module_id', $ids)->whereNull('teacher_id')
            ->selectRaw('module_id, COUNT(*) AS n')->groupBy('module_id')->pluck('n', 'module_id');

        $hasil = [];
        $sebelumSelesai = true;
        foreach ($modules->values() as $i => $m) {
            $p = $progress->get($m->id);
            $terkunci = $i > 0 && ! $sebelumSelesai;
            // Jumlah soal yang keluar per latihan: 15 acak (bank guru) atau 20 acak (bank default).
            $bankMilik = (int) ($milik->get($m->id, 0));
            $tampil = $bankMilik > 0
                ? min($bankMilik, self::LATIHAN_GURU_TAMPIL)
                : min((int) $default->get($m->id, 0), self::LATIHAN_DEFAULT_TAMPIL);
            $hasil[] = [
                'module' => $m,
                'progress' => $p,
                'terkunci' => $terkunci,
                'posisi' => $i + 1,
                'jumlah_soal' => (int) ($milik->get($m->id) ?? $default->get($m->id, 0)),
                'tampil' => $tampil,
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

    /**
     * Jumlah soal latihan yang keluar untuk satu percobaan.
     * - Modul default: 15 acak (atau seluruh bank bila bank < 15, mis. masa pengisian admin).
     * - Modul guru (custom maupun fork): 15 acak (atau seluruh bank bila < 15).
     */
    public function jumlahTampilLatihan(Module $module, ?int $teacherId = null): int
    {
        $efektif = $this->soalEfektif($module, $teacherId);
        $bank = $efektif->count();

        // Versi guru (modul custom atau fork default yang sudah ada isinya): keluar 15 acak.
        if ($teacherId && $module->quizzes()->where('teacher_id', $teacherId)->exists()) {
            return (int) min($bank, self::LATIHAN_GURU_TAMPIL);
        }

        // Versi default admin: keluar 20 acak (atau seluruh bank bila masih diisi menuju 40).
        return (int) min($bank, self::LATIHAN_DEFAULT_TAMPIL);
    }

    /**
     * True bila bank efektif milik guru belum memenuhi minimal 25 soal,
     * sehingga latihan modul tersebut diblokir untuk siswa sampai gurunya melengkapi.
     * Bank default admin tidak diblokir (admin mengisi bertahap menuju 40).
     */
    public function bankGuruKurang(Module $module, ?int $teacherId = null): bool
    {
        if (! $teacherId) {
            return false;
        }

        $milik = $module->quizzes()->where('teacher_id', $teacherId)->count();

        // Modul default tanpa fork guru memakai bank default admin → tidak diblokir.
        if ($module->isDefault() && $milik === 0) {
            return false;
        }

        return $milik < self::BANK_GURU_MIN;
    }

    /**
     * Kumpulan soal ujian akhir: acak dari bank ke-6 modul default.
     * Mengembalikan maksimal UJIAN_AKHIR_JUMLAH quiz (seluruh bank bila kurang).
     */
    public function soalUjianAkhir()
    {
        $ids = $this->modulDefault()->pluck('id');

        return Quiz::whereIn('module_id', $ids)->whereNull('teacher_id')->orderBy('id')->get();
    }

    /**
     * Susun paket acak untuk satu percobaan kuis: urutan soal diacak,
     * opsi tiap soal diacak, maksimal SOAL_PERCOBAAN soal.
     *
     * @return array<int, array{id: int, order: array<int, int>}>
     *   `order` = daftar indeks lama dalam urutan tampil baru.
     */
    public function paketAcak($quizzes, ?int $jumlah = null): array
    {
        $acak = $quizzes->shuffle()->take($jumlah ?? self::SOAL_PERCOBAAN)->values();
        $paket = [];
        foreach ($acak as $quiz) {
            $n = count($quiz->options ?? []);
            $order = $n > 0 ? collect(range(0, $n - 1))->shuffle()->all() : [];
            $paket[] = ['id' => $quiz->id, 'order' => array_values($order)];
        }

        return $paket;
    }

    /**
     * Uraikan paket session menjadi data siap tampil & dinilai.
     *
     * @return array<int, array{quiz: \App\Models\Quiz, options: array, correct: int}>
     */
    public function uraikanPaket(array $paket): array
    {
        $ids = collect($paket)->map(fn ($b) => $b['id'] ?? null)->filter()->all();
        $map = Quiz::whereIn('id', $ids)->get()->keyBy('id');

        $hasil = [];
        foreach ($paket as $baris) {
            $quiz = $map->get($baris['id'] ?? null);
            if (! $quiz) {
                continue;
            }
            $opsiLama = $quiz->options ?? [];
            $order = $baris['order'] ?? range(0, count($opsiLama) - 1);
            $opsiBaru = [];
            foreach ($order as $idxLama) {
                $opsiBaru[] = $opsiLama[$idxLama] ?? '';
            }
            $correctBaru = array_search((int) $quiz->correct_index, $order, true);
            $hasil[] = [
                'quiz' => $quiz,
                'options' => $opsiBaru,
                'correct' => $correctBaru === false ? 0 : (int) $correctBaru,
                'order' => array_values($order),
            ];
        }

        return $hasil;
    }
}

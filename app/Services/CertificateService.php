<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\FinalExamAttempt;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Penerbitan sertifikat murni backend.
 * Syarat: HANYA 6 modul default (teacher_id NULL), masing-masing
 * is_completed + quiz_score >= 80, DITAMBAH lulus ujian akhir (>= 80).
 * Modul custom guru diabaikan.
 */
class CertificateService
{
    public function __construct(private StudentModuleService $modul) {}

    public function modulDefault()
    {
        return Module::whereNull('teacher_id')->orderBy('order')->get();
    }

    /**
     * 6 modul default lulus (tanpa ujian akhir). Dipakai sebagai gerbang
     * membuka halaman ujian akhir.
     */
    public function modulLulus(User $user): bool
    {
        $modules = $this->modulDefault();
        if ($modules->count() === 0) {
            return false;
        }
        $progress = $user->progress()->get()->keyBy('module_id');

        return $modules->every(fn ($m) => isset($progress[$m->id])
            && $progress[$m->id]->is_completed
            && (int) $progress[$m->id]->quiz_score >= StudentProgress::PASS_SCORE);
    }

    public function finalExamPassed(User $user): bool
    {
        return FinalExamAttempt::where('student_id', $user->id)->where('passed', true)->exists();
    }

    public function isEligible(User $user): bool
    {
        return $this->modulLulus($user) && $this->finalExamPassed($user);
    }

    public function countLulus(User $user): int
    {
        $modules = $this->modulDefault();
        $progress = $user->progress()->get()->keyBy('module_id');

        return $modules->filter(fn ($m) => isset($progress[$m->id])
            && $progress[$m->id]->is_completed
            && (int) $progress[$m->id]->quiz_score >= StudentProgress::PASS_SCORE)->count();
    }

    /**
     * Terbitkan sertifikat bila eligible, idempoten (1 user = 1 sertifikat).
     * Mengembalikan Certificate atau null bila belum eligible.
     */
    public function issueIfEligible(User $user): ?Certificate
    {
        return DB::transaction(function () use ($user) {
            $modules = Module::whereNull('teacher_id')->orderBy('order')->lockForUpdate()->get();
            if ($modules->count() === 0) {
                return null;
            }

            $progress = StudentProgress::where('student_id', $user->id)
                ->whereIn('module_id', $modules->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('module_id');

            // Verifikasi ulang keenam modul default di dalam transaction.
            foreach ($modules as $m) {
                $p = $progress->get($m->id);
                if (! $p || ! $p->is_completed || (int) $p->quiz_score < StudentProgress::PASS_SCORE) {
                    return null;
                }
            }

            // Syarat tambahan: lulus ujian akhir (tidak percaya frontend).
            if (! FinalExamAttempt::where('student_id', $user->id)->where('passed', true)->exists()) {
                return null;
            }

            $existing = Certificate::where('user_id', $user->id)->first();
            if ($existing) {
                return $existing;
            }

            $superAdmin = User::superAdmin();
            $guru = $this->modul->kelasAktif($user)?->teacher;

            return Certificate::create([
                'user_id' => $user->id,
                'code' => $this->kodeUnik(),
                'issued_at' => now(),
                'super_admin_id' => $superAdmin?->id,
                'guru_id' => $guru?->id,
                'super_admin_nama' => $superAdmin?->name,
                'guru_nama' => $guru?->name,
                'super_admin_sign_path' => $this->arsipkanTtd($superAdmin, 'superadmin'),
                'guru_sign_path' => $this->arsipkanTtd($guru, 'guru'),
            ]);
        });
    }

    private function kodeUnik(): string
    {
        do {
            $code = 'KBT-' . Str::upper(Str::random(8));
        } while (Certificate::where('code', $code)->exists());

        return $code;
    }

    private function arsipkanTtd(?User $penanda, string $peran): ?string
    {
        if (! $penanda || ! $penanda->signature_path) {
            return null;
        }
        if (! Storage::disk('public')->exists($penanda->signature_path)) {
            return null;
        }
        $tujuan = 'signatures/arsip/' . Str::uuid() . '_' . $peran . '.png';

        return Storage::disk('public')->copy($penanda->signature_path, $tujuan) ? $tujuan : null;
    }
}

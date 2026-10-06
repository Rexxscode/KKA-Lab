<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function index(Request $request, StudentModuleService $modul)
    {
        $user = $request->user();
        // Sertifikat hanya dari modul default (milik admin).
        $modules = Module::whereNull('teacher_id')->orderBy('order')->get();
        $progress = $user->progress()->get()->keyBy('module_id');

        $eligible = $modules->count() > 0 && $modules->every(function ($m) use ($progress) {
            return isset($progress[$m->id]) && $progress[$m->id]->is_completed && $progress[$m->id]->quiz_score >= StudentProgress::PASS_SCORE;
        });

        $totalXp = $user->totalXp();
        $lulus = $modules->filter(fn ($m) => isset($progress[$m->id]) && $progress[$m->id]->is_completed && $progress[$m->id]->quiz_score >= StudentProgress::PASS_SCORE)->count();

        // Kode acak tersimpan: stabil tiap kunjungan & bisa diverifikasi publik.
        // Penandatangan dikunci saat terbit: super admin + guru kelas aktif.
        $certificate = null;
        if ($eligible) {
            $superAdmin = User::superAdmin();
            $guru = $modul->kelasAktif($user)?->teacher;
            $certificate = Certificate::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'code' => $this->kodeUnik(),
                    'issued_at' => now(),
                    'super_admin_id' => $superAdmin?->id,
                    'guru_id' => $guru?->id,
                    'super_admin_nama' => $superAdmin?->name,
                    'guru_nama' => $guru?->name,
                    'super_admin_sign_path' => $this->arsipkanTtd($superAdmin, 'superadmin'),
                    'guru_sign_path' => $this->arsipkanTtd($guru, 'guru'),
                ]
            );
        }

        return view('siswa.sertifikat', compact('modules', 'progress', 'eligible', 'totalXp', 'certificate', 'lulus'));
    }

    private function kodeUnik(): string
    {
        do {
            $code = 'KKA-' . Str::upper(Str::random(8));
        } while (Certificate::where('code', $code)->exists());

        return $code;
    }

    /**
     * Salin gambar tanda tangan penandatangan ke arsip sertifikat
     * agar tidak berubah bila ia menggambar ulang nanti.
     */
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

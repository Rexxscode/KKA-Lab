<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request, CertificateService $sertifikat)
    {
        $user = $request->user();
        // Admin yang membuka langsung (bookmark lama) tidak boleh menerbitkan untuk dirinya sendiri.
        $isPreviewAdmin = $user->isAdmin();
        // Sertifikat HANYA dari 6 modul default (teacher_id NULL). Modul custom guru diabaikan.
        $modules = $sertifikat->modulDefault();
        $progress = $user->progress()->get()->keyBy('module_id');

        $eligible = $sertifikat->isEligible($user);
        $totalXp = $user->totalXp();
        $lulus = $sertifikat->countLulus($user);

        // Penerbitan idempoten dalam transaction (1 user = 1 sertifikat, anti-duplikat).
        // Admin pratinjau: read-only, jangan terbitkan.
        $certificate = null;
        if ($eligible && ! $isPreviewAdmin) {
            try {
                $certificate = $sertifikat->issueIfEligible($user);
            } catch (\Throwable $e) {
                report($e);
                $certificate = \App\Models\Certificate::where('user_id', $user->id)->first();
            }
        }

        return view('siswa.sertifikat', compact('modules', 'progress', 'eligible', 'totalXp', 'certificate', 'lulus'));
    }
}

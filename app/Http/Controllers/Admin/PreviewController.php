<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\RaportController;
use App\Models\Certificate;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\CertificateService;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PreviewController extends Controller
{
    /**
     * Daftar siswa untuk dipilih sebagai target pratinjau.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $siswa = User::where('role', 'siswa')
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('name')
            ->withCount(['progress as lulus_count' => fn ($c) => $c->where('is_completed', true)])
            ->paginate(15)
            ->withQueryString();

        return view('admin.pratinjau-index', compact('siswa', 'q'));
    }

    /**
     * Pratinjau dashboard milik siswa target — READ-ONLY.
     * Tidak memanggil touchOpen/recordQuiz/join/certificate milik admin.
     */
    public function show(User $siswa, StudentModuleService $modul, CertificateService $sertifikat)
    {
        abort_unless($siswa->isSiswa(), 404);

        $user = $siswa;
        $daftar = $modul->denganStatus($user);
        $totalXp = $user->totalXp();
        $badges = $user->badges()->with('module')->get();
        $classrooms = $user->classrooms()->with('teacher')->get();
        $kelasAktif = $modul->kelasAktif($user);
        $terakhir = RaportController::terakhir($user->id);
        $riwayat = QuizAttempt::with('module')->where('student_id', $user->id)->latest()->take(5)->get();

        $rapor = (new RaportController)->ringkasan($user, $modul);
        $selesai = collect($daftar)->where(fn ($d) => $d['progress'] && $d['progress']->is_completed)->count();
        $totalModul = count($daftar);
        $persen = $totalModul ? (int) round($selesai / $totalModul * 100) : 0;

        $lanjut = collect($daftar)->first(fn ($d) => ! $d['terkunci'] && $d['progress'] && ! $d['progress']->is_completed && ($d['progress']->reading_done || $d['progress']->quiz_score > 0))
            ?? collect($daftar)->first(fn ($d) => ! $d['terkunci'] && (! $d['progress'] || ! $d['progress']->is_completed));

        $nilaiList = collect($daftar)->map(fn ($d) => $d['progress'] ? (int) $d['progress']->quiz_score : 0)->filter(fn ($n) => $n > 0);
        $rataNilai = $nilaiList->count() ? (int) round($nilaiList->avg()) : 0;
        $nilaiTerbaik = $nilaiList->count() ? (int) $nilaiList->max() : 0;
        $totalKuis = QuizAttempt::where('student_id', $user->id)->count();

        $sertifikatAda = Certificate::where('user_id', $user->id)->exists();
        $sertifikatEligible = $sertifikat->isEligible($user);
        $modulLulusSemua = $sertifikat->modulLulus($user);
        $ujianLulus = $sertifikat->finalExamPassed($user);

        Log::info('admin.pratinjau', ['actor' => request()->user()->id, 'target' => $user->id]);

        return view('siswa.dashboard', compact('daftar', 'totalXp', 'badges', 'classrooms', 'kelasAktif', 'terakhir', 'riwayat', 'rapor', 'selesai', 'totalModul', 'persen', 'lanjut', 'rataNilai', 'nilaiTerbaik', 'totalKuis', 'sertifikatAda', 'sertifikatEligible', 'modulLulusSemua', 'ujianLulus') + [
            'isPreview' => true,
            'previewUser' => $user,
        ]);
    }
}

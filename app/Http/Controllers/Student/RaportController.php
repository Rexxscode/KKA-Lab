<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\QuizAttempt;
use App\Models\StudentProgress;
use App\Models\User;
use App\Services\StudentModuleService;
use Illuminate\Http\Request;

class RaportController extends Controller
{
    /**
     * Saran baca-ulang per modul default bila skor lemah.
     */
    public const SARAN_BAGIAN = [
        'apa-itu-ai' => 'baca ulang bagian 3–4 (Machine Learning & Data Berlabel)',
        'latih-ai-mu' => 'baca ulang materi deteksi objek & konsep model pretrained, lalu ulangi kuis sampai lulus',
        'bias-data' => 'mainkan lagi slider simulasi sampai selisih kelompok kecil (adil)',
        'etika-hoaks-ai' => 'hafalkan lagi rumus CHECK (Cek sumber • Hindari judul bombastis • Eja tanggal • Cek gambar • Kroscek)',
        'python-dasar-ai' => 'jalankan ulang contoh print, list, dan fungsi sampai tanpa salah ketik',
        'data-logika-ai' => 'jalankan ulang contoh rata-rata, filter data, dan mini-classifier aturan',
    ];

    public function index(Request $request, StudentModuleService $modul)
    {
        $user = $request->user();

        return view('siswa.raporku', $this->ringkasan($user, $modul));
    }

    /**
     * Versi cetak: tabel ringkas tanpa sidebar, siap print/Simpan PDF.
     */
    public function cetak(Request $request, StudentModuleService $modul)
    {
        $user = $request->user();

        return view('siswa.raporku-cetak', $this->ringkasan($user, $modul) + ['siswa' => $user]);
    }

    /**
     * Bangun data rapor agar bisa dipakai ulang (web, cetak, guru).
     */
    public function ringkasan(User $user, StudentModuleService $modul): array
    {
        $daftar = $modul->denganStatus($user);

        $skor = [];
        $saran = [];
        foreach ($daftar as $d) {
            $m = $d['module'];
            $p = $d['progress'];
            $nilai = $p ? (int) $p->quiz_score : 0;
            $skor[] = ['label' => $d['posisi'] . '. ' . $m->title, 'nilai' => $nilai];

            if (! $p || (! $p->reading_done && $p->quiz_score === 0 && ! $p->is_completed)) {
                $saran[] = [
                    'modul' => $m->title,
                    'tipe' => 'mulai',
                    'teks' => "Mulai dari bahan bacaan “{$m->title}”, scroll sampai akhir, lalu tandai selesai membaca.",
                    'tautan' => route('siswa.bahan.show', $m->slug),
                    'label_tautan' => 'Baca bahan',
                ];
            } elseif (! $p->is_completed) {
                $bagian = self::SARAN_BAGIAN[$m->slug] ?? 'baca ulang materi modul ini dari awal';
                $saran[] = [
                    'modul' => $m->title,
                    'tipe' => 'lemah',
                    'teks' => "Modul “{$m->title}” lemah (skor {$nilai}) → {$bagian}, lalu ulangi kuis (soal diacak tiap percobaan).",
                    'tautan' => route('siswa.bahan.show', $m->slug),
                    'label_tautan' => 'Pelajari lagi',
                ];
            } elseif ($nilai < 90) {
                $saran[] = [
                    'modul' => $m->title,
                    'tipe' => 'mantap',
                    'teks' => "“{$m->title}” sudah lulus (skor {$nilai}). Mantapkan ke 90+ dengan mengulang kuis — soalnya diacak.",
                    'tautan' => route('siswa.kuis.show', $m->slug),
                    'label_tautan' => 'Ulangi kuis',
                ];
            }
        }

        $totalXp = $user->totalXp();
        $lulus = collect($daftar)->filter(fn ($d) => $d['progress'] && $d['progress']->is_completed)->count();

        // Skor terbaik tiap modul dari histori percobaan.
        $terbaik = QuizAttempt::where('student_id', $user->id)
            ->selectRaw('module_id, MAX(score) AS s')->groupBy('module_id')->pluck('s', 'module_id');

        // Catatan guru terbaru tiap modul (dari kelas mana pun).
        $umpanBalik = Feedback::with(['classroom', 'module'])->where('student_id', $user->id)
            ->latest()->get()->unique('module_id')->keyBy('module_id');

        return compact('daftar', 'skor', 'saran', 'totalXp', 'lulus', 'terbaik', 'umpanBalik');
    }

    /**
     * Entri progres terakhir untuk banner permanen dasbor.
     */
    public static function terakhir(int $userId): ?StudentProgress
    {
        return StudentProgress::with('module')->where('student_id', $userId)->latest('updated_at')->first();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Certificate;
use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgresSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = Module::whereNull('teacher_id')->orderBy('order')->get();
        $siswas = User::where('role', 'siswa')->orderBy('id')->get();

        foreach ($siswas as $i => $siswa) {
            $gaya = $siswa->email === 'siswa@kkalab.sch.id' ? 0 : ($i % 5);

            foreach ($defaults as $urutan => $modul) {
                $this->progres($siswa, $modul, $gaya, $urutan);
            }

            // Sertifikat untuk yang 6/6 lulus.
            $lulusSemua = $defaults->every(function ($modul) use ($siswa) {
                $p = StudentProgress::where('student_id', $siswa->id)->where('module_id', $modul->id)->first();

                return $p && $p->is_completed && $p->quiz_score >= StudentProgress::PASS_SCORE;
            });
            if ($lulusSemua) {
                Certificate::firstOrCreate(
                    ['user_id' => $siswa->id],
                    ['code' => $this->kodeUnik(), 'issued_at' => now()->subDays(2 + ($i % 20))]
                );
            }

            // Badge generik untuk yang menonjol.
            if ($gaya === 0) {
                $this->beriLencana($siswa, ['Tuntas Semua Modul', 'Perfeksionis', 'Bintang Sertifikat']);
            } elseif ($gaya <= 2) {
                $this->beriLencana($siswa, ['Rajin Praktik', 'Pantang Menyerah']);
            }
        }
    }

    private function progres(User $siswa, Module $modul, int $gaya, int $urutan): void
    {
        // 0 bintang: semua lulus. 1-2 menengah: 3-4 lulus. 3 pemula: 1-2 berjalan. 4 acak.
        $lulus = match (true) {
            $gaya === 0 => true,
            $gaya <= 2 => $urutan < 2 + $gaya,
            $gaya === 3 => false,
            default => ($siswa->id + $modul->id) % 2 === 0,
        };
        $disentuh = $lulus || $gaya !== 4 || ($siswa->id + $modul->id) % 3 !== 0;

        if (! $disentuh && $gaya !== 3) {
            return;
        }

        if ($lulus) {
            $skor = 85 + (($siswa->id + $modul->id) % 16);
            $xp = $modul->xp_reward + (int) round($skor / 10);
            $hands = 2 + (($siswa->id + $urutan) % 3);
            $usaha = 1 + (($siswa->id + $urutan) % 3);
        } else {
            $skor = $gaya === 3 ? 30 + (($siswa->id + $urutan) % 30) : 40 + (($siswa->id + $urutan) % 30);
            $xp = (int) round($skor / 10);
            $hands = ($siswa->id + $urutan) % 3;
            $usaha = 1 + (($siswa->id + $urutan) % 2);
        }

        StudentProgress::updateOrCreate(
            ['student_id' => $siswa->id, 'module_id' => $modul->id],
            [
                'quiz_score' => $skor,
                'xp_earned' => $xp,
                'is_completed' => $lulus,
                'reading_done' => true,
                'hands_on_count' => $hands,
                'hands_on_xp_awarded' => $hands >= 2,
                'hands_on_last_at' => now()->subHours(2 + $urutan),
            ]
        );

        // Badge modul untuk yang lulus.
        if ($lulus && $modul->badge) {
            $siswa->badges()->syncWithoutDetaching([$modul->badge->id]);
        }

        // Riwayat percobaan menanjak (sekali seed saja per siswa).
        if (QuizAttempt::where('student_id', $siswa->id)->where('module_id', $modul->id)->doesntExist()) {
            $jalan = max(1, $usaha);
            for ($k = 1; $k <= $jalan; $k++) {
                $s = min(100, (int) round($skor * ($k / $jalan)));
                QuizAttempt::create([
                    'student_id' => $siswa->id,
                    'module_id' => $modul->id,
                    'score' => $s,
                    'answers' => array_fill(0, 15, ($siswa->id + $k) % 5),
                    'passed' => $s >= StudentProgress::PASS_SCORE,
                ]);
            }
        }
    }

    private function beriLencana(User $siswa, array $nama): void
    {
        $ids = Badge::whereIn('name', $nama)->pluck('id')->all();
        if ($ids) {
            $siswa->badges()->syncWithoutDetaching($ids);
        }
    }

    private function kodeUnik(): string
    {
        do {
            $code = 'KKA-' . Str::upper(Str::random(8));
        } while (Certificate::where('code', $code)->exists());

        return $code;
    }
}

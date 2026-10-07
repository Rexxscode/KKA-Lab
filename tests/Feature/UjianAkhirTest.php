<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Classroom;
use App\Models\FinalExamAttempt;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\StudentProgress;
use App\Models\User;
use App\Services\StudentModuleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UjianAkhirTest extends TestCase
{
    use RefreshDatabase;

    private function luluskanSemua(User $siswa): void
    {
        foreach (Module::whereNull('teacher_id')->orderBy('order')->get() as $m) {
            StudentProgress::updateOrCreate(
                ['student_id' => $siswa->id, 'module_id' => $m->id],
                ['quiz_score' => 85, 'xp_earned' => $m->xp_reward, 'is_completed' => true, 'reading_done' => true]
            );
        }
    }

    private function tambahSoal(Module $modul, int $jumlah, ?int $teacherId = null): void
    {
        for ($i = 0; $i < $jumlah; $i++) {
            Quiz::create([
                'module_id' => $modul->id,
                'teacher_id' => $teacherId,
                'question' => 'Soal tambahan ' . $modul->slug . '-' . $i,
                'options' => ['A', 'B', 'C', 'D', 'E'],
                'correct_index' => 0,
                'points' => 20,
            ]);
        }
    }

    public function test_ujian_akhir_terkunci_sebelum_6_modul_lulus(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($siswa)->get(route('siswa.ujian.index'))
            ->assertRedirect(route('siswa.bahan.index'));
        $this->actingAs($siswa)->get(route('siswa.ujian.show'))
            ->assertRedirect(route('siswa.bahan.index'));
    }

    public function test_latihan_default_keluar_20_dari_bank_40(): void
    {
        $this->seed(DatabaseSeeder::class);
        $modul = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->tambahSoal($modul, 25); // bank 15 + 25 = 40

        $layanan = app(StudentModuleService::class);
        $this->assertSame(40, $layanan->soalEfektif($modul)->count());
        $this->assertSame(20, $layanan->jumlahTampilLatihan($modul));

        $paket = $layanan->paketAcak($layanan->soalEfektif($modul), $layanan->jumlahTampilLatihan($modul));
        $this->assertCount(20, $paket);
    }

    public function test_bank_guru_min_25_blokir_latihan_dan_maks_30(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $modul = Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Guru', 'slug' => 'modul-guru',
            'order' => 1, 'description' => 'Tes', 'xp_reward' => 100,
        ]);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'Kelas', 'code' => '111111']);
        $kelas->students()->attach($siswa->id);
        $kelas->modules()->attach($modul->id, ['position' => 1]);
        StudentProgress::create([
            'student_id' => $siswa->id, 'module_id' => $modul->id,
            'quiz_score' => 0, 'xp_earned' => 0, 'is_completed' => false, 'reading_done' => true,
        ]);

        $layanan = app(StudentModuleService::class);

        // Bank 10 < 25: latihan diblokir.
        $this->tambahSoal($modul, 10, $guru->id);
        $this->assertTrue($layanan->bankGuruKurang($modul, $guru->id));
        $this->actingAs($siswa)->get(route('siswa.kuis.show', $modul->slug))
            ->assertRedirect(route('siswa.bahan.index'));

        // Bank 25: terbuka, keluar 15 acak.
        $this->tambahSoal($modul, 15, $guru->id);
        $this->assertFalse($layanan->bankGuruKurang($modul, $guru->id));
        $this->assertSame(15, $layanan->jumlahTampilLatihan($modul, $guru->id));

        // Maks 30: bulk ke-31 ditolak.
        $this->actingAs($guru)->post(route('guru.quiz.module.bulk', $modul), [
            'soal' => [
                ['question' => 'Kelebihan?', 'options' => ['A', 'B', 'C', 'D', 'E'], 'correct_letter' => 'A'],
                ['question' => 'Kelebihan 2?', 'options' => ['A', 'B', 'C', 'D', 'E'], 'correct_letter' => 'A'],
                ['question' => 'Kelebihan 3?', 'options' => ['A', 'B', 'C', 'D', 'E'], 'correct_letter' => 'A'],
                ['question' => 'Kelebihan 4?', 'options' => ['A', 'B', 'C', 'D', 'E'], 'correct_letter' => 'A'],
                ['question' => 'Kelebihan 5?', 'options' => ['A', 'B', 'C', 'D', 'E'], 'correct_letter' => 'A'],
                ['question' => 'Kelebihan 6?', 'options' => ['A', 'B', 'C', 'D', 'E'], 'correct_letter' => 'A'],
            ],
        ])->assertSessionHas('toast');
        $this->assertSame(25, $modul->quizzes()->where('teacher_id', $guru->id)->count());
    }

    public function test_bank_default_maks_40(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@kkalab.sch.id')->firstOrFail();
        $modul = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->tambahSoal($modul, 25); // bank pas 40

        $this->actingAs($admin)->post(route('guru.quiz.module.store', $modul), [
            'question' => 'Soal ke-41?',
            'options' => ['A', 'B', 'C', 'D', 'E'],
            'correct_letter' => 'A',
        ])->assertSessionHas('toast');
        $this->assertSame(40, $modul->quizzes()->whereNull('teacher_id')->count());
    }

    public function test_ujian_akhir_lulus_menerbitkan_sertifikat(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->luluskanSemua($siswa);

        // Halaman ujian terbuka setelah 6 modul lulus.
        $this->actingAs($siswa)->get(route('siswa.ujian.index'))->assertOk();
        $this->actingAs($siswa)->get(route('siswa.ujian.show'))->assertOk();

        // Jawab semua benar dari paket sesi.
        $paket = session('ujian_paket');
        $this->assertNotEmpty($paket);
        $tampil = app(StudentModuleService::class)->uraikanPaket($paket);
        $this->assertCount(40, $tampil);
        $answers = [];
        foreach ($tampil as $i => $t) {
            $answers[$i] = $t['correct'];
        }

        $this->actingAs($siswa)->post(route('siswa.ujian.submit'), ['answers' => $answers])
            ->assertRedirect(route('siswa.dashboard'));

        $attempt = FinalExamAttempt::where('student_id', $siswa->id)->latest()->firstOrFail();
        $this->assertTrue($attempt->passed);
        $this->assertSame(100, $attempt->score);
        $this->assertDatabaseHas('certificates', ['user_id' => $siswa->id]);
    }
}

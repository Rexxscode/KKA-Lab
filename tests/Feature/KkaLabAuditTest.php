<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\StudentProgress;
use App\Models\User;
use App\Services\StudentModuleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KkaLabAuditTest extends TestCase
{
    use RefreshDatabase;

    private function seedKka(): void
    {
        $this->seed(DatabaseSeeder::class);
    }

    public function test_enam_modul_default_dengan_15_soal_berpembahasan(): void
    {
        $this->seedKka();

        $modules = Module::whereNull('teacher_id')->orderBy('order')->get();
        $this->assertCount(6, $modules);
        $this->assertSame(['apa-itu-ai', 'latih-ai-mu', 'bias-data', 'etika-hoaks-ai', 'python-dasar-ai', 'data-logika-ai'], $modules->pluck('slug')->all());

        foreach ($modules as $m) {
            $this->assertCount(15, $m->quizzes()->whereNull('teacher_id')->get());
            $this->assertTrue($m->quizzes()->whereNull('teacher_id')->whereNull('explanation')->doesntExist());
        }
    }

    public function test_paket_acak_membatasi_15_soal_dan_memetakan_kunci_benar(): void
    {
        $this->seedKka();
        $svc = new StudentModuleService;

        $module = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $quizzes = $module->quizzes()->whereNull('teacher_id')->orderBy('id')->get();

        $paket = $svc->paketAcak($quizzes);
        $this->assertCount(15, $paket);

        $tampil = $svc->uraikanPaket($paket);
        $this->assertCount(15, $tampil);
        foreach ($tampil as $t) {
            // Opsi tampil adalah permutasi opsi asli & kunci menunjuk opsi yang sama.
            $this->assertCount(count($t['quiz']->options), $t['options']);
            $this->assertSame(
                $t['quiz']->options[$t['quiz']->correct_index],
                $t['options'][$t['correct']]
            );
        }
    }

    public function test_alur_kuis_acak_lulus_dan_masuk_banner_sertifikat_butuh_6_modul(): void
    {
        $this->seedKka();
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Modul pertama terbuka; baca bahan dulu.
        $modulBahan = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->actingAs($siswa)->withSession(['baca_mulai_' . $modulBahan->id => now()->subMinutes(5)->toIso8601String()])->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();

        // Buka kuis: paket acak tersimpan di sesi.
        $res = $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'));
        $res->assertOk();
        $module = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $paket = session('kuis_paket_' . $module->id);
        $this->assertNotEmpty($paket);
        $this->assertCount(15, $paket);

        // Jawab semua benar sesuai peta acak → lulus 100.
        $svc = new StudentModuleService;
        $tampil = $svc->uraikanPaket($paket);
        $answers = [];
        foreach ($tampil as $i => $t) {
            $answers[$i] = $t['correct'];
        }
        $this->actingAs($siswa)
            ->post(route('siswa.kuis.submit', 'apa-itu-ai'), ['answers' => $answers])
            ->assertRedirect(route('siswa.dashboard'));

        $progress = StudentProgress::where('student_id', $siswa->id)->where('module_id', $module->id)->firstOrFail();
        $this->assertTrue($progress->is_completed);
        $this->assertSame(100, (int) $progress->quiz_score);

        // Banner permanen muncul di dasbor.
        $this->actingAs($siswa)->get(route('siswa.dashboard'))->assertSee('Terakhir:');

        // Sertifikat belum layak: baru 1 dari 6 modul lulus.
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertSee('Belum memenuhi syarat');

        // Luluskan 5 modul sisanya langsung, sertifikat menjadi layak.
        foreach (Module::whereNull('teacher_id')->where('id', '!=', $module->id)->get() as $m) {
            StudentProgress::updateOrCreate(
                ['student_id' => $siswa->id, 'module_id' => $m->id],
                ['quiz_score' => 85, 'xp_earned' => $m->xp_reward, 'is_completed' => true, 'reading_done' => true]
            );
        }
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertSee('Sertifikat Kelulusan');
    }

    public function test_halaman_python_memuat_editor_dan_kuis_terbuka_berurutan(): void
    {
        $this->seedKka();
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Selesaikan modul 1-4 agar modul 5 terbuka.
        foreach (Module::whereIn('slug', ['apa-itu-ai', 'latih-ai-mu', 'bias-data', 'etika-hoaks-ai'])->get() as $m) {
            StudentProgress::updateOrCreate(
                ['student_id' => $siswa->id, 'module_id' => $m->id],
                ['quiz_score' => 90, 'xp_earned' => $m->xp_reward, 'is_completed' => true, 'reading_done' => true]
            );
        }

        $this->actingAs($siswa)->get(route('siswa.bahan.show', 'python-dasar-ai'))
            ->assertOk()
            ->assertSee('Praktik Python Asli')
            ->assertSee('pyodide-worker.js');

        $modulPy = Module::where('slug', 'python-dasar-ai')->firstOrFail();
        $this->actingAs($siswa)->withSession(['baca_mulai_' . $modulPy->id => now()->subMinutes(5)->toIso8601String()])->post(route('siswa.bahan.read', 'python-dasar-ai'))->assertRedirect();
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'python-dasar-ai'))->assertOk();

        // Modul 6 masih terkunci sebelum modul 5 lulus.
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'data-logika-ai'))->assertRedirect();
    }

    public function test_raporku_menampilkan_grafik_dan_saran(): void
    {
        $this->seedKka();
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($siswa)->get(route('siswa.raporku'))
            ->assertOk()
            ->assertSee('Raporku')
            ->assertSee('Grafik Skor per Modul')
            ->assertSee('Saran Otomatis');
    }

    public function test_dasbor_guru_menampilkan_blok_perlu_perhatian(): void
    {
        $this->seedKka();
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $kelas = \App\Models\Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $pos = 1;
        foreach (Module::whereNull('teacher_id')->orderBy('order')->get() as $dm) {
            $kelas->modules()->attach($dm->id, ['position' => $pos++]);
        }
        $kelas->students()->attach($siswa->id);

        $this->actingAs($guru)->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Perlu Perhatian')
            ->assertSee('XI RPL 1');
    }
}

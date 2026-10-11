<?php

namespace Tests\Feature;

use App\Models\AiChat;
use App\Models\Classroom;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\StudentProgress;
use App\Models\User;
use App\Services\StudentModuleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Ronde2Test extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_terpasang(): void
    {
        $res = $this->get('/');
        $res->assertOk();
        $this->assertSame('SAMEORIGIN', $res->headers->get('X-Frame-Options'));
        $this->assertSame('nosniff', $res->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString("default-src 'self'",
            $res->headers->get('Content-Security-Policy'));
        // CDN yang dipakai (Pyodide + Chart.js) wajib diizinkan connect-src.
        // Host bobot model TF sudah dibuang dari CSP (fitur kamera dihapus).
        $csp = $res->headers->get('Content-Security-Policy');
        $this->assertStringContainsString('https://cdn.jsdelivr.net', $csp);
        $this->assertStringNotContainsString('https://tfhub.dev', $csp);
        $this->assertStringNotContainsString('https://www.kaggle.com', $csp);
        $this->assertStringNotContainsString('https://storage.googleapis.com', $csp);
        $this->assertStringContainsString('camera=()', $res->headers->get('Permissions-Policy'));
    }

    public function test_landing_hanya_modul_default_dan_angka_sinkron(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Buatan Guru', 'slug' => 'modul-guru',
            'order' => 99, 'description' => 'Tidak boleh tampil di landing.', 'xp_reward' => 999,
        ]);

        $res = $this->get('/');
        $res->assertOk();
        // Tepat 6 modul default, modul guru tidak bocor ke landing.
        $res->assertSee('6 Modul KKA', false);
        $res->assertSee('Cara Kerja Model AI', false);
        $res->assertDontSee('Modul Buatan Guru', false);
        $res->assertDontSee('Latih AI-mu', false);
        // Angka sinkron dengan DB: pokok 950, maks 1010, 90 soal.
        $res->assertSee('Hingga 1010', false);
        $res->assertSee('>90<', false);
        // Tak ada lagi UI kamera/deteksi di landing.
        $res->assertDontSee('kkaLandingCam', false);
        $res->assertDontSee('Aktifkan Kamera', false);
        $res->assertDontSee('Coba AI Langsung', false);
    }
    public function test_lepas_modul_tak_terpasang_404(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $modul = Module::whereNull('teacher_id')->firstOrFail();

        $this->actingAs($guru)
            ->delete(route('guru.kelas.modul.lepas', [$kelas, $modul]))
            ->assertNotFound();
    }

    public function test_unduh_bukti_hanya_milik_guru(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)->get(route('admin.bukti.unduh', $siswa))->assertNotFound();
    }

    public function test_reset_link_pesan_generik_tanpa_enumerasi(): void
    {
        $this->post('/forgot-password', ['email' => 'tidak-terdaftar@example.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');
    }

    public function test_regenerasi_kode_menghanguskan_kode_lama(): void
    {        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);

        $this->actingAs($guru)->patch(route('guru.kelas.kode', $kelas))->assertRedirect();
        $baru = $kelas->fresh()->code;
        $this->assertNotSame('123456', $baru);

        // Kode lama ditolak.
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => '123456'])->assertRedirect();
        $this->assertDatabaseMissing('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $siswa->id]);

        // Kode baru diterima.
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $baru])->assertRedirect();
        $this->assertDatabaseHas('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $siswa->id]);
    }

    public function test_tandai_baca_menolak_tanpa_jeda_baca(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Tanpa membuka bahan dulu: ditolak, progres tidak tercatat.
        $this->actingAs($siswa)->post(route('siswa.bahan.read', 'apa-itu-ai'))
            ->assertRedirect(route('siswa.bahan.show', 'apa-itu-ai'));
        $this->assertDatabaseMissing('student_progress', [
            'student_id' => $siswa->id,
            'module_id' => Module::where('slug', 'apa-itu-ai')->firstOrFail()->id,
        ]);
    }

    public function test_xp_handson_tidak_hilang_saat_submit_kuis(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $module = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        StudentProgress::updateOrCreate(
            ['student_id' => $siswa->id, 'module_id' => $module->id],
            ['quiz_score' => 0, 'xp_earned' => 20, 'is_completed' => false, 'reading_done' => true, 'hands_on_count' => 2, 'hands_on_xp_awarded' => true]
        );

        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();
        $tampil = (new StudentModuleService)->uraikanPaket(session('kuis_paket_' . $module->id));
        $answers = [];
        foreach ($tampil as $i => $t) {
            $answers[$i] = $t['correct'];
        }
        $this->actingAs($siswa)->post(route('siswa.kuis.submit', 'apa-itu-ai'), ['answers' => $answers])
            ->assertRedirect(route('siswa.dashboard'));

        // Lulus 100: xp_reward + 10, bonus hands-on 20 wajib ikut.
        $this->assertSame(20 + $module->xp_reward + 10, (int) StudentProgress::where('student_id', $siswa->id)->where('module_id', $module->id)->firstOrFail()->xp_earned);
    }

    public function test_reset_progres_membersihkan_jejak_turunan(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $module = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $kelas->modules()->attach($module->id, ['position' => 1]);
        $kelas->students()->attach($siswa->id);
        StudentProgress::updateOrCreate(
            ['student_id' => $siswa->id, 'module_id' => $module->id],
            ['quiz_score' => 90, 'xp_earned' => 50, 'is_completed' => true, 'reading_done' => true]
        );
        \App\Models\QuizAttempt::create(['student_id' => $siswa->id, 'module_id' => $module->id, 'score' => 90, 'answers' => [0], 'passed' => true]);
        $badge = \App\Models\Badge::firstOrCreate(['module_id' => $module->id], ['name' => 'Badge Tes']);
        $siswa->badges()->syncWithoutDetaching([$badge->id]);
        \App\Models\Feedback::create(['classroom_id' => $kelas->id, 'student_id' => $siswa->id, 'module_id' => $module->id, 'body' => 'Bagus, pertahankan!']);

        $this->actingAs($guru)->delete(route('guru.kelas.siswa.reset', [$kelas, $siswa]))->assertRedirect();

        $this->assertDatabaseMissing('student_progress', ['student_id' => $siswa->id, 'module_id' => $module->id]);
        $this->assertDatabaseMissing('quiz_attempts', ['student_id' => $siswa->id, 'module_id' => $module->id]);
        $this->assertDatabaseMissing('badge_user', ['badge_id' => $badge->id, 'user_id' => $siswa->id]);
        $this->assertDatabaseMissing('feedbacks', ['classroom_id' => $kelas->id, 'student_id' => $siswa->id, 'module_id' => $module->id]);
    }

    public function test_refresh_kuis_memakai_paket_yang_sama(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $modulBahan = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->actingAs($siswa)->withSession(['baca_mulai_' . $modulBahan->id => now()->subMinutes(5)->toIso8601String()])->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();
        $p1 = session('kuis_paket_' . Module::where('slug', 'apa-itu-ai')->firstOrFail()->id);
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();
        $p2 = session('kuis_paket_' . Module::where('slug', 'apa-itu-ai')->firstOrFail()->id);

        $this->assertNotEmpty($p1);
        $this->assertSame($p1, $p2);
    }

    public function test_submit_kuis_menolak_jawaban_tak_lengkap(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $modulBahan = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->actingAs($siswa)->withSession(['baca_mulai_' . $modulBahan->id => now()->subMinutes(5)->toIso8601String()])->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();

        $this->actingAs($siswa)->post(route('siswa.kuis.submit', 'apa-itu-ai'), ['answers' => [0, 1]])
            ->assertSessionHasErrors('answers');
    }

    public function test_submit_kuis_lewat_waktu_ditolak(): void
    {        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $modulBahan = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->actingAs($siswa)->withSession(['baca_mulai_' . $modulBahan->id => now()->subMinutes(5)->toIso8601String()])->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();
        $module = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $paket = session('kuis_paket_' . $module->id);
        $this->assertNotEmpty($paket);

        $this->actingAs($siswa)->withSession([
            'kuis_paket_' . $module->id => $paket,
            'kuis_mulai_' . $module->id => now()->subHour()->toIso8601String(),
        ])->post(route('siswa.kuis.submit', 'apa-itu-ai'), ['answers' => array_fill(0, 15, 0)])
            ->assertRedirect(route('siswa.kuis.show', 'apa-itu-ai'));
    }

    public function test_papan_peringkat_urut_xp(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $juara = User::factory()->create(['role' => 'siswa', 'name' => 'Si Juara']);
        $santai = User::factory()->create(['role' => 'siswa', 'name' => 'Si Santai']);
        $kelas->students()->attach([$juara->id, $santai->id]);
        $modul = Module::whereNull('teacher_id')->firstOrFail();
        StudentProgress::create(['student_id' => $juara->id, 'module_id' => $modul->id, 'quiz_score' => 100, 'xp_earned' => 150, 'is_completed' => true]);
        StudentProgress::create(['student_id' => $santai->id, 'module_id' => $modul->id, 'quiz_score' => 80, 'xp_earned' => 40, 'is_completed' => true]);

        $this->actingAs($santai)->get(route('siswa.leaderboard'))
            ->assertOk()->assertSeeInOrder(['Si Juara', 'Si Santai']);
    }

    public function test_log_ai_hanya_milik_siswa_sendiri_kecuali_admin(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $guruLain = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $admin = User::factory()->create(['role' => 'admin']);
        $muridku = User::factory()->create(['role' => 'siswa']);
        $muridOrang = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($muridku->id);
        $kelasLain = Classroom::create(['teacher_id' => $guruLain->id, 'name' => 'XI RPL 2', 'code' => '654321']);
        $kelasLain->students()->attach($muridOrang->id);
        AiChat::create(['user_id' => $muridku->id, 'message' => 'tanya-muridku', 'reply' => 'jawab']);
        AiChat::create(['user_id' => $muridOrang->id, 'message' => 'tanya-murid-orang', 'reply' => 'jawab']);

        $this->actingAs($guru)->get(route('guru.ai.index'))
            ->assertOk()->assertSee('tanya-muridku')->assertDontSee('tanya-murid-orang');
        $this->actingAs($admin)->get(route('admin.ai.index'))
            ->assertOk()->assertSee('tanya-muridku')->assertSee('tanya-murid-orang');
    }

    public function test_duplikat_modul_menyalin_soal(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $asli = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $jumlahSoal = $asli->quizzes()->whereNull('teacher_id')->count();
        $this->assertGreaterThan(0, $jumlahSoal);

        $this->actingAs($guru)->post(route('guru.modul.duplikat', $asli))->assertRedirect();

        $salin = Module::where('teacher_id', $guru->id)->where('title', $asli->title . ' (salinan)')->firstOrFail();
        $this->assertSame($jumlahSoal, $salin->quizzes()->where('teacher_id', $guru->id)->count());
    }

    public function test_kelola_kelas_kick_reset_arsip(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $pendaftar = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($siswa->id);
        $modul = Module::firstOrCreate(['slug' => 'modul-tes'], ['title' => 'Modul Tes', 'order' => 99, 'xp_reward' => 10]);
        $kelas->modules()->attach($modul->id, ['position' => 1]);
        StudentProgress::create(['student_id' => $siswa->id, 'module_id' => $modul->id, 'quiz_score' => 90, 'xp_earned' => 20, 'is_completed' => true]);

        // Reset progres.
        $this->actingAs($guru)->delete(route('guru.kelas.siswa.reset', [$kelas, $siswa]))->assertRedirect();
        $this->assertDatabaseMissing('student_progress', ['student_id' => $siswa->id, 'module_id' => $modul->id]);

        // Kick.
        $this->actingAs($guru)->delete(route('guru.kelas.siswa.kick', [$kelas, $siswa]))->assertRedirect();
        $this->assertDatabaseMissing('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $siswa->id]);

        // Arsip: gabung diblokir.
        $this->actingAs($guru)->patch(route('guru.kelas.arsip', $kelas))->assertRedirect();
        $this->assertSame('arsip', $kelas->fresh()->status);
        $this->actingAs($pendaftar)->post(route('siswa.join'), ['code' => '123456'])->assertRedirect();
        $this->assertDatabaseMissing('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $pendaftar->id]);

        // Buka lagi: gabung jalan.
        $this->actingAs($guru)->patch(route('guru.kelas.arsip', $kelas))->assertRedirect();
        $this->actingAs($pendaftar)->post(route('siswa.join'), ['code' => '123456'])->assertRedirect();
        $this->assertDatabaseHas('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $pendaftar->id]);
    }

    public function test_percobaan_kuis_tercatat_dan_tampil_di_riwayat(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $modulBahan = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $this->actingAs($siswa)->withSession(['baca_mulai_' . $modulBahan->id => now()->subMinutes(5)->toIso8601String()])->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();
        $module = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $paket = session('kuis_paket_' . $module->id);
        $tampil = (new StudentModuleService)->uraikanPaket($paket);
        $answers = [];
        foreach ($tampil as $i => $t) {
            $answers[$i] = $t['correct'];
        }
        $this->actingAs($siswa)->post(route('siswa.kuis.submit', 'apa-itu-ai'), ['answers' => $answers])
            ->assertRedirect(route('siswa.dashboard'));

        $this->assertDatabaseHas('quiz_attempts', ['student_id' => $siswa->id, 'module_id' => $module->id, 'score' => 100, 'passed' => true]);
        $this->actingAs($siswa)->get(route('siswa.dashboard'))->assertSee('Riwayat Kuis');
        $this->actingAs($siswa)->get(route('siswa.raporku'))->assertSee('Terbaik: 100');
    }

    public function test_umpan_balik_guru_tampil_di_rapor_siswa(): void
    {        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $luar = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($siswa->id);
        $modul = Module::firstOrCreate(['slug' => 'modul-tes'], ['title' => 'Modul Tes', 'order' => 99, 'xp_reward' => 10]);
        $kelas->modules()->attach($modul->id, ['position' => 1]);

        $this->actingAs($guru)->post(route('guru.kelas.siswa.feedback', [$kelas, $siswa]), [
            'module_id' => $modul->id,
            'body' => 'Bagus! Tingkatkan kecepatan.',
        ])->assertRedirect();
        $this->assertDatabaseHas('feedbacks', ['student_id' => $siswa->id, 'body' => 'Bagus! Tingkatkan kecepatan.']);

        $this->actingAs($siswa)->get(route('siswa.raporku'))->assertSee('Bagus! Tingkatkan kecepatan.');

        // Bukan anggota: 404.
        $this->actingAs($guru)->post(route('guru.kelas.siswa.feedback', [$kelas, $luar]), [
            'module_id' => $modul->id,
            'body' => 'x',
        ])->assertNotFound();
    }

    public function test_halaman_bahan_materi_punya_varian_dark(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        foreach (['apa-itu-ai', 'latih-ai-mu', 'bias-data', 'etika-hoaks-ai', 'python-dasar-ai', 'data-logika-ai'] as $slug) {
            $this->actingAs($siswa)->get(route('siswa.bahan.show', $slug))
                ->assertOk()
                ->assertSee('dark:text-white', false)
                ->assertSee('dark:bg-emerald-500/10', false);
            // Luluskan agar modul berikutnya terbuka.
            $m = Module::where('slug', $slug)->firstOrFail();
            StudentProgress::updateOrCreate(
                ['student_id' => $siswa->id, 'module_id' => $m->id],
                ['quiz_score' => 90, 'xp_earned' => $m->xp_reward, 'is_completed' => true, 'reading_done' => true]
            );
        }
    }

    public function test_password_lemah_ditolak_saat_register(): void
    {
        $this->post('/register', [
            'name' => 'Lemah',
            'email' => 'lemah@example.com',
            'password' => 'abcdefgh',
            'password_confirmation' => 'abcdefgh',
            'role' => 'siswa',
        ])->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_fork_soal_menyimpan_origin_id(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $modul = Module::where('slug', 'apa-itu-ai')->firstOrFail();

        $this->actingAs($guru)->post(route('guru.quiz.module.store', $modul), [
            'question' => 'Soal tambahan guru?',
            'options' => ['A1', 'B1', 'C1', 'D1', 'E1'],
            'correct_letter' => 'A',
        ])->assertRedirect();

        // Fork otomatis: salinan soal default menunjuk ke soal asal,
        // sedangkan soal baru buatan guru tetap origin null.
        $milik = Quiz::where('module_id', $modul->id)->where('teacher_id', $guru->id);
        $this->assertSame(15, (clone $milik)->whereNotNull('origin_id')->count());
        $this->assertSame(1, (clone $milik)->whereNull('origin_id')->count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\AiChat;
use App\Models\Classroom;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
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
        $this->assertStringContainsString("default-src 'self'", $res->headers->get('Content-Security-Policy'));
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

    public function test_refresh_kuis_memakai_paket_yang_sama(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($siswa)->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
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

        $this->actingAs($siswa)->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
        $this->actingAs($siswa)->get(route('siswa.kuis.show', 'apa-itu-ai'))->assertOk();

        $this->actingAs($siswa)->post(route('siswa.kuis.submit', 'apa-itu-ai'), ['answers' => [0, 1]])
            ->assertSessionHasErrors('answers');
    }

    public function test_submit_kuis_lewat_waktu_ditolak(): void
    {        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($siswa)->post(route('siswa.bahan.read', 'apa-itu-ai'))->assertRedirect();
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
}

<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Module;
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
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
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
}

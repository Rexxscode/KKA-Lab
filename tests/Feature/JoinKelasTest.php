<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\User;
use App\Services\StudentModuleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JoinKelasTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_join_kelas_dengan_kode(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Guru buat kelas.
        $this->actingAs($guru)->post(route('guru.kelas.store'), ['name' => 'XI RPL 3'])
            ->assertRedirect();
        $kelas = Classroom::where('teacher_id', $guru->id)->firstOrFail();
        $this->assertSame(6, strlen($kelas->code));
        $this->assertSame('buka', $kelas->status);

        // Siswa join dengan kode.
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $kelas->code])
            ->assertRedirect();
        $this->assertDatabaseHas('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $siswa->id]);

        // Kelas aktif terbaca.
        $aktif = app(StudentModuleService::class)->kelasAktif($siswa->fresh());
        $this->assertNotNull($aktif);
        $this->assertSame($kelas->id, $aktif->id);

        // Dashboard menampilkan kelas aktif.
        $this->actingAs($siswa)->get(route('siswa.dashboard'))
            ->assertOk()->assertSee($kelas->name);
    }

    public function test_join_kode_salah_menampilkan_pesan(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Kode kurang dari 6 digit: error validasi bahasa Indonesia.
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => '12345'])
            ->assertSessionHasErrors('code');

        // Kode 6 digit tapi tidak ada: toast error, tidak bergabung.
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => '000000'])
            ->assertSessionHas('toast');
        $this->assertDatabaseMissing('classroom_user', ['student_id' => $siswa->id]);

        // Kode ber-spasi tetap diterima setelah normalisasi.
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $this->actingAs($guru)->post(route('guru.kelas.store'), ['name' => 'XI RPL 3']);
        $kelas = Classroom::where('teacher_id', $guru->id)->firstOrFail();
        $spasi = substr($kelas->code, 0, 3) . ' ' . substr($kelas->code, 3);
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $spasi])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('classroom_user', ['classroom_id' => $kelas->id, 'student_id' => $siswa->id]);

        // Kode berhuruf ditolak validasi (kode asli selalu 6 digit angka).
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => 'abc123'])
            ->assertSessionHasErrors('code');
    }

    public function test_join_dobel_tidak_menduplikat(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($guru)->post(route('guru.kelas.store'), ['name' => 'XI RPL 3']);
        $kelas = Classroom::where('teacher_id', $guru->id)->firstOrFail();

        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $kelas->code])
            ->assertRedirect();
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $kelas->code])
            ->assertRedirect();

        $this->assertSame(1, $siswa->classrooms()->whereKey($kelas->id)->count());
        $this->assertSame(1, \DB::table('classroom_user')->where('classroom_id', $kelas->id)->where('student_id', $siswa->id)->count());
    }

    public function test_pindah_kelas_mengganti_kelas_aktif(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($guru)->post(route('guru.kelas.store'), ['name' => 'Kelas A']);
        $a = Classroom::where('teacher_id', $guru->id)->orderBy('id')->firstOrFail();
        $this->actingAs($guru)->post(route('guru.kelas.store'), ['name' => 'Kelas B']);
        $b = Classroom::where('teacher_id', $guru->id)->orderByDesc('id')->firstOrFail();

        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $a->code]);
        $this->actingAs($siswa)->post(route('siswa.join'), ['code' => $b->code])
            ->assertSessionHas('toast');

        $aktif = app(StudentModuleService::class)->kelasAktif($siswa->fresh());
        $this->assertNotNull($aktif);
        $this->assertSame($b->id, $aktif->id);
    }
}

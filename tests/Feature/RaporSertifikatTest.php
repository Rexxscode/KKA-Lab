<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Classroom;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporSertifikatTest extends TestCase
{
    use RefreshDatabase;

    private function seedKka(): void
    {
        $this->seed(DatabaseSeeder::class);
    }

    private function luluskanSemua(User $siswa): void
    {
        foreach (Module::whereNull('teacher_id')->orderBy('order')->get() as $m) {
            StudentProgress::updateOrCreate(
                ['student_id' => $siswa->id, 'module_id' => $m->id],
                ['quiz_score' => 85, 'xp_earned' => $m->xp_reward, 'is_completed' => true, 'reading_done' => true]
            );
        }
    }

    public function test_sertifikat_terbit_dengan_kode_stabil_dan_bisa_diverifikasi_publik(): void
    {
        $this->seedKka();
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Belum lulus semua: belum ada sertifikat.
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertSee('Belum memenuhi syarat');
        $this->assertDatabaseMissing('certificates', ['user_id' => $siswa->id]);

        $this->luluskanSemua($siswa);

        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk()->assertSee('Kode Sertifikat');
        $cert = Certificate::where('user_id', $siswa->id)->firstOrFail();
        $this->assertMatchesRegularExpression('/^KKA-[A-Z0-9]{8}$/', $cert->code);

        // Kode stabil: kunjungan ulang tidak membuat baris/kode baru.
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk();
        $this->assertSame($cert->id, Certificate::where('user_id', $siswa->id)->firstOrFail()->id);

        // Verifikasi publik tanpa login.
        $this->get(route('sertifikat.verifikasi', $cert->code))
            ->assertOk()->assertSee('Sertifikat Valid')->assertSee($siswa->name);
        $this->get(route('sertifikat.verifikasi', 'KKA-TIDAKADA'))->assertNotFound();
    }

    public function test_raporku_cetak_dan_rapor_guru_per_siswa(): void
    {
        $this->seedKka();
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $luar = User::factory()->create(['role' => 'siswa']);

        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($siswa->id);

        $this->actingAs($siswa)->get(route('siswa.raporku.cetak'))
            ->assertOk()->assertSee('Rapor Belajar');

        $this->actingAs($guru)->get(route('guru.kelas.siswa.show', [$kelas, $siswa]))
            ->assertOk()->assertSee('Rapor ' . $siswa->name);

        // Siswa di luar kelas: 404.
        $this->actingAs($guru)->get(route('guru.kelas.siswa.show', [$kelas, $luar]))->assertNotFound();

        // Guru lain: 403.
        $guruLain = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $this->actingAs($guruLain)->get(route('guru.kelas.siswa.show', [$kelas, $siswa]))->assertForbidden();
    }
}

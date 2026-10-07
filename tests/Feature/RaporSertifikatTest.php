<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Classroom;
use App\Models\FinalExamAttempt;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    private function luluskanUjian(User $siswa, int $score = 85): void
    {
        FinalExamAttempt::create([
            'student_id' => $siswa->id,
            'score' => $score,
            'answers' => [],
            'passed' => $score >= 80,
        ]);
    }

    public function test_sertifikat_terbit_dengan_kode_stabil_dan_bisa_diverifikasi_publik(): void
    {
        $this->seedKka();
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Belum lulus semua: belum ada sertifikat.
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertSee('Belum memenuhi syarat');
        $this->assertDatabaseMissing('certificates', ['user_id' => $siswa->id]);

        $this->luluskanSemua($siswa);

        // 6 modul lulus saja belum cukup: wajib lulus ujian akhir dulu.
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk()->assertSee('Belum memenuhi syarat');
        $this->assertDatabaseMissing('certificates', ['user_id' => $siswa->id]);

        $this->luluskanUjian($siswa);

        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk()->assertSee('Kode Sertifikat');
        $cert = Certificate::where('user_id', $siswa->id)->firstOrFail();
        $this->assertMatchesRegularExpression('/^KBT-[A-Z0-9]{8}$/', $cert->code);

        // Kode stabil: kunjungan ulang tidak membuat baris/kode baru.
        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk();
        $this->assertSame($cert->id, Certificate::where('user_id', $siswa->id)->firstOrFail()->id);

        // Verifikasi publik tanpa login.
        $this->get(route('sertifikat.verifikasi', $cert->code))
            ->assertOk()->assertSee('Sertifikat Valid')->assertSee($siswa->name);
        $this->get(route('sertifikat.verifikasi', 'KBT-TIDAKADA'))->assertNotFound();
    }

    public function test_raporku_cetak_dan_rapor_guru_per_siswa(): void
    {        $this->seedKka();
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

    public function test_sertifikat_mencatat_super_admin_dan_guru_kelas_aktif(): void
    {
        $this->seedKka();
        $superAdmin = User::where('email', 'admin@kkalab.sch.id')->firstOrFail();
        $superAdmin->update(['name' => 'Super Admin']);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif', 'name' => 'Guru Wali']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($siswa->id);
        $this->luluskanSemua($siswa);
        $this->luluskanUjian($siswa);

        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk();

        $cert = Certificate::where('user_id', $siswa->id)->firstOrFail();
        $this->assertSame($superAdmin->id, $cert->super_admin_id);
        $this->assertSame('Super Admin', $cert->super_admin_nama);
        $this->assertSame($guru->id, $cert->guru_id);
        $this->assertSame('Guru Wali', $cert->guru_nama);

        $this->actingAs($siswa)->get(route('siswa.sertifikat'))
            ->assertSee('Super Admin', false)->assertSee('Guru Wali', false);
    }

    public function test_sertifikat_tanpa_kelas_guru_dikosongkan(): void
    {
        $this->seedKka();
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->luluskanSemua($siswa);
        $this->luluskanUjian($siswa);

        $this->actingAs($siswa)->get(route('siswa.sertifikat'))->assertOk();

        $cert = Certificate::where('user_id', $siswa->id)->firstOrFail();
        $this->assertNotNull($cert->super_admin_id);
        $this->assertNull($cert->guru_id);
        $this->assertNull($cert->guru_nama);
    }

    public function test_tanda_tangan_bisa_diupload_guru_ditolak_untuk_siswa(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->post(route('profile.ttd'), [
            'tanda_tangan' => UploadedFile::fake()->image('ttd.png'),
        ])->assertRedirect(route('profile.edit'));
        $this->assertNotNull($guru->fresh()->signature_path);
        Storage::disk('public')->assertExists($guru->fresh()->signature_path);

        $this->actingAs($siswa)->post(route('profile.ttd'), [
            'tanda_tangan' => UploadedFile::fake()->image('ttd.png'),
        ])->assertForbidden();
    }

    public function test_halaman_status_tanda_tangan_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.tanda-tangan'))
            ->assertOk()->assertSee('Super Admin');
    }
}

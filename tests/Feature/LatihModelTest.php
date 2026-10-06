<?php

namespace Tests\Feature;

use App\Models\GlobalModel;
use App\Models\Module;
use App\Models\StudentModel;
use App\Models\StudentProgress;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LatihModelTest extends TestCase
{
    use RefreshDatabase;

    private function bukaAkses(User $siswa): void
    {
        $m1 = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        StudentProgress::updateOrCreate(
            ['student_id' => $siswa->id, 'module_id' => $m1->id],
            ['quiz_score' => 90, 'xp_earned' => 100, 'is_completed' => true, 'reading_done' => true]
        );
    }

    private function datasetValid(array $labels = ['Botol', 'Buku'], int $perLabel = 20): array
    {
        $contoh = [];
        foreach ($labels as $label) {
            $contoh[$label] = array_fill(0, $perLabel, base64_encode(random_bytes(24)));
        }

        return ['backbone' => 'mobilenet', 'dim' => 6, 'labels' => $labels, 'contoh' => $contoh];
    }

    public function test_publish_menyimpan_antrean_pending(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->bukaAkses($siswa);

        $this->actingAs($siswa)->postJson(route('siswa.latih.publish'), [
            'name' => 'Deteksi Botol vs Buku',
            'labels' => ['Botol', 'Buku'],
            'dataset' => $this->datasetValid(),
        ])->assertCreated()->assertJson(['message' => 'Model "Deteksi Botol vs Buku" masuk antre! Gurumu akan memeriksa sebelum tayang global.']);

        $this->assertDatabaseHas('student_models', [
            'student_id' => $siswa->id,
            'name' => 'Deteksi Botol vs Buku',
            'status' => StudentModel::STATUS_PENDING,
        ]);
    }

    public function test_publish_menolak_contoh_kurang(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->bukaAkses($siswa);

        $this->actingAs($siswa)->postJson(route('siswa.latih.publish'), [
            'name' => 'Model Kurang',
            'labels' => ['Botol', 'Buku'],
            'dataset' => $this->datasetValid(['Botol', 'Buku'], 5),
        ])->assertStatus(422)->assertJsonValidationErrors('dataset');

        $this->assertDatabaseMissing('student_models', ['student_id' => $siswa->id]);
    }

    public function test_publish_ditolak_saat_terkunci_dan_untuk_admin(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Modul 1 belum lulus: terkunci.
        $this->actingAs($siswa)->postJson(route('siswa.latih.publish'), [
            'name' => 'X',
            'labels' => ['A', 'B'],
            'dataset' => $this->datasetValid(['A', 'B']),
        ])->assertForbidden();

        // Pratinjau admin tidak mencatat.
        $this->actingAs($admin)->postJson(route('siswa.latih.publish'), [
            'name' => 'X',
            'labels' => ['A', 'B'],
            'dataset' => $this->datasetValid(['A', 'B']),
        ])->assertForbidden();
        $this->assertDatabaseMissing('student_models', ['student_id' => $admin->id]);
    }

    public function test_publish_dibatasi_lima_antrean(): void
    {        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->bukaAkses($siswa);

        for ($i = 0; $i < StudentModel::MAKS_PENDING; $i++) {
            StudentModel::create([
                'student_id' => $siswa->id,
                'name' => "Model $i",
                'labels' => ['A', 'B'],
                'dataset' => $this->datasetValid(['A', 'B']),
                'status' => StudentModel::STATUS_PENDING,
            ]);
        }

        $this->actingAs($siswa)->postJson(route('siswa.latih.publish'), [
            'name' => 'Kelebihan',
            'labels' => ['A', 'B'],
            'dataset' => $this->datasetValid(['A', 'B']),
        ])->assertStatus(429);
    }

    private function pendingModel(User $guru, User $siswa, string $nama = 'Model Bagus'): StudentModel
    {
        $kelas = \App\Models\Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($siswa->id);

        return StudentModel::create([
            'student_id' => $siswa->id,
            'name' => $nama,
            'labels' => ['Botol', 'Buku'],
            'dataset' => $this->datasetValid(),
            'status' => StudentModel::STATUS_PENDING,
        ]);
    }

    public function test_setujui_menayangkan_global_dan_memberi_xp_sekali(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $model = $this->pendingModel($guru, $siswa);

        $this->actingAs($guru)->post(route('guru.model.setujui', $model), ['accuracy' => 92])
            ->assertRedirect();

        $this->assertSame(StudentModel::STATUS_APPROVED, $model->fresh()->status);
        $global = GlobalModel::aktif();
        $this->assertNotNull($global);
        $this->assertSame('Model Bagus', $global->name);

        $progress = StudentProgress::where('student_id', $siswa->id)->firstOrFail();
        $this->assertTrue((bool) $progress->training_xp_awarded);
        $this->assertSame(30, (int) $progress->xp_earned);

        // Setujui ulang: XP tidak ganda.
        $this->actingAs($guru)->post(route('guru.model.setujui', $model))->assertRedirect();
        $this->assertSame(30, (int) $progress->fresh()->xp_earned);
    }

    public function test_guru_lain_tidak_bisa_moderasi(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $guruLain = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $model = $this->pendingModel($guru, $siswa);

        $this->actingAs($guruLain)->post(route('guru.model.setujui', $model))->assertForbidden();
        $this->actingAs($guruLain)->post(route('guru.model.tolak', $model), ['note' => 'x'])->assertForbidden();
        $this->assertSame(StudentModel::STATUS_PENDING, $model->fresh()->status);
    }

    public function test_tolak_butuh_catatan_dan_tanpa_xp(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $model = $this->pendingModel($guru, $siswa);

        $this->actingAs($guru)->post(route('guru.model.tolak', $model))->assertSessionHasErrors('note');

        $this->actingAs($guru)->post(route('guru.model.tolak', $model), ['note' => 'Contoh kurang bervariasi.'])
            ->assertRedirect();
        $this->assertSame(StudentModel::STATUS_REJECTED, $model->fresh()->status);
        $this->assertNull(GlobalModel::aktif());
        $this->assertDatabaseMissing('student_progress', ['student_id' => $siswa->id]);
    }

    public function test_halaman_model_global(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($siswa)->get(route('siswa.model.global'))
            ->assertOk()->assertSee('Belum ada model global');

        GlobalModel::create([
            'name' => 'Model Perdana',
            'slug' => 'model-perdana',
            'labels' => ['Botol', 'Buku'],
            'dataset' => $this->datasetValid(),
            'is_active' => true,
        ]);

        $this->actingAs($siswa)->get(route('siswa.model.global'))
            ->assertOk()->assertSee('Model Perdana', false);
    }
}

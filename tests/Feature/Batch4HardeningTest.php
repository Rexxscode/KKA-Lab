<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\GlobalModel;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\StudentModel;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Batch4HardeningTest extends TestCase
{
    use RefreshDatabase;

    private function soalBaru(string $tanya = 'Soal baru?'): array
    {
        return [
            'question' => $tanya,
            'options' => ['Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E'],
            'correct_letter' => 'A',
        ];
    }

    public function test_guru_store_fork_atomik_di_modul_default(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $modul = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        $default = $modul->quizzes()->whereNull('teacher_id')->count();
        $this->assertGreaterThan(0, $default);

        $this->actingAs($guru)->post(route('guru.quiz.module.store', $modul), $this->soalBaru())
            ->assertRedirect();

        // Fork lengkap + 1 soal baru milik guru, default tak tersentuh.
        $this->assertSame($default + 1, $modul->quizzes()->where('teacher_id', $guru->id)->count());
        $this->assertSame($default, $modul->quizzes()->whereNull('teacher_id')->count());
    }

    public function test_guru_destroy_diblokir_bila_bank_tinggal_di_bawah_minimal(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $modul = Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Hapus', 'slug' => 'modul-hapus',
            'order' => 99, 'description' => 'Tes', 'xp_reward' => 100,
        ]);
        for ($i = 1; $i <= 25; $i++) {
            Quiz::create([
                'module_id' => $modul->id, 'teacher_id' => $guru->id,
                'question' => "Soal {$i}?", 'options' => ['A', 'B', 'C', 'D', 'E'],
                'correct_index' => 0, 'points' => 20,
            ]);
        }
        $target = $modul->quizzes()->where('teacher_id', $guru->id)->orderBy('id')->firstOrFail();

        $this->actingAs($guru)->delete(route('guru.quiz.destroy', $target))
            ->assertSessionHasErrors('quiz');
        $this->assertSame(25, $modul->quizzes()->where('teacher_id', $guru->id)->count());
    }

    public function test_setujui_ulang_tidak_menduplikat_global(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => '123456']);
        $kelas->students()->attach($siswa->id);
        $contoh = ['Botol' => array_fill(0, 20, base64_encode('a')), 'Buku' => array_fill(0, 20, base64_encode('b'))];
        $model = StudentModel::create([
            'student_id' => $siswa->id, 'name' => 'Model Sekali',
            'labels' => ['Botol', 'Buku'],
            'dataset' => ['backbone' => 'mobilenet', 'dim' => 6, 'labels' => ['Botol', 'Buku'], 'contoh' => $contoh],
            'status' => StudentModel::STATUS_PENDING,
        ]);

        $this->actingAs($guru)->post(route('guru.model.setujui', $model), ['accuracy' => 80])->assertRedirect();
        $this->assertSame(1, GlobalModel::where('is_active', true)->count());

        // Approve ulang: ditolak halus, global tetap 1.
        $this->actingAs($guru)->post(route('guru.model.setujui', $model))->assertRedirect();
        $this->assertSame(1, GlobalModel::where('is_active', true)->count());
    }

    public function test_setujui_menolak_bobot_rusak(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 2', 'code' => '654321']);
        $kelas->students()->attach($siswa->id);
        $contoh = ['Botol' => array_fill(0, 20, base64_encode('a')), 'Buku' => array_fill(0, 20, base64_encode('b'))];
        $model = StudentModel::create([
            'student_id' => $siswa->id, 'name' => 'Model Racun',
            'labels' => ['Botol', 'Buku'],
            'dataset' => ['backbone' => 'mobilenet', 'dim' => 6, 'labels' => ['Botol', 'Buku'], 'contoh' => $contoh],
            'weights' => ['tensors' => [['bentuk' => 'rusak']]],
            'status' => StudentModel::STATUS_PENDING,
        ]);

        $this->actingAs($guru)->post(route('guru.model.setujui', $model), ['accuracy' => 90])->assertRedirect();
        $this->assertSame(StudentModel::STATUS_PENDING, $model->fresh()->status);
        $this->assertNull(GlobalModel::aktif());
    }
}

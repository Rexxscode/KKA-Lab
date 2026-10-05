<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectDetectionTest extends TestCase
{
    use RefreshDatabase;

    private function bukaModulDua(User $siswa): void
    {
        $satu = Module::where('slug', 'apa-itu-ai')->firstOrFail();
        StudentProgress::updateOrCreate(
            ['student_id' => $siswa->id, 'module_id' => $satu->id],
            ['quiz_score' => 90, 'xp_earned' => $satu->xp_reward, 'is_completed' => true, 'reading_done' => true]
        );
        $this->actingAs($siswa)->post(route('siswa.bahan.read', 'latih-ai-mu'))->assertRedirect();
    }

    public function test_halaman_modul_dua_memakai_widget_deteksi_bukan_knn(): void
    {
        $this->seed(DatabaseSeeder::class);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $this->bukaModulDua($siswa);

        $res = $this->actingAs($siswa)->get(route('siswa.bahan.show', 'latih-ai-mu'));
        $res->assertOk();
        $res->assertSee('Mulai Deteksi', false);
        $res->assertSee('Ambang confidence', false);
        $res->assertSee('data-testid="det-widget"', false);
        $res->assertSee('object-detector', false);
        $res->assertDontSee('kkaKnN', false);
        $res->assertDontSee('Muat Dataset Contoh', false);
        $res->assertDontSee('knn-classifier', false);
    }

    public function test_aset_detector_ada_dan_memakai_coco_ssd(): void
    {
        $modul = base_path('resources/js/ai/object-detector.js');
        $util = base_path('resources/js/ai/detect-utils.js');
        $this->assertFileExists($modul);
        $this->assertFileExists($util);

        $isi = file_get_contents($modul);
        $this->assertStringContainsString('coco-ssd', $isi);
        $this->assertStringContainsString('requestAnimationFrame', $isi);
        $this->assertStringContainsString('getUserMedia', $isi);
        $this->assertStringNotContainsString('knnClassifier', $isi);
        $this->assertStringNotContainsString('polaTensor', $isi);

        $this->assertStringContainsString('COCO_LABELS_ID', file_get_contents($util));
    }

    public function test_soal_default_modul_dua_tidak_lagi_membahas_knn(): void
    {
        $this->seed(DatabaseSeeder::class);
        $module = Module::where('slug', 'latih-ai-mu')->firstOrFail();

        $soal = $module->quizzes()->whereNull('teacher_id')->pluck('question');
        $this->assertCount(15, $soal);
        foreach ($soal as $q) {
            $this->assertStringNotContainsStringIgnoringCase('KNN', $q);
        }
        $this->assertTrue($soal->contains(fn ($q) => str_contains($q, 'Bounding box')));
        $this->assertTrue($soal->contains(fn ($q) => str_contains($q, 'COCO-SSD')));
    }
}

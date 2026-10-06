<?php

namespace Tests\Feature;

use App\Ai\Agents\TutorAgent;
use App\Ai\Tools\LearningProgressTool;
use App\Ai\Tools\StudentProfileTool;
use App\Models\Classroom;
use App\Models\Module;
use App\Models\StudentProgress;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request as ToolRequest;
use Tests\TestCase;

class AiToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_tool_hanya_data_aman(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $siswa = User::factory()->create(['role' => 'siswa', 'name' => 'Budi Santoso', 'email' => 'budi@example.com']);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'XI RPL 1', 'code' => 'RAHASIA123']);
        $kelas->students()->attach($siswa->id);

        $out = (string) (new StudentProfileTool($siswa))->handle(new ToolRequest);

        $this->assertStringContainsString('Budi Santoso', $out);
        $this->assertStringContainsString('XI RPL 1', $out);
        $this->assertStringNotContainsString('budi@example.com', $out);
        $this->assertStringNotContainsString('RAHASIA123', $out);
        $this->assertStringNotContainsString('password', strtolower($out));
    }

    public function test_progress_tool_hanya_milik_sendiri(): void
    {
        $this->seed(DatabaseSeeder::class);
        $modul = Module::first();
        $a = User::factory()->create(['role' => 'siswa', 'name' => 'Andi', 'email' => 'andi@example.com']);
        $b = User::factory()->create(['role' => 'siswa', 'name' => 'Siti', 'email' => 'siti@example.com']);
        StudentProgress::create(['student_id' => $a->id, 'module_id' => $modul->id, 'is_completed' => true, 'quiz_score' => 90, 'xp_earned' => 50]);
        StudentProgress::create(['student_id' => $b->id, 'module_id' => $modul->id, 'is_completed' => false, 'quiz_score' => 40, 'xp_earned' => 10]);

        $outA = (string) (new LearningProgressTool($a))->handle(new ToolRequest);

        $this->assertStringContainsString('selesai', $outA);
        $this->assertStringContainsString('90', $outA);
        $this->assertStringNotContainsString('Siti', $outA);
        $this->assertStringNotContainsString('siti@example.com', $outA);
        $this->assertStringNotContainsString('andi@example.com', $outA);
    }

    public function test_agent_dengan_tools_bisa_difake(): void
    {        $siswa = User::factory()->create(['role' => 'siswa']);
        TutorAgent::fake(['Halo! Ini jawaban AI.']);

        $res = (new TutorAgent(user: $siswa))->prompt('apa itu AI?');

        $this->assertSame('Halo! Ini jawaban AI.', $res->text);
        TutorAgent::assertPromptedTimes(1);
    }

    public function test_instructions_tutor_aman_dan_berbahasa_indonesia(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $instruksi = (string) (new TutorAgent(user: $siswa, moduleContext: 'latih-ai-mu'))->instructions();

        $this->assertStringContainsString('Bahasa Indonesia', $instruksi);
        $this->assertStringContainsString('latih-ai-mu', $instruksi);
        $this->assertStringContainsString('DILARANG', $instruksi);
        $this->assertStringContainsString('jawaban kuis', $instruksi);
    }
}

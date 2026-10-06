<?php

namespace Database\Seeders;

use App\Models\AiChat;
use App\Models\Classroom;
use App\Models\Feedback;
use App\Models\User;
use Database\Seeders\Data\ObrolanAiData;
use Database\Seeders\Data\UmpanBalikData;
use Illuminate\Database\Seeder;

class InteraksiSeeder extends Seeder
{
    public function run(): void
    {
        $this->obrolanAi();
        $this->umpanBalik();
    }

    private function obrolanAi(): void
    {
        $siswas = User::where('role', 'siswa')
            ->where('email', 'like', '%@siswa.kkalab.sch.id')
            ->orderBy('id')->take(12)->get();
        if ($siswas->isEmpty()) {
            return;
        }

        $obrolan = ObrolanAiData::daftar();
        foreach ($obrolan as $i => $chat) {
            // Tiap sampel dipakai 3 siswa berbeda → ±39 baris.
            for ($k = 0; $k < 3; $k++) {
                $siswa = $siswas[($i * 3 + $k) % $siswas->count()];
                AiChat::firstOrCreate(
                    ['user_id' => $siswa->id, 'message' => $chat['message']],
                    [
                        'module_slug' => $chat['module'],
                        'reply' => $chat['reply'],
                        'created_at' => now()->subDays(($i + $k) % 14)->subHours($k),
                        'updated_at' => now()->subDays(($i + $k) % 14)->subHours($k),
                    ]
                );
            }
        }
    }

    private function umpanBalik(): void
    {
        $catatan = UmpanBalikData::daftar();
        $n = 0;

        foreach (Classroom::with('students')->where('status', 'buka')->orderBy('id')->get() as $kelas) {
            $moduls = $kelas->modules()->orderByPivot('position')->take(3)->get();
            foreach ($kelas->students->take(5) as $siswa) {
                foreach ($moduls as $j => $modul) {
                    Feedback::updateOrCreate(
                        ['classroom_id' => $kelas->id, 'student_id' => $siswa->id, 'module_id' => $modul->id],
                        ['body' => $catatan[$n++ % count($catatan)]]
                    );
                    if ($n >= 25) {
                        return;
                    }
                }
            }
        }
    }
}

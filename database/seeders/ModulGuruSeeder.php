<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\Data\ModulGuruData;
use Illuminate\Database\Seeder;

class ModulGuruSeeder extends Seeder
{
    public function run(): void
    {
        $pemilik = [
            'guru@kkalab.sch.id' => 4,
            'budi.santoso@kkalab.sch.id' => 4,
            'dewi.lestari@kkalab.sch.id' => 3,
            'andi.pratama@kkalab.sch.id' => 3,
        ];

        $antrean = [];
        foreach ($pemilik as $email => $jatah) {
            $guru = User::where('email', $email)->first();
            if (! $guru) {
                continue;
            }
            for ($i = 0; $i < $jatah; $i++) {
                $antrean[] = $guru;
            }
        }

        foreach (ModulGuruData::daftar() as $i => $m) {
            /** @var User $guru */
            $guru = $antrean[$i % count($antrean)];

            $module = Module::firstOrCreate(
                ['slug' => $m['slug']],
                [
                    'teacher_id' => $guru->id,
                    'title' => $m['title'],
                    'order' => (int) (Module::where('teacher_id', $guru->id)->max('order') ?? 0) + 1,
                    'description' => $m['description'],
                    'materi' => $m['materi'],
                    'xp_reward' => $m['xp_reward'],
                ]
            );

            foreach ($m['quizzes'] as $q) {
                Quiz::firstOrCreate(
                    ['module_id' => $module->id, 'teacher_id' => $guru->id, 'question' => $q['q']],
                    [
                        'options' => $q['o'],
                        'correct_index' => $q['c'],
                        'points' => 20,
                        'explanation' => $q['e'],
                        'origin_id' => null,
                    ]
                );
            }

            Badge::firstOrCreate(
                ['module_id' => $module->id],
                ['name' => 'Badge ' . $m['title'], 'icon_path' => null]
            );

            // Tempel ke semua kelas guru (posisi akhir).
            foreach ($guru->taughtClassrooms as $kelas) {
                if (! $kelas->modules()->where('modules.id', $module->id)->exists()) {
                    $pos = (int) ($kelas->modules()->max('classroom_module.position') ?? 0) + 1;
                    $kelas->modules()->attach($module->id, ['position' => $pos]);
                }
            }
        }

        // Badge pencapaian umum (tanpa modul).
        foreach (['Pembelajar Cepat', 'Penanya Kritis', 'Rajin Praktik', 'Bintang Kelas', 'Pantang Menyerah', 'Perfeksionis', 'Eksplorer AI', 'Detektif Data', 'Penjaga Etika', 'Pythonista', 'Ahli Logika', 'Maraton Belajar', 'Tuntas Semua Modul', 'Bintang Sertifikat'] as $nama) {
            Badge::firstOrCreate(['name' => $nama, 'module_id' => null], ['icon_path' => null]);
        }
    }
}

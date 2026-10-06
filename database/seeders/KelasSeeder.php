<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $guruDemo = User::where('email', 'guru@kkalab.sch.id')->firstOrFail();
        $guru = fn (string $email) => User::where('email', $email)->firstOrFail();

        $daftar = [
            ['name' => 'XI RPL 1', 'code' => '100001', 'guru' => $guruDemo, 'status' => 'buka'],
            ['name' => 'XI RPL 2', 'code' => '100002', 'guru' => $guru('budi.santoso@kkalab.sch.id'), 'status' => 'buka'],
            ['name' => 'X TKJ 1', 'code' => '100003', 'guru' => $guru('dewi.lestari@kkalab.sch.id'), 'status' => 'buka'],
            ['name' => 'XI MM 1', 'code' => '100004', 'guru' => $guru('andi.pratama@kkalab.sch.id'), 'status' => 'buka'],
            ['name' => 'XII RPL 1 (Lintas Minat)', 'code' => '100005', 'guru' => $guruDemo, 'status' => 'buka'],
            ['name' => 'X RPL 1 (Arsip 2024)', 'code' => '100006', 'guru' => $guru('budi.santoso@kkalab.sch.id'), 'status' => 'arsip'],
        ];

        $kelasModels = [];
        foreach ($daftar as $k) {
            $kelasModels[] = Classroom::firstOrCreate(
                ['code' => $k['code']],
                ['name' => $k['name'], 'teacher_id' => $k['guru']->id, 'status' => $k['status']]
            );
        }

        // Sebar 30 siswa: tiap kelas 8 anggota, sebagian ikut 2 kelas.
        $siswas = User::where('role', 'siswa')->where('email', 'like', '%@siswa.kkalab.sch.id')->orderBy('id')->get();
        $pola = [0, 1, 2, 3, 4, 0, 1, 2, 3, 4];
        foreach ($siswas as $i => $siswa) {
            $kelasModels[$pola[$i % 10]]->students()->syncWithoutDetaching([$siswa->id]);
            if ($i % 3 === 0) {
                $kelasModels[($pola[$i % 10] + 1) % 5]->students()->syncWithoutDetaching([$siswa->id]);
            }
        }

        // Tempel 6 modul default (posisi 1-6): 2 tenggat lewat, 2 dekat, sisanya tanpa tenggat.
        $defaults = Module::whereNull('teacher_id')->orderBy('order')->get();
        foreach ($kelasModels as $kelas) {
            $pos = 1;
            foreach ($defaults as $urutan => $dm) {
                $pivot = ['position' => $pos++];
                if ($urutan < 2) {
                    $pivot['due_at'] = now()->subDays(3 - $urutan);
                } elseif ($urutan < 4) {
                    $pivot['due_at'] = now()->addDays(5 + $urutan);
                }
                $ada = $kelas->modules()->where('modules.id', $dm->id)->first();
                if ($ada) {
                    $kelas->modules()->updateExistingPivot($dm->id, $pivot);
                } else {
                    $kelas->modules()->attach($dm->id, $pivot);
                }
            }
        }

        // Pengaman: kelas yang belum punya susunan dapat modul default + custom gurunya.
        foreach (Classroom::whereDoesntHave('modules')->get() as $kelas) {
            $pos = 1;
            foreach ($defaults as $dm) {
                $kelas->modules()->syncWithoutDetaching([$dm->id => ['position' => $pos++]]);
            }
            foreach (Module::where('teacher_id', $kelas->teacher_id)->orderBy('order')->get() as $cm) {
                $kelas->modules()->syncWithoutDetaching([$cm->id => ['position' => $pos++]]);
            }
        }
    }
}

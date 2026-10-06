<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModulDefaultSeeder::class,
            PenggunaSeeder::class,
            KelasSeeder::class,
            ModulGuruSeeder::class,
            ProgresSeeder::class,
            InteraksiSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (kredensial tetap, password: password).
        $this->demo('Admin KKA Lab', 'admin@kkalab.sch.id', 'admin');
        $this->demo('Bu Marissa (Guru)', 'guru@kkalab.sch.id', 'guru');
        $this->demo('Siswa Demo', 'siswa@kkalab.sch.id', 'siswa');

        // Guru tambahan: 3 aktif + 1 menunggu verifikasi NPK.
        $gurus = [
            ['Pak Budi Santoso', 'budi.santoso@kkalab.sch.id', 'aktif'],
            ['Bu Dewi Lestari', 'dewi.lestari@kkalab.sch.id', 'aktif'],
            ['Pak Andi Pratama', 'andi.pratama@kkalab.sch.id', 'aktif'],
            ['Bu Sari Wulandari', 'sari.wulandari@kkalab.sch.id', 'pending'],
        ];
        foreach ($gurus as [$nama, $email, $status]) {
            $guru = User::firstOrCreate(
                ['email' => $email],
                ['name' => $nama, 'password' => Hash::make('password'), 'email_verified_at' => now()]
            );
            $guru->forceFill(['role' => 'guru', 'status' => $status])->save();
        }

        // Contoh berkas bukti NPK agar tombol unduh di admin tidak 404.
        Storage::disk('local')->put('bukti-npk/contoh-npk.pdf', $this->pdfContoh());
        User::where('email', 'sari.wulandari@kkalab.sch.id')
            ->whereNull('bukti_npk_path')
            ->update(['bukti_npk_path' => 'bukti-npk/contoh-npk.pdf']);

        // 30 siswa dengan nama Indonesia.
        foreach (self::namaSiswa() as $i => [$namaDepan, $namaBelakang]) {
            $email = strtolower($namaDepan . '.' . $namaBelakang . ($i + 1) . '@siswa.kkalab.sch.id');
            $siswa = User::firstOrCreate(
                ['email' => $email],
                ['name' => $namaDepan . ' ' . $namaBelakang, 'password' => Hash::make('password'), 'email_verified_at' => now()]
            );
            $siswa->forceFill(['role' => 'siswa', 'status' => 'aktif'])->save();
        }
    }

    private function demo(string $nama, string $email, string $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $nama, 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $user->forceFill(['role' => $role, 'status' => 'aktif'])->save();
    }

    private function pdfContoh(): string
    {
        return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 300 100]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n4 0 obj<</Length 60>>stream\nBT /F1 14 Tf 40 55 Td (Contoh Bukti NPK - SEED) Tj ET\nendstream\nendobj\n5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
    }

    /** @return array<int, array{0: string, 1: string}> */
    public static function namaSiswa(): array
    {
        return [
            ['Ayu', 'Putri'], ['Budi', 'Hartono'], ['Citra', 'Ayunda'], ['Dimas', 'Saputra'],
            ['Eka', 'Wulandari'], ['Fajar', 'Ramadhan'], ['Gita', 'Permata'], ['Hendra', 'Gunawan'],
            ['Intan', 'Pratiwi'], ['Joko', 'Susilo'], ['Kirana', 'Dewi'], ['Lukman', 'Hakim'],
            ['Maya', 'Anggraini'], ['Nadia', 'Safitri'], ['Oscar', 'Wijaya'], ['Putri', 'Maharani'],
            ['Rian', 'Ardiansyah'], ['Sinta', 'Dewi'], ['Tono', 'Sugiarto'], ['Umi', 'Kalsum'],
            ['Vina', 'Melati'], ['Wahyu', 'Hidayat'], ['Yoga', 'Saputra'], ['Zahra', 'Aulia'],
            ['Deni', 'Kurniawan'], ['Fitri', 'Handayani'], ['Galih', 'Prakoso'], ['Hana', 'Marwah'],
            ['Irfan', 'Maulana'], ['Kartika', 'Sari'],
        ];
    }
}

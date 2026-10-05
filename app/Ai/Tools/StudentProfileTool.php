<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Profil ringkas pemilik chat: nama, peran, dan kelas.
 * SENGAJA tidak mengembalikan email, kata sandi, kode gabung kelas,
 * path file, maupun data milik pengguna lain.
 */
class StudentProfileTool implements Tool
{
    public function __construct(private User $user) {}

    public function name(): string
    {
        return 'get_student_profile';
    }

    public function description(): Stringable|string
    {
        return 'Lihat profil ringkas siswa yang sedang chat: nama, peran, dan nama kelasnya. Hanya data miliknya sendiri.';
    }

    public function handle(Request $request): Stringable|string
    {
        $kelas = $this->user->classrooms()->pluck('classrooms.name')->implode(', ');
        $peran = match ($this->user->role) {
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'admin' => 'Admin',
            default => (string) $this->user->role,
        };

        return 'Nama: '.$this->user->name.'; Peran: '.$peran.'; Kelas: '.($kelas ?: 'belum gabung kelas').'.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}

<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModuleGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_tidak_bisa_otak_atik_modul_guru(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@kkalab.sch.id')->firstOrFail();
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $modulGuru = Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Guru', 'slug' => 'modul-guru-guard',
            'order' => 1, 'description' => 'Tes', 'materi' => 'Materi', 'xp_reward' => 100,
        ]);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'Kelas', 'code' => '222222']);
        $kelas->modules()->attach($modulGuru->id, ['position' => 1]);

        // Ubah / hapus / duplikat modul guru → 403.
        $this->actingAs($admin)->patch(route('guru.modul.update', $modulGuru), [
            'title' => 'Diubah Admin', 'materi' => 'X',
        ])->assertForbidden();
        $this->actingAs($admin)->delete(route('guru.modul.destroy', $modulGuru))->assertForbidden();
        $this->actingAs($admin)->post(route('guru.modul.duplikat', $modulGuru))->assertForbidden();

        // Lepas / geser modul guru di kelas → 403.
        $this->actingAs($admin)->delete(route('guru.kelas.modul.lepas', [$kelas, $modulGuru]))->assertForbidden();
        $this->actingAs($admin)->patch(route('guru.kelas.modul.naik', [$kelas, $modulGuru]))->assertForbidden();

        // Tempel modul guru ke kelas → 403.
        $kelas2 = Classroom::create(['teacher_id' => $guru->id, 'name' => 'Kelas 2', 'code' => '333333']);
        $this->actingAs($admin)->post(route('guru.kelas.modul.tempel', $kelas2), ['module_id' => $modulGuru->id])->assertForbidden();

        $this->assertSame('Modul Guru', $modulGuru->fresh()->title);
        $this->assertTrue($kelas->modules()->where('modules.id', $modulGuru->id)->exists());
    }

    public function test_admin_bisa_lihat_modul_guru_lewat_kelas_saja(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@kkalab.sch.id')->firstOrFail();
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $modulGuru = Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Guru Lihat', 'slug' => 'modul-guru-lihat',
            'order' => 1, 'description' => 'Deskripsi', 'materi' => 'Materi guru.', 'xp_reward' => 100,
        ]);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'Kelas', 'code' => '555555']);
        $kelas->modules()->attach($modulGuru->id, ['position' => 1]);

        // Lihat lewat kelas: OK, materi tampil, tanpa form ubah.
        $this->actingAs($admin)->get(route('guru.kelas.modul.show', [$kelas, $modulGuru]))
            ->assertOk()->assertSee('Materi guru.', false)->assertDontSee('Simpan Perubahan', false);

        // Modul yang TIDAK terpasang di kelas: 404 (tak bisa intip via URL).
        $modulLain = Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Lain', 'slug' => 'modul-guru-lain',
            'order' => 2, 'description' => 'X', 'materi' => 'Y', 'xp_reward' => 100,
        ]);
        $this->actingAs($admin)->get(route('guru.kelas.modul.show', [$kelas, $modulLain]))->assertNotFound();

        // Guru lain yang bukan pemilik kelas: 403.
        $guruLain = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $this->actingAs($guruLain)->get(route('guru.kelas.modul.show', [$kelas, $modulGuru]))->assertForbidden();
    }

    public function test_admin_tetap_bisa_kelola_modul_default(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@kkalab.sch.id')->firstOrFail();
        $modul = Module::where('slug', 'apa-itu-ai')->firstOrFail();

        // Tambah modul default baru.
        $this->actingAs($admin)->post(route('guru.modul.store'), [
            'title' => 'Modul Default Baru', 'materi' => 'Materi default.',
        ])->assertRedirect();
        $baru = Module::where('title', 'Modul Default Baru')->firstOrFail();
        $this->assertNull($baru->teacher_id);

        // Ubah modul default.
        $this->actingAs($admin)->patch(route('guru.modul.update', $modul), [
            'title' => $modul->title, 'materi' => 'Materi diperbarui admin.',
        ])->assertRedirect();
        $this->assertSame('Materi diperbarui admin.', $modul->fresh()->materi);
    }

    public function test_guru_tetap_bisa_atur_modul_sendiri(): void
    {
        $this->seed(DatabaseSeeder::class);
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $modulGuru = Module::create([
            'teacher_id' => $guru->id, 'title' => 'Modul Guru', 'slug' => 'modul-guru-atur',
            'order' => 1, 'description' => 'Tes', 'materi' => 'Materi', 'xp_reward' => 100,
        ]);
        $kelas = Classroom::create(['teacher_id' => $guru->id, 'name' => 'Kelas', 'code' => '444444']);
        $kelas->modules()->attach($modulGuru->id, ['position' => 1]);

        $this->actingAs($guru)->delete(route('guru.kelas.modul.lepas', [$kelas, $modulGuru]))->assertRedirect();
        $this->assertFalse($kelas->modules()->where('modules.id', $modulGuru->id)->exists());
    }
}

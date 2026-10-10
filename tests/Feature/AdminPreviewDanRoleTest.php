<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPreviewDanRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_biasa_ditolak_buat_admin(): void
    {
        User::factory()->create(['role' => 'admin', 'email' => 'admin@kkalab.sch.id']);
        $biasa = User::factory()->create(['role' => 'admin', 'email' => 'biasa@x.id']);

        $this->actingAs($biasa)->post(route('admin.users.store'), [
            'name' => 'X', 'email' => 'newadmin@x.id',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'newadmin@x.id']);
    }

    public function test_super_admin_bisa_buat_guru(): void
    {
        $super = User::factory()->create(['role' => 'admin', 'email' => 'admin@kkalab.sch.id']);

        $this->actingAs($super)->post(route('admin.users.store'), [
            'name' => 'X', 'email' => 'guru-baru@x.id',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'guru',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'guru-baru@x.id', 'role' => 'guru']);
    }

    public function test_admin_ditolak_gabung_kelas(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@kkalab.sch.id']);

        $this->actingAs($admin)->post(route('siswa.join'), ['code' => '123456'])->assertForbidden();
    }

    public function test_pratinjau_siswa_read_only(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@kkalab.sch.id')->first();
        $siswa = User::where('email', 'siswa@kkalab.sch.id')->first();

        $this->actingAs($admin)->get(route('admin.pratinjau.index'))->assertOk()->assertSee('Pratinjau Siswa');
        $res = $this->actingAs($admin)->get(route('admin.pratinjau.show', $siswa))->assertOk();
        $res->assertSee('Mode pratinjau admin');
        // Tombol aksi dikunci agar tidak keluar dari konteks preview.
        $res->assertSee('Dinonaktifkan dalam pratinjau', false);
        $res->assertSee('Tombol Bahan/Kuis dinonaktifkan dalam pratinjau', false);
        // Tidak ada progres baru milik admin yang tercipta.
        $this->assertDatabaseMissing('student_progress', ['student_id' => $admin->id]);
        // Admin langsung buka dashboard siswa diarahkan ke pratinjau via banner.
        $this->actingAs($admin)->get(route('siswa.dashboard'))->assertOk()->assertSee('Gunakan Pratinjau Siswa');
    }
}

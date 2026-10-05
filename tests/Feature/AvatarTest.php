<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_bisa_diupload_dan_tampil(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('foto.jpg'),
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $path = $user->fresh()->avatar_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $this->actingAs($user)->get(route('profile.edit'))->assertOk()->assertSee($path, false);
    }

    public function test_avatar_baru_menghapus_yang_lama(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('lama.jpg'),
        ]);
        $lama = $user->fresh()->avatar_path;

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('baru.jpg'),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($lama);
        Storage::disk('public')->assertExists($user->fresh()->avatar_path);
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->create('jahat.php', 100, 'application/x-php'),
        ])->assertSessionHasErrors('avatar');

        $this->assertNull($user->fresh()->avatar_path);
    }
}

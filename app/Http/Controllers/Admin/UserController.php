<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:guru,admin,siswa',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => "Akun {$data['role']} {$data['email']} berhasil dibuat!"]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa reset password sendiri dari sini.');
        abort_if($user->isAdmin(), 403, 'Akun admin tidak bisa di-reset dari sini.');

        $user->update(['password' => Hash::make($data['password'])]);

        // Keluarkan semua sesi aktif akun ini agar password lama tidak bisa dipakai lagi.
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
        Log::info('admin.reset_password', ['actor' => $request->user()->id, 'target' => $user->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Password {$user->email} berhasil di-reset. Sesi lain milik akun ini ikut dikeluarkan."]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa menghapus akun sendiri.');
        abort_if($user->isAdmin(), 403, 'Akun admin tidak bisa dihapus dari sini.');

        $email = $user->email;
        if ($user->bukti_npk_path) {
            Storage::disk('local')->delete($user->bukti_npk_path);
        }
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }
        $user->delete();
        Log::info('admin.hapus_akun', ['actor' => $request->user()->id, 'target_email' => $email]);

        return back()->with('toast', ['type' => 'success', 'message' => "Akun {$email} dihapus."]);
    }

    public function setujui(Request $request, User $user)
    {
        abort_unless($user->isGuru() && $user->status === 'pending', 404);
        $user->update(['status' => 'aktif']);
        Log::info('admin.setujui_guru', ['actor' => $request->user()->id, 'target' => $user->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Akun guru {$user->email} disetujui! Fitur gurunya sudah terbuka."]);
    }

    public function tolak(Request $request, User $user)
    {
        abort_unless($user->isGuru() && $user->status === 'pending', 404);
        $user->update(['status' => 'ditolak']);
        Log::info('admin.tolak_guru', ['actor' => $request->user()->id, 'target' => $user->id]);

        return back()->with('toast', ['type' => 'success', 'message' => "Pengajuan {$user->email} ditolak. Ia tidak bisa masuk lagi."]);
    }

    public function unduhBukti(Request $request, User $user)
    {
        abort_unless($user->isGuru() && $user->bukti_npk_path && Storage::disk('local')->exists($user->bukti_npk_path), 404);
        Log::info('admin.unduh_bukti', ['actor' => $request->user()->id, 'target' => $user->id]);

        return Storage::disk('local')->download($user->bukti_npk_path, 'bukti-npk-' . $user->id . '.' . pathinfo($user->bukti_npk_path, PATHINFO_EXTENSION));
    }
}

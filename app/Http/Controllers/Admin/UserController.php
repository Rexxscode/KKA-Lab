<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('toast', ['type' => 'success', 'message' => "Password {$user->email} berhasil di-reset."]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa menghapus akun sendiri.');

        $email = $user->email;
        $user->delete();

        return back()->with('toast', ['type' => 'success', 'message' => "Akun {$email} dihapus."]);
    }
}

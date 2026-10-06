<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Registrasi publik HANYA siswa. Guru dibuatkan admin (lihat Admin\UserController@store).
            'role' => ['nullable', 'in:siswa'],
        ]);

        // Tolak tegas bila ada yang memaksa role=guru via request manual.
        if ($request->filled('role') && $request->role !== 'siswa') {
            abort(403, 'Pendaftaran guru hanya melalui administrator.');
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Hak akses diisi eksplisit (role/status tidak fillable).
        $user->role = 'siswa';
        $user->status = 'aktif';
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect(route('siswa.dashboard', absolute: false));
    }
}

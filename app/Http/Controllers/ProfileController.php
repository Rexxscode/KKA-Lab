<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * XP yang dibutuhkan untuk naik satu level di hero profil.
     * Disatukan di sini agar view tidak memakai magic number.
     */
    public const LEVEL_XP = 500;

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->loadMissing(['classrooms', 'badges', 'progress']);

        $peran = match ($user->role) {
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'admin' => 'Admin',
            default => ucfirst((string) $user->role),
        };

        // Statistik belajar hanya relevan untuk siswa.
        // Guru/admin dapat ringkasan peran agar hero tidak menampilkan XP/progres milik orang lain.
        $isSiswa = $user->isSiswa();
        $modulList = $isSiswa ? Module::orderBy('order')->get() : collect();
        $progresMap = $isSiswa
            ? $user->progress->keyBy('module_id')
            : collect();
        $totalXp = $isSiswa ? (int) $user->progress->sum('xp_earned') : 0;
        $selesai = $isSiswa ? $progresMap->where('is_completed', true)->count() : 0;
        $totalModul = $modulList->count();
        // Rata-rata skor kuis yang pernah dikerjakan (termasuk yang belum lulus).
        $akurasi = $isSiswa ? $progresMap->whereNotNull('quiz_score')->avg('quiz_score') : null;

        $level = intdiv($totalXp, self::LEVEL_XP) + 1;
        $levelPct = (int) round(($totalXp % self::LEVEL_XP) / self::LEVEL_XP * 100);
        $levelName = match (true) {
            $level <= 1 => 'AI Rookie',
            $level === 2 => 'AI Explorer',
            $level === 3 => 'AI Builder',
            $level === 4 => 'AI Creator',
            default => 'AI Master',
        };

        // Batasi daftar kelas agar badge hero tidak overflow bila user ikut banyak kelas.
        $kelasNames = $user->classrooms->pluck('name')->take(3)->implode(', ');
        $kelasLebih = $user->classrooms->count() > 3 ? $user->classrooms->count() - 3 : 0;
        $kelasDiajar = $user->isGuru() ? $user->taughtClassrooms()->count() : 0;

        return view('profile.edit', [
            'user' => $user,
            'peran' => $peran,
            'isSiswa' => $isSiswa,
            'kelasNames' => $kelasNames,
            'kelasLebih' => $kelasLebih,
            'kelasDiajar' => $kelasDiajar,
            'modulList' => $modulList,
            'progresMap' => $progresMap,
            'totalXp' => $totalXp,
            'selesai' => $selesai,
            'totalModul' => $totalModul,
            'akurasi' => $akurasi,
            'level' => $level,
            'levelPct' => $levelPct,
            'levelName' => $levelName,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['avatar', 'hapus_avatar']));

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('hapus_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Simpan gambar tanda tangan (canvas) untuk guru & super admin.
     */
    public function tandaTangan(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isGuru() || $user->isSuperAdmin(), 403);

        $data = $request->validate([
            'tanda_tangan' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }
        $user->update(['signature_path' => $request->file('tanda_tangan')->storeAs(
            'signatures', 'ttd-' . $user->id . '.' . $request->file('tanda_tangan')->extension(), 'public'
        )]);

        return Redirect::route('profile.edit')->with('status', 'ttd-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

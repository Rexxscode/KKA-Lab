<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // 'role' & 'status' SENGAJA tidak fillable: hak akses hanya boleh
    // diubah lewat assignment eksplisit di controller terpercaya
    // (register, admin), bukan via mass-assignment input user.
    protected $fillable = ['name', 'email', 'password', 'bukti_npk_path', 'avatar_path'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function guruAktif(): bool
    {
        return $this->isGuru() && $this->status === 'aktif';
    }

    public function taughtClassrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_user', 'student_id', 'classroom_id')->withTimestamps();
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class, 'student_id');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_user')->withTimestamps();
    }

    public function totalXp(): int
    {
        return (int) $this->progress()->sum('xp_earned');
    }

    /**
     * URL foto profil publik, null bila belum pasang.
     */
    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::url($this->avatar_path) : null;
    }
}

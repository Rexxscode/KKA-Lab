<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id', 'code', 'issued_at',
        'super_admin_id', 'guru_id', 'super_admin_nama', 'guru_nama',
        'super_admin_sign_path', 'guru_sign_path',
    ];

    protected $casts = ['issued_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function superAdmin()
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}

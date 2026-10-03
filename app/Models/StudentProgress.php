<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $table = 'student_progress';

    protected $fillable = ['student_id', 'module_id', 'is_completed', 'quiz_score', 'xp_earned'];

    protected $casts = ['is_completed' => 'boolean'];

    public const PASS_SCORE = 80;

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

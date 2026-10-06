<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $table = 'student_progress';

    protected $fillable = ['student_id', 'module_id', 'is_completed', 'quiz_score', 'xp_earned', 'hands_on_count', 'hands_on_xp_awarded', 'hands_on_last_at', 'training_xp_awarded', 'reading_done'];

    protected $casts = ['is_completed' => 'boolean', 'hands_on_xp_awarded' => 'boolean', 'training_xp_awarded' => 'boolean', 'reading_done' => 'boolean', 'hands_on_last_at' => 'datetime'];

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

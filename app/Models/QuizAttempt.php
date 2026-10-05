<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['student_id', 'module_id', 'score', 'answers', 'passed'];

    protected $casts = ['answers' => 'array', 'passed' => 'boolean'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

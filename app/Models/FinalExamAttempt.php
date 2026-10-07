<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExamAttempt extends Model
{
    protected $fillable = ['student_id', 'score', 'answers', 'passed'];

    protected $casts = ['answers' => 'array', 'passed' => 'boolean'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

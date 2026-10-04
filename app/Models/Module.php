<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['teacher_id', 'title', 'slug', 'order', 'description', 'materi', 'xp_reward'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function isDefault(): bool
    {
        return $this->teacher_id === null;
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function badge()
    {
        return $this->hasOne(Badge::class);
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }
}

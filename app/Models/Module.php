<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['title', 'slug', 'order', 'description', 'xp_reward'];

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

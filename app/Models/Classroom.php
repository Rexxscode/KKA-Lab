<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['teacher_id', 'name', 'code'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'classroom_user', 'classroom_id', 'student_id')->withTimestamps();
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'classroom_module')->withPivot('position')->withTimestamps()->orderByPivot('position');
    }
}

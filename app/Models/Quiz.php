<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['teacher_id', 'module_id', 'question', 'options', 'correct_index', 'explanation', 'points', 'origin_id'];

    protected $casts = ['options' => 'array'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function isDefault(): bool
    {
        return $this->teacher_id === null;
    }
}

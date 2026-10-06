<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalModel extends Model
{
    protected $fillable = [
        'name', 'slug', 'version', 'backbone', 'labels', 'dataset',
        'student_model_id', 'approved_by', 'is_active', 'accuracy',
    ];

    protected $casts = ['labels' => 'array', 'dataset' => 'array', 'is_active' => 'boolean'];

    public function asal()
    {
        return $this->belongsTo(StudentModel::class, 'student_model_id');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function aktif(): ?self
    {
        return static::where('is_active', true)->latest('id')->first();
    }
}

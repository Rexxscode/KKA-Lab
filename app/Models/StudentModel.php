<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentModel extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const MAKS_PENDING = 5;

    public const MAKS_BYTE = 2 * 1024 * 1024;

    protected $fillable = [
        'student_id', 'name', 'labels', 'dataset', 'weights', 'backbone', 'status', 'accuracy', 'note',
    ];

    protected $casts = ['labels' => 'array', 'dataset' => 'array', 'weights' => 'array'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}

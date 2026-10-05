<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChat extends Model
{
    protected $fillable = ['user_id', 'module_slug', 'message', 'reply'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

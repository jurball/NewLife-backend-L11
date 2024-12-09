<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class files extends Model
{
    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'ids'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

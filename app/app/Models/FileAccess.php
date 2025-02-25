<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAccess extends Model
{
    protected $table = 'file-access';

    protected $fillable = [
        'owner_id',
        'user_id',
        'file_id',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function owner()
    {
        return $this->belongsTo(Files::class, 'user_id');
    }
}

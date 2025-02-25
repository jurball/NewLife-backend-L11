<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'file_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function access_file()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }
}

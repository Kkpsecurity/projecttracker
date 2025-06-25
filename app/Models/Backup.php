<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'uuid', 'name', 'tables', 'user_id', 'filename', 'size', 'record_count', 'status'
    ];

    protected $casts = [
        'tables' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

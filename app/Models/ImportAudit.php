<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportAudit extends Model
{
    protected $table = 'import_audits';

    protected $fillable = [
        'import_id',
        'type',
        'changes',
        'user_id',
    ];

    protected $casts = [
        'changes' => 'array',
        'import_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

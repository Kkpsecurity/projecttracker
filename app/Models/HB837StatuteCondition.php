<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HB837StatuteCondition extends Model
{
    use HasFactory;

    protected $table = 'hb837_statute_conditions';

    protected $fillable = [
        'hb837_id',
        'created_by',
        'condition_key',
        'status',
        'observations',
        'sort_order',
    ];

    protected $casts = [
        'hb837_id' => 'integer',
        'created_by' => 'integer',
        'sort_order' => 'integer',
    ];

    public function hb837(): BelongsTo
    {
        return $this->belongsTo(HB837::class, 'hb837_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

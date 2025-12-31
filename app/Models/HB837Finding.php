<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HB837Finding extends Model
{
    use HasFactory;

    protected $table = 'hb837_findings';

    protected $fillable = [
        'hb837_id',
        'plot_id',
        'created_by',
        'category',
        'severity',
        'location_context',
        'description',
        'recommendation',
        'status',
        'source',
    ];

    public function hb837(): BelongsTo
    {
        return $this->belongsTo(HB837::class, 'hb837_id');
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class, 'plot_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

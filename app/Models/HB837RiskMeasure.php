<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HB837RiskMeasure extends Model
{
    use HasFactory;

    protected $table = 'hb837_risk_measures';

    protected $fillable = [
        'hb837_id',
        'created_by',
        'section',
        'measure_no',
        'cb_rank',
        'measure',
        'sort_order',
    ];

    protected $casts = [
        'hb837_id' => 'integer',
        'created_by' => 'integer',
        'measure_no' => 'integer',
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

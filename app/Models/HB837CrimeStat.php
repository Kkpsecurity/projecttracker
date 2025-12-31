<?php

namespace App\Models;

use App\Services\HB837\HB837CrimeStatSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HB837CrimeStat extends Model
{
    use HasFactory;

    protected $table = 'hb837_crime_stats';

    protected $fillable = [
        'hb837_id',
        'hb837_file_id',
        'source',
        'report_title',
        'period_start',
        'period_end',
        'crime_risk',
        'stats',
        'is_reviewed',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'stats' => 'array',
        'is_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function setStatsAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['stats'] = null;
            return;
        }

        $statsArray = is_array($value) ? $value : (array) $value;
        $normalized = HB837CrimeStatSchema::normalize($statsArray, [
            'hb837_file_id' => $this->hb837_file_id,
        ]);

        $this->attributes['stats'] = $normalized === null ? null : json_encode($normalized);
    }

    public function hb837(): BelongsTo
    {
        return $this->belongsTo(HB837::class, 'hb837_id');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(HB837File::class, 'hb837_file_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

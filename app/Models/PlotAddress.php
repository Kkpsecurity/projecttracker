<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotAddress extends Model
{
    protected $fillable = [
        'plot_id',
        'street_address',
        'city',
        'state',
        'zip_code',
    ];

    // Relationships
    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->city,
            $this->state,
            $this->zip_code,
        ]);

        return implode(', ', $parts);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotAddress extends Model
{
    protected $fillable = [
        'plot_id',
        'address_line_1',
        'address_line_2',
        'street_address',
        'city',
        'state',
        'zip_code',
        'country',
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
            $this->address_line_1 ?: $this->street_address,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}

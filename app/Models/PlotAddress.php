<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotAddress extends Model
{
    protected $table = 'plot_addresses';

    protected $fillable = [
        'plot_id',
        'latitude',
        'longitude',
        'location_name',
        'street_address',
        'city',
        'state',
        'zip_code',
        'country',
        'description',
        'property_type',
        'property_value',
        'square_footage',
        'status',
        'metadata',
        'verified_at',
        'verified_by',
    ];

    // CAST
    protected $casts = [
        'plot_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'location_name' => 'string',
        'street_address' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip_code' => 'string',
        'country' => 'string',
        'description' => 'string',
        'property_type' => 'string',
        'property_value' => 'decimal:2',
        'square_footage' => 'decimal:2',
        'status' => 'string',
        'metadata' => 'json',
        'verified_at' => 'datetime',
        'verified_by' => 'integer',
    ];

    // Constants for status
    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_PENDING = 'pending';

    const STATUS_SOLD = 'sold';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInArea($query, $minLat, $maxLat, $minLng, $maxLng)
    {
        return $query->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng]);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street_address,
            $this->city,
            $this->state,
            $this->zip_code,
        ]);

        return implode(', ', $parts);
    }

    public function getFormattedValueAttribute()
    {
        return $this->property_value ? '$'.number_format((float) $this->property_value, 2) : null;
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
}

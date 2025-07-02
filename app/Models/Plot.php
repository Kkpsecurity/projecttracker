<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plot extends Model
{
    protected $fillable = [
        'plot_name',
        'description',
        'hb837_id',
        'lot_number',
        'block_number',
        'subdivision_name',
        'coordinates_latitude',
        'coordinates_longitude',
    ];

    protected $casts = [
        'coordinates_latitude' => 'decimal:8',
        'coordinates_longitude' => 'decimal:8',
    ];

    // Relationships
    public function hb837(): BelongsTo
    {
        return $this->belongsTo(HB837::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(PlotAddress::class);
    }

    public function plotGroups(): BelongsToMany
    {
        return $this->belongsToMany(PlotGroup::class, 'plot_group_plots')
            ->withPivot('sort_order', 'notes')
            ->withTimestamps()
            ->orderBy('plot_group_plots.sort_order');
    }

    // Accessors
    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->lot_number ? "Lot {$this->lot_number}" : null,
            $this->block_number ? "Block {$this->block_number}" : null,
            $this->subdivision_name,
        ]);

        return implode(', ', $parts);
    }

    public function getCoordinatesAttribute(): ?string
    {
        if ($this->coordinates_latitude && $this->coordinates_longitude) {
            return $this->coordinates_latitude . ', ' . $this->coordinates_longitude;
        }
        return null;
    }
}

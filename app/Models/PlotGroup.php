<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlotGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function plots(): BelongsToMany
    {
        return $this->belongsToMany(Plot::class, 'plot_group_plots')
                    ->withPivot('sort_order', 'notes')
                    ->withTimestamps()
                    ->orderBy('plot_group_plots.sort_order');
    }

    public function plotGroupPlots(): HasMany
    {
        return $this->hasMany(PlotGroupPlot::class);
    }

    // Accessors
    public function getPlotsCountAttribute(): int
    {
        return $this->plots()->count();
    }

    public function getMappedPlotsCountAttribute(): int
    {
        return $this->plots()
                    ->whereNotNull('coordinates_latitude')
                    ->whereNotNull('coordinates_longitude')
                    ->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}

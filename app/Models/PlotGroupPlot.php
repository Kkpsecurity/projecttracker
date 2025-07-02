<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotGroupPlot extends Model
{
    protected $fillable = [
        'plot_group_id',
        'plot_id',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // Relationships
    public function plotGroup(): BelongsTo
    {
        return $this->belongsTo(PlotGroup::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }
}

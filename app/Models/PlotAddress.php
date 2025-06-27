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
    ];

    // CAST
    protected $casts = [
        'plot_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'location_name' => 'string',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $table = 'plots';

    protected $fillable = [
        'plot_name',
    ];

    // CAST
    protected $casts = [
        'plot_name' => 'string',
    ];

    public function plotAddresses()
    {
        return $this->hasMany(PlotAddress::class);
    }
}

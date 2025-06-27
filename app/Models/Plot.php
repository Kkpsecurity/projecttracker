<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $table = 'plots';

    protected $fillable = [
        'plot_name',
        'description',
        'plot_type',
        'client_contact_name',
        'client_contact_email',
        'client_contact_phone',
        'is_active',
    ];

    // CAST
    protected $casts = [
        'plot_name' => 'string',
        'description' => 'string',
        'plot_type' => 'string',
        'client_contact_name' => 'string',
        'client_contact_email' => 'string',
        'client_contact_phone' => 'string',
        'is_active' => 'boolean',
    ];

    // Constants for plot types
    const TYPE_CUSTOM = 'custom';

    const TYPE_PROSPECT = 'prospect';

    const TYPE_CLIENT = 'client';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('plot_type', $type);
    }

    // Accessors and Mutators
    public function getIsCustomAttribute()
    {
        return $this->plot_type === self::TYPE_CUSTOM;
    }

    public function getIsProspectAttribute()
    {
        return $this->plot_type === self::TYPE_PROSPECT;
    }

    public function getIsClientAttribute()
    {
        return $this->plot_type === self::TYPE_CLIENT;
    }

    public function plotAddresses()
    {
        return $this->hasMany(PlotAddress::class);
    }
}

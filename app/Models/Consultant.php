<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultant extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'dba_company_name',
        'mailing_address',
        'fcp_expiration_date',
        'assigned_light_meter',
        'lm_nist_expiration_date',
        'subcontractor_bonus_rate',
        'notes',
    ];

    protected $casts = [
        'fcp_expiration_date' => 'date',
        'lm_nist_expiration_date' => 'date',
        'subcontractor_bonus_rate' => 'decimal:2',
    ];

    // Relationships
    public function hb837Projects(): HasMany
    {
        return $this->hasMany(HB837::class, 'assigned_consultant_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ConsultantFile::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('fcp_expiration_date')
                    ->orWhere('fcp_expiration_date', '>', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('fcp_expiration_date', [
            now(),
            now()->addDays($days)
        ]);
    }
}

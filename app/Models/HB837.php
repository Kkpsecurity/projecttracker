<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HB837 extends Model
{
    protected $table = 'hb837';

    protected $fillable = [
        'user_id',
        'assigned_consultant_id',
        'owner_id',
        'owner_name',
        'property_name',
        'property_type',
        'units',
        'management_company',
        'address',
        'city',
        'county',
        'state',
        'zip',
        'phone',
        'report_status',
        'contracting_status',
        'scheduled_date_of_inspection',
        'report_submitted',
        'billing_req_sent',
        'agreement_submitted',
        'quoted_price',
        'sub_fees_estimated_expenses',
        'project_net_profit',
        'notes',
        'financial_notes',
        'securitygauge_crime_risk',
        'property_manager_name',
        'property_manager_email',
        'regional_manager_name',
        'regional_manager_email',
        'macro_client',
        'macro_contact',
        'macro_email',
    ];

    protected $casts = [
        'scheduled_date_of_inspection' => 'datetime',
        'report_submitted' => 'datetime',
        'billing_req_sent' => 'datetime',
        'agreement_submitted' => 'datetime',
        'quoted_price' => 'decimal:2',
        'sub_fees_estimated_expenses' => 'decimal:2',
        'project_net_profit' => 'decimal:2',
        'units' => 'integer',
    ];

    // Relationships
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class, 'assigned_consultant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plot(): HasOne
    {
        return $this->hasOne(Plot::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(HB837File::class, 'hb837_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('report_status', ['in-progress', 'not-started']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('report_status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('report_status', 'in-progress');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('report_status', $status);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->scheduled_date_of_inspection &&
               $this->scheduled_date_of_inspection < now() &&
               $this->report_status !== 'completed';
    }

    public function getProgressPercentageAttribute(): float
    {
        $totalSteps = 4; // Based on report status workflow
        $currentStep = 0;

        switch ($this->report_status) {
            case 'not-started':
                $currentStep = 0;
                break;
            case 'in-progress':
                $currentStep = 1;
                break;
            case 'in-review':
                $currentStep = 2;
                break;
            case 'completed':
                $currentStep = 4;
                break;
        }

        return ($currentStep / $totalSteps) * 100;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
        ]);

        return implode(', ', $parts);
    }
}

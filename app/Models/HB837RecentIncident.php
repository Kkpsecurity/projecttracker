<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HB837RecentIncident extends Model
{
    protected $table = 'hb837_recent_incidents';

    protected $fillable = [
        'hb837_id',
        'created_by',
        'incident_date',
        'summary',
        'sort_order',
    ];

    public function hb837()
    {
        return $this->belongsTo(HB837::class, 'hb837_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

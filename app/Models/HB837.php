<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use App\Models\HB837File;

class HB837 extends Model
{
    protected $table = 'hb837';

    protected $fillable = [
        'report_status',
        'property_name',
        'management_company',
        'owner_id',
        'owner_name',
        'property_type',
        'units',
        'address',
        'city',
        'county',
        'state',
        'zip',
        'phone',
        'assigned_consultant_id',
        'scheduled_date_of_inspection',
        'report_submitted',
        'billing_req_sent',
        'financial_notes',
        'securitygauge_crime_risk',
        "notes",
        'property_manager_name',
        'property_manager_email',
        'regional_manager_name',
        'regional_manager_email',
        'agreement_submitted',
        'contracting_status',
        'quoted_price',
        'sub_fees_estimated_expenses',
        'project_net_profit',
        'macro_client',
        'macro_contact',
        'macro_email',
        'user_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship for related files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(HB837File::class, 'hb837_id', 'id');
    }

    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'assigned_consultant_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id');
    }

    public function getAddressesGroupedByMacroClient()
    {
        return $this->select('macro_client', 'macro_contact', 'macro_email')
            ->groupBy('macro_client', 'macro_contact', 'macro_email')
            ->get();
    }

}

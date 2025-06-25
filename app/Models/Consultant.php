<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultant extends Model
{
    protected $table = 'consultants';

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
        'notes'
    ];

    // Cast the fields
    protected $casts = [
        'fcp_expiration_date' => 'datetime',
        'lm_nist_expiration_date' => 'datetime',
        'subcontractor_bonus_rate' => 'float',
    ];

    /**
     * Relationship: A consultant can have many files.
     */
    public function files(): HasMany
    {
        return $this->hasMany(ConsultantFile::class, 'consultant_id');
    }

    /**
     * Attach a new file to the consultant.
     */
    public function addFile($fileData)
    {
        return $this->files()->create($fileData);
    }

    /**
     * Get all files for this consultant.
     */
    public function getFiles()
    {
        return $this->files()->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'company_name',
        'tax_id',
    ];

    /**
     * Relationships
     */

    // Example relationship: An owner may own multiple properties.
    public function properties()
    {
        return $this->hasMany(HB837::class, 'owner_id');
    }

    /**
     * Accessors
     */

    // Combine full address into a single attribute
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip}";
    }

    /**
     * Mutators
     */

    // Ensure phone numbers are stored in a clean format
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Scopes
     */

    // Scope to filter owners by state
    public function scopeInState($query, $state)
    {
        return $query->where('state', $state);
    }

    // Scope to search for an owner by name or company
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%$term%")
            ->orWhere('company_name', 'LIKE', "%$term%");
    }

    /**
     * Utility Methods
     */

    // Check if the owner has valid contact details
    public function hasValidContactDetails()
    {
        return $this->email || $this->phone;
    }
}

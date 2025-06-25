<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_name',
        'project_name',
        'poc',
        'status',
        'quick_status',
        'description',
        'corporate_name',
        'file1',
        'file2',
        'file3',
        'project_services_total',
        'project_expenses_total',
        'final_services_total',
        'final_billing_total',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'integer',
        'client_name' => 'string',
        'project_name' => 'string',
        'poc' => 'string',
        'status' => 'string',
        'quick_status' => 'string',
        'description' => 'string',
        'corporate_name' => 'string',
        'project_services_total' => 'decimal:2',
        'project_expenses_total' => 'decimal:2',
        'final_services_total' => 'decimal:2',
        'final_billing_total' => 'decimal:2',
    ];
}

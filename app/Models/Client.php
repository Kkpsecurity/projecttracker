<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    /**
     * @var string[]
     */
    protected $casts = [

        'id'                => 'integer',
        'client_name'       => 'string',
        'project_name'      => 'string',
        'poc'               => 'string',
        'status'            => 'string',
        'quick_status'      => 'string',
        'description'       => 'string',
        'corporate_name'    => 'string'

    ];
}

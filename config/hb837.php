<?php

return [
    'mappings' => [
        'report_status' => 'report_status',
        'property_name' => 'property_name',
        'management_company' => 'management_company',
        'owner_id' => 'owner',
        // ... all other field mappings ...
    ],

    'required_fields' => ['address', 'property_name', 'city', 'zip'],

    'defaults' => [
        'owner_id' => 1, // Default owner ID
        'property_type' => 'garden',
        'contracting_status' => 'quoted',
        'report_status' => 'not-started',
    ],


    'property_types' => ['garden', 'midrise', 'highrise', 'industrial', 'bungalo'],
    'contracting_statuses' => ['executed', 'quoted', 'started', 'closed'],
    'report_statuses' => ['not-started', 'in-progress', 'in-review', 'completed'],


    'map_api_key' => env('GOOGLE_MAPS_API_KEY'),

    'tab_mapping' => [
        'active' => [
            'report_statuses' => ['not-started', 'in-progress', 'in-review'],
            'contracting_statuses' => ['executed'],
        ],
        'quoted' => [
            'report_statuses' => ['not-started', 'in-progress', 'in-review'],
            'contracting_statuses' => ['quoted', 'started'],
        ],
        'completed' => [
            'report_statuses' => ['completed'],
        ],
        'closed' => [
            'contracting_statuses' => ['closed'],
        ],
    ],

    'security_gauge' => [
        1 => 'Low',
        2 => 'Moderate',
        3 => 'Elevated',
        4 => 'High',
        5 => 'Severe'
    ]
];

<?php

return [
    'mappings' => [
        'report_status' => 'report_status',
        'property_name' => 'property_name',
        'management_company' => 'management_company',
        'owner_id' => 'owner',
        'owner_name' => 'owner_name',
        'property_type' => 'property_type',
        'units' => 'units',
        'address' => 'address',
        'city' => 'city',
        'county' => 'county',
        'state' => 'state',
        'zip' => 'zip',
        'phone' => 'phone',
        'assigned_consultant_id' => 'assigned_consultant_id',
        'scheduled_date_of_inspection' => 'scheduled_date_of_inspection',
        'report_submitted' => 'report_submitted',
        'billing_req_sent' => 'billing_req_sent',
        'financial_notes' => 'financial_notes',
        'securitygauge_crime_risk' => 'securitygauge_crime_risk',
        'notes' => 'notes',
        'property_manager_name' => 'property_manager_name',
        'property_manager_email' => 'property_manager_email',
        'regional_manager_name' => 'regional_manager_name',
        'regional_manager_email' => 'regional_manager_email',
        'agreement_submitted' => 'agreement_submitted',
        'contracting_status' => 'contracting_status',
        'quoted_price' => 'quoted_price',
        'sub_fees_estimated_expenses' => 'sub_fees_estimated_expenses',
        'project_net_profit' => 'project_net_profit',
        'macro_client' => 'macro_client',
        'macro_contact' => 'macro_contact',
        'macro_email' => 'macro_email',
        'user_id' => 'user_id'
    ],

    'required_fields' => ['address', 'property_name', 'city', 'zip'],

    'defaults' => [
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
        5 => 'Critical',
    ],
];

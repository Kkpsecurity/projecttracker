<?php

return [
    'mappings' => [
        // System fields
        'id' => 'id',
        'user_id' => 'user_id',
        'assigned_consultant_id' => 'assigned_consultant_id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',

        // Import metadata
        'import_session_id' => 'import_session_id',
        'import_source' => 'import_source',
        'last_import_at' => 'last_import_at',
        'import_metadata' => 'import_metadata',

        // Property owner information
        'owner_id' => 'owner',
        'owner_name' => 'owner_name',

        // Property information
        'property_name' => 'property_name',
        'property_type' => 'property_type',
        'units' => 'units',
        'management_company' => 'management_company',

        // Address fields
        'address' => 'address',
        'city' => 'city',
        'county' => 'county',
        'state' => 'state',
        'zip' => 'zip',

        // Project workflow and dates
        'report_status' => 'report_status',
        'contracting_status' => 'contracting_status',
        'scheduled_date_of_inspection' => 'scheduled_date_of_inspection',
        'report_submitted' => 'report_submitted',
        'billing_req_submitted' => 'billing_req_submitted',
        'agreement_submitted' => 'agreement_submitted',

        // Financial information
        'quoted_price' => 'quoted_price',
        'sub_fees_estimated_expenses' => 'sub_fees_estimated_expenses',
        'project_net_profit' => 'project_net_profit',
        'financial_notes' => 'financial_notes',

        // Security assessment
        'securitygauge_crime_risk' => 'securitygauge_crime_risk',

        // Contact management
        'property_manager_name' => 'property_manager_name',
        'property_manager_email' => 'property_manager_email',
        'regional_manager_name' => 'regional_manager_name',
        'regional_manager_email' => 'regional_manager_email',

        // Macro client information
        'macro_client' => 'macro_client',
        'macro_contact' => 'macro_contact',
        'macro_email' => 'macro_email',
        'phone' => 'phone',

        // Notes
        'notes' => 'notes',
        'consultant_notes' => 'consultant_notes'
    ],

    'required_fields' => ['address', 'property_name', 'city', 'zip'],

    // Field definitions for dynamic form generation
    'field_definitions' => [
        'property_name' => [
            'type' => 'text',
            'label' => 'Property Name',
            'placeholder' => 'Enter property name',
            'required' => true
        ],
        'property_type' => [
            'type' => 'select',
            'label' => 'Property Type',
            'options' => 'property_types', // References config key
            'placeholder' => 'Select property type'
        ],
        'units' => [
            'type' => 'number',
            'label' => 'Number of Units',
            'placeholder' => 'Number of units',
            'attributes' => ['min' => '1']
        ],
        'management_company' => [
            'type' => 'text',
            'label' => 'Management Company',
            'placeholder' => 'Management company name'
        ],
        'owner_name' => [
            'type' => 'text',
            'label' => 'Owner Name',
            'placeholder' => 'Property owner name'
        ],
        'assigned_consultant_id' => [
            'type' => 'select',
            'label' => 'Assigned Consultant',
            'options' => 'consultants', // References variable passed to view
            'placeholder' => 'Select consultant',
            'value_field' => 'id',
            'display_field' => ['first_name', 'last_name']
        ],
        'report_status' => [
            'type' => 'select',
            'label' => 'Report Status',
            'options' => 'report_statuses',
            'default' => 'not-started'
        ],
        'contracting_status' => [
            'type' => 'select',
            'label' => 'Contracting Status',
            'options' => 'contracting_statuses',
            'placeholder' => 'Select status',
            'default' => 'quoted'
        ],
        'scheduled_date_of_inspection' => [
            'type' => 'date',
            'label' => 'Scheduled Inspection Date'
        ],
        'report_submitted' => [
            'type' => 'date',
            'label' => 'Report Submitted Date'
        ],
        'billing_req_sent' => [
            'type' => 'date',
            'label' => 'Billing Request Sent Date'
        ],
        'billing_req_submitted' => [
            'type' => 'date',
            'label' => 'Billing Request Submitted Date'
        ],
        'agreement_submitted' => [
            'type' => 'date',
            'label' => 'Agreement Submitted Date'
        ],
        'securitygauge_crime_risk' => [
            'type' => 'select',
            'label' => 'Crime Risk Level',
            'options' => 'security_gauge',
            'placeholder' => 'Select crime risk'
        ],
        // Address fields
        'address' => [
            'type' => 'text',
            'label' => 'Street Address',
            'placeholder' => 'Enter street address',
            'required' => true
        ],
        'city' => [
            'type' => 'text',
            'label' => 'City',
            'placeholder' => 'Enter city',
            'required' => true
        ],
        'county' => [
            'type' => 'text',
            'label' => 'County',
            'placeholder' => 'Enter county'
        ],
        'state' => [
            'type' => 'text',
            'label' => 'State',
            'placeholder' => 'Enter state',
            'attributes' => ['maxlength' => '2']
        ],
        'zip' => [
            'type' => 'text',
            'label' => 'ZIP Code',
            'placeholder' => 'Enter ZIP code',
            'required' => true,
            'attributes' => ['maxlength' => '10']
        ],
        'phone' => [
            'type' => 'tel',
            'label' => 'Phone Number',
            'placeholder' => 'Enter phone number',
            'attributes' => ['maxlength' => '15']
        ],
        // Contact fields
        'property_manager_name' => [
            'type' => 'text',
            'label' => 'Property Manager Name',
            'placeholder' => 'Enter property manager name'
        ],
        'property_manager_email' => [
            'type' => 'email',
            'label' => 'Property Manager Email',
            'placeholder' => 'Enter property manager email'
        ],
        'regional_manager_name' => [
            'type' => 'text',
            'label' => 'Regional Manager Name',
            'placeholder' => 'Enter regional manager name'
        ],
        'regional_manager_email' => [
            'type' => 'email',
            'label' => 'Regional Manager Email',
            'placeholder' => 'Enter regional manager email'
        ],
        'macro_client' => [
            'type' => 'text',
            'label' => 'Macro Client',
            'placeholder' => 'Enter macro client name'
        ],
        'macro_contact' => [
            'type' => 'text',
            'label' => 'Macro Contact',
            'placeholder' => 'Enter macro contact person'
        ],
        'macro_email' => [
            'type' => 'email',
            'label' => 'Macro Email',
            'placeholder' => 'Enter macro client email'
        ],
        // Financial fields
        'quoted_price' => [
            'type' => 'number',
            'label' => 'Quoted Price',
            'placeholder' => 'Enter quoted price',
            'attributes' => ['step' => '0.01', 'min' => '0']
        ],
        'sub_fees_estimated_expenses' => [
            'type' => 'number',
            'label' => 'Sub Fees & Expenses',
            'placeholder' => 'Enter sub fees & expenses',
            'attributes' => ['step' => '0.01', 'min' => '0']
        ],
        'project_net_profit' => [
            'type' => 'number',
            'label' => 'Project Net Profit',
            'placeholder' => 'Automatically calculated',
            'attributes' => ['step' => '0.01', 'readonly' => 'readonly'],
            'help_text' => 'Automatically calculated: Quoted Price - Sub Fees & Expenses'
        ],
        'financial_notes' => [
            'type' => 'textarea',
            'label' => 'Financial Notes',
            'placeholder' => 'Enter financial notes',
            'attributes' => ['rows' => '3']
        ],
        // Notes fields
        'notes' => [
            'type' => 'textarea',
            'label' => 'General Notes',
            'placeholder' => 'Enter general notes',
            'attributes' => ['rows' => '4']
        ],
        'consultant_notes' => [
            'type' => 'textarea',
            'label' => 'Consultant Notes',
            'placeholder' => 'Enter consultant notes',
            'attributes' => ['rows' => '4']
        ]
    ],

    // Tab organization for UI
    'tab_fields' => [
        'general' => [
            'property_name',
            'property_type',
            'units',
            'securitygauge_crime_risk',
            'management_company',
            'owner_name',

            'assigned_consultant_id',
            'report_status',
            'contracting_status',
            'scheduled_date_of_inspection',
            'report_submitted',
            'agreement_submitted'
        ],
        'address' => [
            'address',
            'city',
            'county',
            'state',
            'zip'
        ],
        'contact' => [
            'property_manager_name',
            'property_manager_email',
            'regional_manager_name',
            'regional_manager_email',
            'macro_client',
            'macro_contact',
            'macro_email',
            'phone'
        ],
        'financial' => [
            'quoted_price',
            'sub_fees_estimated_expenses',
            'project_net_profit',
            'billing_req_submitted',
            'financial_notes',
        ],
        'notes' => [
            'notes',
            'consultant_notes'
        ],
        'files' => [
            // Files are stored in separate hb837_files table
            'id' // Used as foreign key reference
        ],
        'map' => [
            // Map coordinates stored in related plots and plot_addresses tables
            'id',
            'address',
            'city',
            'county',
            'state',
            'zip'
        ]
    ],

    'defaults' => [
        'property_type' => 'garden',
        'contracting_status' => 'quoted',
        'report_status' => 'not-started',
    ],

    'property_types' => ['garden', 'midrise', 'highrise', 'industrial', 'bungalo'],
    'contracting_statuses' => ['executed', 'quoted', 'started', 'closed'],
    'report_statuses' => ['not-started', 'underway', 'in-review', 'completed'],

    'map_api_key' => env('GOOGLE_MAPS_API_KEY'),

    'tab_mapping' => [
        'active' => [
            'report_statuses' => ['not-started', 'underway', 'in-review'],
            'contracting_statuses' => ['executed'],
        ],
        'quoted' => [
            'report_statuses' => ['not-started', 'underway', 'in-review'],
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
        5 => 'Severe',
    ],
];

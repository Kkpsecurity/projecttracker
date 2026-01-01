<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HB837 Admin Form Tab Layout Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines which fields appear in each tab of the
    | HB837 admin form. This is used for systematic validation to ensure
    | all fields are properly mapped and displayed in the correct tabs.
    |
    */

    'hb837_tabs' => [
        'general' => [
            'label' => 'General Information',
            'icon' => 'fas fa-info-circle',
            'priority' => 1,
            'description' => 'Basic property and project information',
            'fields' => [
                'property_name' => [
                    'type' => 'text',
                    'label' => 'Property Name',
                    'required' => true,
                    'validation' => 'required|string|max:255',
                    'excel_columns' => ['Property Name', 'property name', 'PROPERTY NAME'],
                    'grid_width' => 'col-md-6',
                ],
                'property_type' => [
                    'type' => 'select',
                    'label' => 'Property Type',
                    'required' => false,
                    'validation' => 'nullable|string|max:100',
                    'excel_columns' => ['Property Type', 'property type', 'PROPERTY TYPE'],
                    'options' => ['Residential', 'Commercial', 'Industrial', 'Mixed Use'],
                    'grid_width' => 'col-md-6',
                ],
                'units' => [
                    'type' => 'number',
                    'label' => 'Units',
                    'required' => false,
                    'validation' => 'nullable|integer|min:0',
                    'excel_columns' => ['Units', 'units', 'UNITS', 'Unit Count'],
                    'grid_width' => 'col-md-4',
                ],
                'report_status' => [
                    'type' => 'select',
                    'label' => 'Report Status',
                    'required' => true,
                    'validation' => 'required|in:pending,in_progress,completed',
                    'excel_columns' => ['Report Status', 'Status'],
                    'options' => ['pending', 'in_progress', 'completed'],
                    'grid_width' => 'col-md-4',
                ],
                'contracting_status' => [
                    'type' => 'select',
                    'label' => 'Contracting Status',
                    'required' => false,
                    'validation' => 'nullable|string|max:50',
                    'excel_columns' => ['Contracting Status', 'Contract Status'],
                    'options' => ['quoted', 'contracted', 'executed', 'cancelled'],
                    'grid_width' => 'col-md-4',
                ],
                'assigned_consultant_id' => [
                    'type' => 'select',
                    'label' => 'Assigned Consultant',
                    'required' => false,
                    'validation' => 'nullable|exists:users,id',
                    'excel_columns' => ['Assigned Consultant', 'Consultant', 'Assigned To'],
                    'relationship' => 'assignedConsultant',
                    'grid_width' => 'col-md-6',
                ],
                'user_id' => [
                    'type' => 'select',
                    'label' => 'User',
                    'required' => false,
                    'validation' => 'nullable|exists:users,id',
                    'excel_columns' => ['User', 'User ID'],
                    'relationship' => 'user',
                    'grid_width' => 'col-md-6',
                ],
                'notes' => [
                    'type' => 'textarea',
                    'label' => 'General Notes',
                    'required' => false,
                    'validation' => 'nullable|string|max:2000',
                    'excel_columns' => ['Notes', 'notes', 'NOTES', 'Comments'],
                    'grid_width' => 'col-md-12',
                    'rows' => 3,
                ],
            ],
        ],

        'address' => [
            'label' => 'Address Information',
            'icon' => 'fas fa-map-marker-alt',
            'priority' => 2,
            'description' => 'Property location and contact details',
            'fields' => [
                'address' => [
                    'type' => 'textarea',
                    'label' => 'Address',
                    'required' => false,
                    'validation' => 'nullable|string|max:500',
                    'excel_columns' => ['Address', 'address', 'ADDRESS', 'Property Address'],
                    'grid_width' => 'col-md-12',
                    'rows' => 2,
                ],
                'city' => [
                    'type' => 'text',
                    'label' => 'City',
                    'required' => false,
                    'validation' => 'nullable|string|max:100',
                    'excel_columns' => ['City', 'city', 'CITY'],
                    'grid_width' => 'col-md-4',
                ],
                'county' => [
                    'type' => 'text',
                    'label' => 'County',
                    'required' => false,
                    'validation' => 'nullable|string|max:100',
                    'excel_columns' => ['County', 'county', 'COUNTY'],
                    'grid_width' => 'col-md-4',
                ],
                'state' => [
                    'type' => 'text',
                    'label' => 'State',
                    'required' => false,
                    'validation' => 'nullable|string|max:50',
                    'excel_columns' => ['State', 'state', 'STATE'],
                    'grid_width' => 'col-md-2',
                ],
                'zip' => [
                    'type' => 'text',
                    'label' => 'ZIP Code',
                    'required' => false,
                    'validation' => 'nullable|string|max:20',
                    'excel_columns' => ['Zip', 'zip', 'ZIP', 'Zip Code', 'ZIP Code', 'Postal Code'],
                    'grid_width' => 'col-md-2',
                ],
                'phone' => [
                    'type' => 'text',
                    'label' => 'Phone',
                    'required' => false,
                    'validation' => 'nullable|string|max:20',
                    'excel_columns' => ['Phone', 'phone', 'PHONE', 'Phone Number'],
                    'grid_width' => 'col-md-4',
                ],
            ],
        ],

        'contact' => [
            'label' => 'Contact Information',
            'icon' => 'fas fa-users',
            'priority' => 3,
            'description' => 'Property managers, owners, and other contacts',
            'fields' => [
                'management_company' => [
                    'type' => 'text',
                    'label' => 'Management Company',
                    'required' => false,
                    'validation' => 'nullable|string|max:255',
                    'excel_columns' => ['Management Company', 'Mgmt Company', 'management company'],
                    'grid_width' => 'col-md-6',
                ],
                'property_manager_name' => [
                    'type' => 'text',
                    'label' => 'Property Manager Name',
                    'required' => false,
                    'validation' => 'nullable|string|max:255',
                    'excel_columns' => ['Property Manager Name', 'PM Name', 'property manager name'],
                    'grid_width' => 'col-md-6',
                ],
                'property_manager_email' => [
                    'type' => 'email',
                    'label' => 'Property Manager Email',
                    'required' => false,
                    'validation' => 'nullable|email|max:255',
                    'excel_columns' => ['Property Manager Email', 'PM Email', 'property manager email'],
                    'grid_width' => 'col-md-6',
                ],
                'regional_manager_name' => [
                    'type' => 'text',
                    'label' => 'Regional Manager Name',
                    'required' => false,
                    'validation' => 'nullable|string|max:255',
                    'excel_columns' => ['Regional Manager Name', 'RM Name', 'regional manager name'],
                    'grid_width' => 'col-md-6',
                ],
                'regional_manager_email' => [
                    'type' => 'email',
                    'label' => 'Regional Manager Email',
                    'required' => false,
                    'validation' => 'nullable|email|max:255',
                    'excel_columns' => ['Regional Manager Email', 'RM Email', 'regional manager email'],
                    'grid_width' => 'col-md-6',
                ],
                'owner_name' => [
                    'type' => 'text',
                    'label' => 'Owner Name',
                    'required' => false,
                    'validation' => 'nullable|string|max:255',
                    'excel_columns' => ['Owner Name', 'owner name', 'OWNER NAME'],
                    'grid_width' => 'col-md-6',
                ],
                'macro_client' => [
                    'type' => 'text',
                    'label' => 'Macro Client',
                    'required' => false,
                    'validation' => 'nullable|string|max:255',
                    'excel_columns' => ['Macro Client', 'macro client', 'MACRO CLIENT'],
                    'grid_width' => 'col-md-4',
                ],
                'macro_contact' => [
                    'type' => 'text',
                    'label' => 'Macro Contact',
                    'required' => false,
                    'validation' => 'nullable|string|max:255',
                    'excel_columns' => ['Macro Contact', 'macro contact', 'MACRO CONTACT'],
                    'grid_width' => 'col-md-4',
                ],
                'macro_email' => [
                    'type' => 'email',
                    'label' => 'Macro Email',
                    'required' => false,
                    'validation' => 'nullable|email|max:255',
                    'excel_columns' => ['Macro Email', 'macro email', 'MACRO EMAIL'],
                    'grid_width' => 'col-md-4',
                ],
                'owner_id' => [
                    'type' => 'number',
                    'label' => 'Owner ID',
                    'required' => false,
                    'validation' => 'nullable|integer',
                    'grid_width' => 'col-md-4',
                ],
                'consultant_notes' => [
                    'type' => 'textarea',
                    'label' => 'Consultant Notes',
                    'required' => false,
                    'validation' => 'nullable|string|max:2000',
                    'grid_width' => 'col-md-12',
                    'rows' => 3,
                ],
            ],
        ],

        'financial' => [
            'label' => 'Financial Information',
            'icon' => 'fas fa-dollar-sign',
            'priority' => 4,
            'description' => 'Pricing, costs, and financial tracking',
            'fields' => [
                'quoted_price' => [
                    'type' => 'decimal',
                    'label' => 'Quoted Price',
                    'required' => false,
                    'validation' => 'nullable|numeric|min:0',
                    'excel_columns' => ['Quoted Price', 'quoted price', 'QUOTED PRICE', 'Quote Amount'],
                    'format' => 'currency',
                    'grid_width' => 'col-md-4',
                ],
                'sub_fees_estimated_expenses' => [
                    'type' => 'decimal',
                    'label' => 'Sub Fees & Estimated Expenses',
                    'required' => false,
                    'validation' => 'nullable|numeric|min:0',
                    'excel_columns' => ['Sub Fees Estimated Expenses', 'sub fees estimated expenses', 'SUB FEES ESTIMATED EXPENSES'],
                    'format' => 'currency',
                    'grid_width' => 'col-md-4',
                ],
                'project_net_profit' => [
                    'type' => 'decimal',
                    'label' => 'Project Net Profit',
                    'required' => false,
                    'validation' => 'nullable|numeric',
                    'excel_columns' => ['Project Net Profit', 'project net profit', 'PROJECT NET PROFIT', 'Net Profit'],
                    'format' => 'currency',
                    'grid_width' => 'col-md-4',
                ],
                'financial_notes' => [
                    'type' => 'textarea',
                    'label' => 'Financial Notes',
                    'required' => false,
                    'validation' => 'nullable|string|max:1000',
                    'excel_columns' => ['Financial Notes', 'financial notes', 'FINANCIAL NOTES'],
                    'grid_width' => 'col-md-12',
                    'rows' => 3,
                ],
                'report_submitted' => [
                    'type' => 'select',
                    'label' => 'Report Submitted',
                    'required' => false,
                    'validation' => 'nullable|boolean',
                    'options' => ['0' => 'No', '1' => 'Yes'],
                    'grid_width' => 'col-md-4',
                ],
                'billing_req_sent' => [
                    'type' => 'select',
                    'label' => 'Billing Request Sent',
                    'required' => false,
                    'validation' => 'nullable|boolean',
                    'options' => ['0' => 'No', '1' => 'Yes'],
                    'grid_width' => 'col-md-4',
                ],
                'agreement_submitted' => [
                    'type' => 'select',
                    'label' => 'Agreement Submitted',
                    'required' => false,
                    'validation' => 'nullable|boolean',
                    'options' => ['0' => 'No', '1' => 'Yes'],
                    'grid_width' => 'col-md-4',
                ],
                'billing_req_submitted' => [
                    'type' => 'select',
                    'label' => 'Billing Request Submitted',
                    'required' => false,
                    'validation' => 'nullable|boolean',
                    'options' => ['0' => 'No', '1' => 'Yes'],
                    'grid_width' => 'col-md-4',
                ],
            ],
        ],

        'dates' => [
            'label' => 'Important Dates',
            'icon' => 'fas fa-calendar',
            'priority' => 5,
            'description' => 'Project timeline and milestone dates',
            'fields' => [
                'scheduled_date_of_inspection' => [
                    'type' => 'date',
                    'label' => 'Scheduled Date of Inspection',
                    'required' => false,
                    'validation' => 'nullable|date',
                    'excel_columns' => ['Scheduled Date of Inspection', 'scheduled date of inspection', 'SCHEDULED DATE OF INSPECTION'],
                    'grid_width' => 'col-md-6',
                ],
            ],
        ],

        'tracking' => [
            'label' => 'System Tracking',
            'icon' => 'fas fa-cogs',
            'priority' => 6,
            'description' => 'System timestamps and import tracking',
            'fields' => [
                'created_at' => [
                    'type' => 'datetime',
                    'label' => 'Created At',
                    'required' => false,
                    'validation' => 'nullable|datetime',
                    'readonly' => true,
                    'grid_width' => 'col-md-6',
                ],
                'updated_at' => [
                    'type' => 'datetime',
                    'label' => 'Updated At',
                    'required' => false,
                    'validation' => 'nullable|datetime',
                    'readonly' => true,
                    'grid_width' => 'col-md-6',
                ],
                'import_session_id' => [
                    'type' => 'text',
                    'label' => 'Import Session ID',
                    'required' => false,
                    'validation' => 'nullable|string|max:100',
                    'readonly' => true,
                    'grid_width' => 'col-md-6',
                ],
                'import_source' => [
                    'type' => 'text',
                    'label' => 'Import Source',
                    'required' => false,
                    'validation' => 'nullable|string|max:100',
                    'readonly' => true,
                    'grid_width' => 'col-md-6',
                ],
                'last_import_at' => [
                    'type' => 'datetime',
                    'label' => 'Last Import At',
                    'required' => false,
                    'validation' => 'nullable|datetime',
                    'readonly' => true,
                    'grid_width' => 'col-md-6',
                ],
                'import_metadata' => [
                    'type' => 'textarea',
                    'label' => 'Import Metadata',
                    'required' => false,
                    'validation' => 'nullable|string',
                    'readonly' => true,
                    'grid_width' => 'col-md-12',
                    'rows' => 2,
                ],
                'securitygauge_crime_risk' => [
                    'type' => 'text',
                    'label' => 'SecurityGauge Crime Risk',
                    'required' => false,
                    'validation' => 'nullable|string|max:100',
                    'grid_width' => 'col-md-6',
                ],
            ],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Field Priorities & Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define which fields are critical for validation (must be mapped)
    | vs optional fields that may not always be present.
    |
    */

    'field_priorities' => [
        'critical' => [
            'property_name',        // Always required
            'address',             // Core location data
            'city',               // Core location data
            'state',              // Core location data
            'zip',                // Core location data
            'report_status',      // Core workflow field
        ],
        'important' => [
            'property_type',      // Business classification
            'units',              // Property details
            'county',             // Location context
            'phone',              // Contact capability
            'macro_client',       // Business relationship
            'macro_contact',      // Primary contact
            'quoted_price',       // Financial tracking
            'contracting_status', // Workflow status
            'assigned_consultant_id', // Assignment tracking
        ],
        'optional' => [
            'macro_email',        // Additional contact
            'sub_fees_estimated_expenses', // Financial details
            'project_net_profit', // Financial analysis
            'management_company', // Property management
            'property_manager_name', // PM details
            'property_manager_email', // PM contact
            'regional_manager_name', // RM details
            'regional_manager_email', // RM contact
            'owner_name',         // Ownership info
            'contract_signed_date', // Timeline
            'fieldwork_completed_date', // Timeline
            'draft_report_completed_date', // Timeline
            'final_report_completed_date', // Timeline
            'invoice_date',       // Financial timeline
            'notes',              // General notes
            'financial_notes',    // Financial notes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Type Mappings
    |--------------------------------------------------------------------------
    |
    | Map field types to database column types and HTML input types.
    |
    */

    'field_types' => [
        'text' => [
            'db_type' => 'string',
            'html_type' => 'text',
            'validation_base' => 'string',
        ],
        'textarea' => [
            'db_type' => 'text',
            'html_type' => 'textarea',
            'validation_base' => 'string',
        ],
        'email' => [
            'db_type' => 'string',
            'html_type' => 'email',
            'validation_base' => 'email',
        ],
        'number' => [
            'db_type' => 'integer',
            'html_type' => 'number',
            'validation_base' => 'integer',
        ],
        'decimal' => [
            'db_type' => 'decimal',
            'html_type' => 'number',
            'validation_base' => 'numeric',
            'attributes' => ['step' => '0.01'],
        ],
        'date' => [
            'db_type' => 'date',
            'html_type' => 'date',
            'validation_base' => 'date',
        ],
        'datetime' => [
            'db_type' => 'datetime',
            'html_type' => 'datetime-local',
            'validation_base' => 'datetime',
        ],
        'select' => [
            'db_type' => 'string',
            'html_type' => 'select',
            'validation_base' => 'string',
        ],
        'boolean' => [
            'db_type' => 'boolean',
            'html_type' => 'checkbox',
            'validation_base' => 'boolean',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Excel Import Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Excel column mapping and import processing.
    |
    */

    'excel_import' => [
        'ignore_case' => true,
        'trim_whitespace' => true,
        'allowed_separators' => [' ', '_', '-'],
        
        'fallback_patterns' => [
            'property_name' => ['property', 'name', 'prop name'],
            'address' => ['addr', 'location', 'street'],
            'phone' => ['tel', 'telephone', 'contact'],
            'macro_client' => ['client', 'macro', 'company'],
            'quoted_price' => ['price', 'quote', 'amount'],
        ],

        'column_aliases' => [
            // Common variations for property name
            'Property Name' => 'property_name',
            'property name' => 'property_name',
            'PROPERTY NAME' => 'property_name',
            'PropName' => 'property_name',
            
            // Common variations for address
            'Address' => 'address',
            'address' => 'address',
            'ADDRESS' => 'address',
            'Property Address' => 'address',
            'Street Address' => 'address',
            
            // Common variations for financial fields
            'Quoted Price' => 'quoted_price',
            'quoted price' => 'quoted_price',
            'QUOTED PRICE' => 'quoted_price',
            'Quote Amount' => 'quoted_price',
            'Price' => 'quoted_price',
            
            // Add more aliases as needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Test Sheet Mappings
    |--------------------------------------------------------------------------
    |
    | Map test sheet columns to their expected database fields for validation.
    |
    */

    'test_sheet_mappings' => [
        'TEST_SHEET_01' => [
            'Property Name' => 'property_name',
            'Property Type' => 'property_type',
            'Units' => 'units',
            'Address' => 'address',
            'City' => 'city',
            'County' => 'county',
            'State' => 'state',
            'Zip' => 'zip',
            'Phone' => 'phone',
            'Quoted Price' => 'quoted_price',
            'Sub Fees Estimated Expenses' => 'sub_fees_estimated_expenses',
            'Project Net Profit' => 'project_net_profit',
            'Macro Client' => 'macro_client',
            'Macro Contact' => 'macro_contact',
            'Macro Email' => 'macro_email',
            'Assigned Consultant' => 'assigned_consultant_id',
        ],
        'TEST_SHEET_02' => [
            // Fields for update phase testing
            'Property Name' => 'property_name',
            'Contracting Status' => 'contracting_status',
            'Assigned Consultant' => 'assigned_consultant_id',
        ],
        'TEST_SHEET_03' => [
            // Fields for completion phase testing
            'Property Name' => 'property_name',
            'Report Status' => 'report_status',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for comprehensive field and tab validation.
    |
    */

    'validation_config' => [
        'required_tabs' => ['general', 'address', 'contact', 'financial'],
        'optional_tabs' => ['dates', 'tracking'],
        'minimum_fields_per_tab' => 3,
        'maximum_fields_per_tab' => 15,
        
        'field_requirements' => [
            'all_fields_must_have' => ['type', 'label', 'validation'],
            'optional_field_properties' => ['required', 'excel_columns', 'options', 'format', 'readonly'],
        ],
        
        'tab_requirements' => [
            'all_tabs_must_have' => ['label', 'fields'],
            'optional_tab_properties' => ['icon', 'priority', 'description'],
        ],
    ],
];

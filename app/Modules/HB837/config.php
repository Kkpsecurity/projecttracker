<?php

return [
    'name' => 'HB837 Property Management',
    'version' => '1.0.0',
    'description' => 'Comprehensive property management system with 3-phase import/export capabilities',

    'features' => [
        'three_phase_upload' => true,
        'field_mapping' => true,
        'data_validation' => true,
        'import_preview' => true,
        'export_filtering' => true,
        'backup_restore' => true,
    ],

    'mappings' => [
        'report_status' => 'Report Status',
        'property_name' => 'Property Name',
        'management_company' => 'Management Company',
        'owner_id' => 'Owner ID',
        'owner_name' => 'Owner Name',
        'property_type' => 'Property Type',
        'units' => 'Units',
        'address' => 'Address',
        'city' => 'City',
        'county' => 'County',
        'state' => 'State',
        'zip' => 'Zip Code',
        'phone' => 'Phone',
        'assigned_consultant_id' => 'Assigned Consultant',
        'scheduled_date_of_inspection' => 'Scheduled Date of Inspection',
        'report_submitted' => 'Report Submitted',
        'billing_req_sent' => 'Billing Req Sent',
        'financial_notes' => 'Financial Notes',
        'securitygauge_crime_risk' => 'SecurityGauge Crime Risk',
        'notes' => 'Notes',
        'property_manager_name' => 'Property Manager Name',
        'property_manager_email' => 'Property Manager Email',
        'regional_manager_name' => 'Regional Manager Name',
        'regional_manager_email' => 'Regional Manager Email',
        'agreement_submitted' => 'Agreement Submitted',
        'contracting_status' => 'Contracting Status',
        'quoted_price' => 'Quoted Price',
        'sub_fees_estimated_expenses' => 'Sub Fees/Estimated Expenses',
        'project_net_profit' => 'Project Net Profit',
        'macro_client' => 'Macro Client',
        'macro_contact' => 'Macro Contact',
        'macro_email' => 'Macro Email',
    ],

    'required_fields' => [
        'property_name',
        'address',
        'city',
        'zip',
    ],

    'defaults' => [
        'property_type' => 'garden',
        'contracting_status' => 'quoted',
        'report_status' => 'not-started',
    ],

    'validation_rules' => [
        'property_name' => 'required|string|max:255',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'zip' => 'required|string|max:20',
        'property_type' => 'in:garden,midrise,highrise,industrial,bungalo',
        'contracting_status' => 'in:executed,quoted,started,closed',
        'report_status' => 'in:not-started,in-progress,in-review,completed',
        'quoted_price' => 'nullable|numeric|min:0',
        'units' => 'nullable|integer|min:0',
        'phone' => 'nullable|string|max:20',
        'management_company' => 'nullable|string|max:255',
        'owner_name' => 'nullable|string|max:255',
    ],

    'options' => [
        'property_types' => [
            'garden' => 'Garden Style',
            'midrise' => 'Mid-Rise',
            'highrise' => 'High-Rise',
            'industrial' => 'Industrial',
            'bungalo' => 'Bungalow',
        ],
        'contracting_statuses' => [
            'executed' => 'Executed',
            'quoted' => 'Quoted',
            'started' => 'Started',
            'closed' => 'Closed',
        ],
        'report_statuses' => [
            'not-started' => 'Not Started',
            'in-progress' => 'In Progress',
            'in-review' => 'In Review',
            'completed' => 'Completed',
        ],
    ],

    'upload' => [
        'max_file_size' => '10MB',
        'allowed_extensions' => ['csv', 'xlsx', 'xls'],
        'storage_path' => 'uploads/hb837',
        'temp_retention_days' => 7,
    ],

    'export' => [
        'default_format' => 'xlsx',
        'available_formats' => ['xlsx', 'csv', 'pdf'],
        'storage_path' => 'exports/hb837',
        'batch_size' => 1000,
    ],

    'backup' => [
        'storage_path' => 'backups/hb837',
        'retention_days' => 30,
        'auto_backup' => true,
        'compression' => true,
    ],

    'permissions' => [
        'view' => 'hb837.view',
        'create' => 'hb837.create',
        'edit' => 'hb837.edit',
        'delete' => 'hb837.delete',
        'import' => 'hb837.import',
        'export' => 'hb837.export',
        'backup' => 'hb837.backup',
    ],

    'ui' => [
        'items_per_page' => 25,
        'max_preview_rows' => 10,
        'refresh_interval' => 30000, // 30 seconds
    ],
];

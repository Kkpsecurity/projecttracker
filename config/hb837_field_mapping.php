<?php

return [
    'field_mapping' => array (

    'address' =>
      array(
        0 => 'Address',
        1 => 'Property Address',
      ),
    'agreement_submitted' =>
      array(
        0 => 'Agreement Submitted',
      ),

    'billing_req_submitted' =>
      array(
        0 => 'Billing Req Submitted',
        1 => 'Billing Request Submitted',
        2 => 'Billing Submitted',
      ),
    'city' =>
      array(
        0 => 'City',
      ),
    'assigned_consultant_id' =>
      array(
        0 => 'Assigned Consultant ID',
        1 => 'Consultant ID',
        2 => 'Assigned Consultant', // Map "Assigned Consultant" to the ID field
      ),
    'consultant_name' =>
      array(
        0 => 'Consultant Name',
      ),
    'consultant_notes' =>
      array(
        0 => 'Consultant Notes',
      ),
    'contracting_status' =>
      array(
        0 => 'Contracting Status',
      ),
    'county' =>
      array(
        0 => 'County',
      ),
    'financial_notes' =>
      array(
        0 => 'Financial Notes',
      ),
    'macro_client' =>
      array(
        0 => 'Macro Client',
      ),
    'macro_client_contact' =>
      array(
        0 => 'Macro Client Contact',
      ),
    'macro_client_email' =>
      array(
        0 => 'Macro Client Email',
      ),
    'macro_contact' =>
      array(
        0 => 'Macro Contact',
      ),
    'macro_email' =>
      array(
        0 => 'Macro Email',
      ),
    'management_company' =>
      array(
        0 => 'Management Company',
      ),
    'notes' =>
      array(
        0 => 'Notes',
      ),
    'owner' =>
      array(
        0 => 'Owner',
      ),
    'owner_id' =>
      array(
        0 => 'Owner ID',
      ),
    'owner_name' =>
      array(
        0 => 'Owner Name',
      ),

    'phone' =>
      array(
        0 => 'Phone',
        1 => 'Phone Number',
      ),
    'project_net_profit' =>
      array(
        0 => 'Project Net Profit',
        1 => 'Net Profit',
      ),

    'property_manager_email' =>
      array(
        0 => 'Property Manager Email',
        1 => 'PM Email',
        2 => 'Prop Manager Email',
        3 => 'Property Email',
      ),
    'property_manager_name' =>
      array(
        0 => 'Property Manager Name',
        1 => 'Property Manager (PM)',
        2 => 'Property Manager',
        3 => 'PM',
        4 => 'Prop Manager',
      ),
    'property_name' =>
      array(
        0 => 'Property Name',
        1 => 'Property',
      ),
    'property_type' =>
      array(
        0 => 'Property Type',
        1 => 'Type',
      ),
    'quoted_price' =>
      array(
        0 => 'Quoted Price',
        1 => 'Price',
      ),
    'quoted_rate' =>
      array(
        0 => 'Quoted Rate',
      ),

    'regional_manager_email' =>
      array(
        0 => 'Regional Manager Email',
        1 => 'RM Email',
        2 => 'Regional Email',
      ),
    'regional_manager_name' =>
      array(
        0 => 'Regional Manager Name',
        1 => 'Regional Manager',
        2 => 'Regional Manager (RM)',
        3 => 'RM Name',
        4 => 'Regional',
      ),
    'report_status' =>
      array(
        0 => 'Report Status',
        1 => 'Status',
        2 => 'Progress',
        3 => 'Report Progress',
        4 => 'Project Status',
      ),
    'report_submitted' =>
      array(
        0 => 'Report Submitted',
      ),
    'scheduled_date_of_inspection' =>
      array(
        0 => 'Scheduled Date of Inspection',
        1 => 'Inspection Date',
      ),
    'securitygauge_crime_risk' =>
      array(
        0 => 'SecurityGauge Crime Risk',
      ),
    'state' =>
      array(
        0 => 'State',
      ),
    'sub_fees_estimated_expenses' =>
      array(
        0 => 'Sub Fees Estimated Expenses',
        1 => 'Estimated Expenses',
        2 => 'Subcontractor Fees or Estimated Expenses',
      ),
    'units' =>
      array(
        0 => 'Units',
        1 => 'Number of Units',
      ),
    'user_id' =>
      array(
        0 => 'User ID',
      ),
    'zip' =>
      array(
        0 => 'Zip',
        1 => 'Zip Code',
      ),
),

    'import_rules' => array (
  'required_fields' => 
  array (
    'property_name_or_address' => 'At least one of property_name or address must be provided',
  ),
  'default_values' => 
  array (
    'report_status' => 'not-started',
    'contracting_status' => 'quoted',
        'user_id' => 20,
  ),
  'update_rules' => 
  array (
    'skip_user_id' => true,
    'empty_to_value' => true,
    'value_changed' => true,
  ),
),

    'validation_rules' => array (
  'property_type' => 
  array (
    'allowed_values' => 
    array (
      0 => 'garden',
      1 => 'midrise',
      2 => 'highrise',
      3 => 'industrial',
      4 => 'bungalo',
    ),
  ),
  'report_status' => 
  array (
    'allowed_values' => 
    array (
      0 => 'not-started',
            1 => 'underway',
      2 => 'in-review',
      3 => 'completed',
    ),
  ),
  'contracting_status' => 
  array (
    'allowed_values' => 
    array (
      0 => 'quoted',
      1 => 'started',
      2 => 'executed',
      3 => 'closed',
    ),
  ),
  'state' => 
  array (
    'max_length' => 2,
    'transform' => 'uppercase',
  ),
  'zip' => 
  array (
    'max_length' => 10,
    'pattern' => 'numeric_and_dash_only',
  ),
  'phone' => 
  array (
    'max_length' => 15,
    'pattern' => 'phone_format',
  ),
),

    'transformations' => array (
  'date_fields' => 
  array (
    0 => 'scheduled_date_of_inspection',
    1 => 'report_submitted',
    2 => 'billing_req_sent',
    3 => 'agreement_submitted',
  ),
  'money_fields' => 
  array (
    0 => 'quoted_price',
        1 => 'quoted_rate',
        2 => 'sub_fees_estimated_expenses',
        3 => 'project_net_profit',
  ),
  'integer_fields' => 
  array (
    0 => 'units',
    1 => 'assigned_consultant_id',
    2 => 'user_id',
    3 => 'owner_id',
  ),
  'text_fields' => 
  array (
    0 => 'property_name',
    1 => 'address',
    2 => 'city',
    3 => 'county',
    4 => 'state',
        5 => 'zip',
        6 => 'phone',
        7 => 'assigned_consultant',
        8 => 'consultant_name',
        9 => 'owner',
        10 => 'owner_name',
        11 => 'property_manager_name',
        12 => 'regional_manager_name',
        13 => 'management_company',
        14 => 'macro_client',
        15 => 'macro_client_contact',
        16 => 'macro_contact',
        17 => 'securitygauge_crime_risk',
      ),
    'email_fields' =>
      array(
        0 => 'property_manager_email',
        1 => 'regional_manager_email',
        2 => 'macro_client_email',
        3 => 'macro_email',
      ),
    'note_fields' =>
      array(
        0 => 'notes',
        1 => 'consultant_notes',
        2 => 'financial_notes',
  ),
),

  'status_maps' => array(
    'report_status' =>
      array(
    'not started' => 'not-started',
    'not-started' => 'not-started',
        'in progress' => 'underway',
        'in-progress' => 'underway',
        'underway' => 'underway',
        'ongoing' => 'underway',
        'active' => 'underway',
    'in review' => 'in-review',
    'in-review' => 'in-review',
        'review' => 'in-review',
    'completed' => 'completed',
    'complete' => 'completed',
        'done' => 'completed',
        'finished' => 'completed',
  ),
  'contracting_status' => 
  array (
    'quote' => 'quoted',
    'quoted' => 'quoted',
    'start' => 'started',
    'started' => 'started',
    'execute' => 'executed',
    'executed' => 'executed',
    'close' => 'closed',
    'closed' => 'closed',
  ),
)
];

<?php

return [
    'field_mapping' => array (
  'test_field' => 
  array (
    0 => 'Test Column',
    1 => 'Alt Test',
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
    'user_id' => 1,
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
      1 => 'in-progress',
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
    1 => 'sub_fees_estimated_expenses',
    2 => 'project_net_profit',
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
  ),
),

    'status_maps' => array (
  'report_status' => 
  array (
    'not started' => 'not-started',
    'not-started' => 'not-started',
    'in progress' => 'in-progress',
    'in-progress' => 'in-progress',
    'in review' => 'in-review',
    'in-review' => 'in-review',
    'completed' => 'completed',
    'complete' => 'completed',
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HB837ImportFieldConfig;

class HB837ImportFieldConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load existing configuration
        $currentMapping = config('hb837_field_mapping.field_mapping', []);

        if (empty($currentMapping)) {
            $this->command->info('No existing field mapping found. Creating basic configuration...');
            $currentMapping = $this->getBasicFieldMapping();
        }

        $sortOrder = 0;

        foreach ($currentMapping as $databaseField => $excelMappings) {
            // Skip if already exists
            if (HB837ImportFieldConfig::where('database_field', $databaseField)->exists()) {
                continue;
            }

            // Determine field properties
            $fieldProperties = $this->getFieldProperties($databaseField);

            HB837ImportFieldConfig::create([
                'database_field' => $databaseField,
                'field_label' => $fieldProperties['label'],
                'description' => $fieldProperties['description'],
                'field_type' => $fieldProperties['type'],
                'max_length' => $fieldProperties['max_length'],
                'nullable' => $fieldProperties['nullable'],
                'default_value' => $fieldProperties['default_value'],
                'is_foreign_key' => $fieldProperties['is_foreign_key'],
                'foreign_table' => $fieldProperties['foreign_table'],
                'foreign_key_column' => $fieldProperties['foreign_key_column'],
                'is_system_field' => $fieldProperties['is_system_field'],
                'is_config_field' => true, // Mark as config field (immutable)
                'is_custom_field' => false, // Not a custom field
                'excel_column_mappings' => $excelMappings,
                'validation_rules' => $fieldProperties['validation_rules'],
                'enum_values' => $fieldProperties['enum_values'],
                'transformation_type' => $fieldProperties['transformation_type'],
                'transformation_options' => $fieldProperties['transformation_options'],
                'is_required_for_import' => $fieldProperties['is_required_for_import'],
                'is_updatable' => $fieldProperties['is_updatable'],
                'is_creatable' => $fieldProperties['is_creatable'],
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]);

            $sortOrder += 10;
        }

        $this->command->info('HB837 import field configurations seeded successfully.');
    }

    /**
     * Get field properties based on field name
     */
    private function getFieldProperties($fieldName): array
    {
        $properties = [
            'label' => ucwords(str_replace('_', ' ', $fieldName)),
            'description' => "Auto-generated field for {$fieldName}",
            'type' => 'string',
            'max_length' => null,
            'nullable' => true,
            'default_value' => null,
            'is_foreign_key' => false,
            'foreign_table' => null,
            'foreign_key_column' => null,
            'is_system_field' => false,
            'validation_rules' => null,
            'enum_values' => null,
            'transformation_type' => null,
            'transformation_options' => null,
            'is_required_for_import' => false,
            'is_updatable' => true,
            'is_creatable' => true,
        ];

        // Specific field configurations
        switch ($fieldName) {
            case 'id':
            case 'user_id':
            case 'created_at':
            case 'updated_at':
                $properties['is_system_field'] = true;
                $properties['is_updatable'] = false;
                break;

            case 'assigned_consultant_id':
                $properties['type'] = 'foreign_key';
                $properties['is_foreign_key'] = true;
                $properties['foreign_table'] = 'consultants';
                $properties['foreign_key_column'] = 'id';
                $properties['nullable'] = true;
                break;

            case 'owner_id':
                $properties['type'] = 'foreign_key';
                $properties['is_foreign_key'] = true;
                $properties['foreign_table'] = 'users';
                $properties['foreign_key_column'] = 'id';
                $properties['nullable'] = true;
                break;

            case 'property_name':
            case 'address':
                $properties['max_length'] = 255;
                $properties['is_required_for_import'] = true;
                $properties['transformation_type'] = 'trim';
                break;

            case 'city':
            case 'county':
                $properties['max_length'] = 100;
                $properties['transformation_type'] = 'trim';
                break;

            case 'state':
                $properties['max_length'] = 2;
                $properties['transformation_type'] = 'uppercase';
                break;

            case 'zip':
                $properties['max_length'] = 10;
                $properties['transformation_type'] = 'phone';
                break;

            case 'phone':
                $properties['max_length'] = 15;
                $properties['transformation_type'] = 'phone';
                break;

            case 'email':
            case 'property_manager_email':
            case 'regional_manager_email':
            case 'macro_email':
                $properties['max_length'] = 255;
                $properties['transformation_type'] = 'email';
                $properties['validation_rules'] = ['email'];
                break;

            case 'report_status':
                $properties['type'] = 'enum';
                $properties['enum_values'] = ['not-started', 'in-progress', 'in-review', 'completed'];
                $properties['transformation_type'] = 'status_normalize';
                $properties['default_value'] = 'not-started';
                break;

            case 'contracting_status':
                $properties['type'] = 'enum';
                $properties['enum_values'] = ['quoted', 'started', 'executed', 'closed'];
                $properties['transformation_type'] = 'status_normalize';
                $properties['default_value'] = 'quoted';
                break;

            case 'property_type':
                $properties['type'] = 'enum';
                $properties['enum_values'] = ['garden', 'midrise', 'highrise', 'industrial', 'bungalo'];
                break;

            case 'units':
                $properties['type'] = 'integer';
                break;

            case 'quoted_price':
            case 'sub_fees_estimated_expenses':
            case 'project_net_profit':
                $properties['type'] = 'decimal';
                $properties['transformation_type'] = 'money';
                break;

            case 'scheduled_date_of_inspection':
            case 'report_submitted':
            case 'billing_req_sent':
            case 'agreement_submitted':
                $properties['type'] = 'date';
                $properties['transformation_type'] = 'date';
                break;

            case 'notes':
            case 'financial_notes':
            case 'private_notes':
                $properties['type'] = 'text';
                break;
        }

        return $properties;
    }

    /**
     * Get basic field mapping if none exists
     */
    private function getBasicFieldMapping(): array
    {
        return [
            'property_name' => ['Property Name', 'PropertyName', 'Name', 'Property'],
            'address' => ['Address', 'Property Address', 'Location', 'Street Address'],
            'city' => ['City'],
            'county' => ['County'],
            'state' => ['State', 'ST'],
            'zip' => ['Zip', 'ZIP', 'Zip Code'],
            'property_type' => ['Property Type', 'Type'],
            'units' => ['Units', 'Unit Count'],
            'owner_name' => ['Owner Name', 'Owner'],
            'phone' => ['Phone', 'Phone Number'],
            'management_company' => ['Management Company'],
            'property_manager_name' => ['Property Manager Name'],
            'property_manager_email' => ['Property Manager Email'],
            'report_status' => ['Report Status', 'Status'],
            'contracting_status' => ['Contracting Status'],
            'quoted_price' => ['Quoted Price', 'Price'],
            'notes' => ['Notes', 'Comments'],
        ];
    }
}

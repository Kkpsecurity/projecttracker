<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HB837ImportFieldConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HB837ImportConfigController extends Controller
{
    /**
     * Display a listing of the import field configurations.
     */
    public function index()
    {
        $configFields = HB837ImportFieldConfig::configFields()->ordered()->get();
        $customFields = HB837ImportFieldConfig::customFields()->ordered()->get();

        return view('admin.hb837-import-config.index', compact('configFields', 'customFields'));
    }

    /**
     * Show the form for creating a new field configuration.
     */
    public function create()
    {
        $fieldTypes = HB837ImportFieldConfig::getFieldTypeOptions();
        $transformationTypes = HB837ImportFieldConfig::getTransformationTypeOptions();

        return view('admin.hb837-import-config.create', compact('fieldTypes', 'transformationTypes'));
    }

    /**
     * Store a newly created field configuration.
     */
    public function store(Request $request)
    {
        $validator = $this->validateField($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $fieldConfig = HB837ImportFieldConfig::create([
                'database_field' => $request->database_field,
                'field_label' => $request->field_label,
                'description' => $request->description,
                'field_type' => $request->field_type,
                'max_length' => $request->max_length,
                'nullable' => $request->has('nullable'),
                'default_value' => $request->default_value,
                'is_foreign_key' => $request->field_type === 'foreign_key',
                'foreign_table' => $request->foreign_table,
                'foreign_key_column' => $request->foreign_key_column,
                'is_config_field' => false, // New fields are not config fields
                'is_custom_field' => true, // New fields are custom fields
                'excel_column_mappings' => $this->parseExcelMappings($request->excel_column_mappings),
                'validation_rules' => $this->parseValidationRules($request->validation_rules),
                'enum_values' => $this->parseEnumValues($request->enum_values),
                'transformation_type' => $request->transformation_type,
                'transformation_options' => $this->parseTransformationOptions($request->transformation_options),
                'is_required_for_import' => $request->has('is_required_for_import'),
                'is_updatable' => $request->has('is_updatable'),
                'is_creatable' => $request->has('is_creatable'),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            // Create database column if requested
            if ($request->has('create_database_column')) {
                if (!$fieldConfig->createDatabaseColumn()) {
                    return redirect()->back()
                        ->with('error', 'Field configuration created but database column creation failed. Check logs for details.')
                        ->withInput();
                }
            }

            // Update configuration file
            HB837ImportFieldConfig::updateConfigurationFile();

            return redirect()->route('admin.hb837-import-config.index')
                ->with('success', 'Field configuration created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating field configuration', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Error creating field configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified field configuration.
     */
    public function show(HB837ImportFieldConfig $hb837ImportConfig)
    {
        $columnExists = $hb837ImportConfig->columnExists();

        return view('admin.hb837-import-config.show', compact('hb837ImportConfig', 'columnExists'));
    }

    /**
     * Show the form for editing the specified field configuration.
     */
    public function edit(HB837ImportFieldConfig $hb837ImportConfig)
    {
        if (!$hb837ImportConfig->canBeModified()) {
            return redirect()->route('admin.hb837-import-config.index')
                ->with('error', 'This system field cannot be modified.');
        }

        $fieldTypes = HB837ImportFieldConfig::getFieldTypeOptions();
        $transformationTypes = HB837ImportFieldConfig::getTransformationTypeOptions();

        return view('admin.hb837-import-config.edit', compact('hb837ImportConfig', 'fieldTypes', 'transformationTypes'));
    }

    /**
     * Update the specified field configuration.
     */
    public function update(Request $request, HB837ImportFieldConfig $hb837ImportConfig)
    {
        if (!$hb837ImportConfig->canBeModified()) {
            return redirect()->route('admin.hb837-import-config.index')
                ->with('error', 'This system field cannot be modified.');
        }

        $validator = $this->validateField($request, $hb837ImportConfig->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $hb837ImportConfig->update([
                'field_label' => $request->field_label,
                'description' => $request->description,
                'max_length' => $request->max_length,
                'nullable' => $request->has('nullable'),
                'default_value' => $request->default_value,
                'foreign_table' => $request->foreign_table,
                'foreign_key_column' => $request->foreign_key_column,
                'excel_column_mappings' => $this->parseExcelMappings($request->excel_column_mappings),
                'validation_rules' => $this->parseValidationRules($request->validation_rules),
                'enum_values' => $this->parseEnumValues($request->enum_values),
                'transformation_type' => $request->transformation_type,
                'transformation_options' => $this->parseTransformationOptions($request->transformation_options),
                'is_required_for_import' => $request->has('is_required_for_import'),
                'is_updatable' => $request->has('is_updatable'),
                'is_creatable' => $request->has('is_creatable'),
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            // Update configuration file
            HB837ImportFieldConfig::updateConfigurationFile();

            return redirect()->route('admin.hb837-import-config.index')
                ->with('success', 'Field configuration updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating field configuration', [
                'id' => $hb837ImportConfig->id,
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Error updating field configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified field configuration.
     */
    public function destroy(HB837ImportFieldConfig $hb837ImportConfig)
    {
        if (!$hb837ImportConfig->canBeDeleted()) {
            return redirect()->route('admin.hb837-import-config.index')
                ->with('error', 'This field cannot be deleted (system field or foreign key).');
        }

        try {
            // Remove database column if it exists
            if ($hb837ImportConfig->columnExists()) {
                if (!$hb837ImportConfig->removeDatabaseColumn()) {
                    return redirect()->back()
                        ->with('error', 'Failed to remove database column. Check logs for details.');
                }
            }

            $hb837ImportConfig->delete();

            // Update configuration file
            HB837ImportFieldConfig::updateConfigurationFile();

            return redirect()->route('admin.hb837-import-config.index')
                ->with('success', 'Field configuration deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting field configuration', [
                'id' => $hb837ImportConfig->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error deleting field configuration: ' . $e->getMessage());
        }
    }

    /**
     * Create database column for field
     */
    public function createColumn(HB837ImportFieldConfig $hb837ImportConfig)
    {
        if ($hb837ImportConfig->columnExists()) {
            return redirect()->back()
                ->with('error', 'Database column already exists.');
        }

        if ($hb837ImportConfig->createDatabaseColumn()) {
            return redirect()->back()
                ->with('success', 'Database column created successfully.');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to create database column. Check logs for details.');
        }
    }

    /**
     * Sync configuration file from database
     */
    public function syncConfig()
    {
        try {
            HB837ImportFieldConfig::updateConfigurationFile();

            return redirect()->back()
                ->with('success', 'Configuration file synchronized successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to synchronize configuration file: ' . $e->getMessage());
        }
    }

    /**
     * Import existing fields from database schema
     */
    public function importSchema()
    {
        try {
            $columns = Schema::getColumnListing('hb837');
            $imported = 0;

            foreach ($columns as $column) {
                // Skip if already exists
                if (HB837ImportFieldConfig::where('database_field', $column)->exists()) {
                    continue;
                }

                // Get column details
                $columnType = Schema::getColumnType('hb837', $column);
                $fieldType = $this->mapColumnTypeToFieldType($columnType);

                // Determine if it's a foreign key
                $isForeignKey = str_ends_with($column, '_id') && $column !== 'id';

                HB837ImportFieldConfig::create([
                    'database_field' => $column,
                    'field_label' => ucwords(str_replace('_', ' ', $column)),
                    'description' => 'Auto-imported from database schema',
                    'field_type' => $fieldType,
                    'nullable' => true,
                    'is_foreign_key' => $isForeignKey,
                    'is_system_field' => in_array($column, HB837ImportFieldConfig::SYSTEM_FIELDS),
                    'excel_column_mappings' => [ucwords(str_replace('_', ' ', $column))],
                    'is_active' => true,
                    'sort_order' => $imported * 10,
                ]);

                $imported++;
            }

            // Update configuration file
            HB837ImportFieldConfig::updateConfigurationFile();

            return redirect()->back()
                ->with('success', "Imported {$imported} fields from database schema.");

        } catch (\Exception $e) {
            Log::error('Error importing schema', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Error importing schema: ' . $e->getMessage());
        }
    }

    /**
     * Validate field configuration
     */
    private function validateField(Request $request, $excludeId = null)
    {
        $rules = [
            'database_field' => 'required|string|max:255|regex:/^[a-z_][a-z0-9_]*$/|unique:hb837_import_field_configs,database_field' . ($excludeId ? ",{$excludeId}" : ''),
            'field_label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_type' => 'required|in:string,text,integer,decimal,date,boolean,enum,foreign_key',
            'max_length' => 'nullable|integer|min:1|max:65535',
            'default_value' => 'nullable|string|max:255',
            'foreign_table' => 'nullable|string|max:255',
            'foreign_key_column' => 'nullable|string|max:255',
            'excel_column_mappings' => 'required|string',
            'validation_rules' => 'nullable|string',
            'enum_values' => 'nullable|string',
            'transformation_type' => 'nullable|string',
            'transformation_options' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ];

        $messages = [
            'database_field.regex' => 'Database field must contain only lowercase letters, numbers, and underscores, starting with a letter or underscore.',
            'database_field.unique' => 'A field with this database field name already exists.',
            'excel_column_mappings.required' => 'At least one Excel column mapping is required.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Parse Excel column mappings from string
     */
    private function parseExcelMappings($mappings)
    {
        if (empty($mappings)) {
            return [];
        }

        return array_filter(array_map('trim', explode("\n", $mappings)));
    }

    /**
     * Parse validation rules from string
     */
    private function parseValidationRules($rules)
    {
        if (empty($rules)) {
            return null;
        }

        return array_filter(array_map('trim', explode('|', $rules)));
    }

    /**
     * Parse enum values from string
     */
    private function parseEnumValues($values)
    {
        if (empty($values)) {
            return null;
        }

        return array_filter(array_map('trim', explode(',', $values)));
    }

    /**
     * Parse transformation options from string
     */
    private function parseTransformationOptions($options)
    {
        if (empty($options)) {
            return null;
        }

        $parsed = [];
        $lines = explode("\n", $options);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $parsed[trim($key)] = trim($value);
            }
        }

        return $parsed ?: null;
    }

    /**
     * Map database column type to field type
     */
    private function mapColumnTypeToFieldType($columnType)
    {
        $mapping = [
            'varchar' => 'string',
            'string' => 'string',
            'text' => 'text',
            'integer' => 'integer',
            'bigint' => 'integer',
            'decimal' => 'decimal',
            'float' => 'decimal',
            'double' => 'decimal',
            'date' => 'date',
            'datetime' => 'date',
            'timestamp' => 'date',
            'boolean' => 'boolean',
            'enum' => 'enum',
        ];

        return $mapping[$columnType] ?? 'string';
    }
}

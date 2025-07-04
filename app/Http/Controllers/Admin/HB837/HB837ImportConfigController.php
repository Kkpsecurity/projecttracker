<?php

namespace App\Http\Controllers\Admin\HB837;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HB837ImportFieldConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class HB837ImportConfigController extends Controller
{
    protected $configPath;
    protected $configKey = 'hb837_field_mapping.field_mapping';

    public function __construct()
    {
        $this->configPath = config_path('hb837_field_mapping.php');
    }

    /**
     * Display the import configuration management interface
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDatatablesData();
        }

        $fieldMappings = config('hb837_field_mapping.field_mapping', []);
        $tableColumns = $this->getTableColumns();

        // Create configFields collection for the view
        $configFields = collect($fieldMappings)->map(function ($excelColumns, $dbField) use ($tableColumns, $fieldMappings) {
            $columnInfo = $this->getColumnInfo($dbField);

            return (object) [
                'id' => $dbField,
                'database_field' => $dbField,
                'field_label' => ucwords(str_replace('_', ' ', $dbField)),
                'field_type' => $columnInfo['type'] ?? 'string',
                'max_length' => $columnInfo['max_length'] ?? null,
                'excel_column_mappings' => $excelColumns,
                'excel_mappings' => $excelColumns, // Keep both for compatibility
                'is_active' => true,
                'column_exists' => in_array($dbField, $tableColumns),
                'sort_order' => array_search($dbField, array_keys($fieldMappings)) + 1,
                'is_system_field' => in_array($dbField, ['id', 'created_at', 'updated_at']),
                'is_foreign_key' => str_ends_with($dbField, '_id'),
                'is_required_for_import' => false, // Default to false, can be configured later
                'validation_rules' => []
            ];
        });

        // Create customFields collection (empty for now, but expected by view)
        $customFields = collect();

        return view('admin.hb837-import-config.index', compact('fieldMappings', 'tableColumns', 'configFields', 'customFields'));
    }

    /**
     * Get DataTables data for field mappings
     */
    protected function getDatatablesData()
    {
        $fieldMappings = config('hb837_field_mapping.field_mapping', []);
        $tableColumns = $this->getTableColumns();

        $data = [];
        foreach ($fieldMappings as $dbField => $excelColumns) {
            $data[] = [
                'id' => $dbField,
                'database_field' => $dbField,
                'excel_columns' => implode(', ', $excelColumns),
                'excel_columns_count' => count($excelColumns),
                'column_exists' => in_array($dbField, $tableColumns),
                'column_type' => $this->getColumnType($dbField),
                'actions' => $dbField
            ];
        }

        return DataTables::of(collect($data))
            ->addColumn('actions', function ($row) {
                // Convert row to array if it's an object
                $rowData = is_array($row) ? $row : (array) $row;
                return view('admin.hb837-import-config.actions', ['field' => $rowData['database_field']])->render();
            })
            ->addColumn('status', function ($row) {
                // Convert row to array if it's an object
                $rowData = is_array($row) ? $row : (array) $row;
                return $rowData['column_exists']
                    ? '<span class="badge badge-success">DB Column Exists</span>'
                    : '<span class="badge badge-warning">DB Column Missing</span>';
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    /**
     * Show form to create new field mapping
     */
    public function create()
    {
        $columnTypes = $this->getAvailableColumnTypes();
        $validationRules = config('hb837_field_mapping.validation_rules', []);

        return view('admin.hb837-import-config.create', compact('columnTypes', 'validationRules'));
    }

    /**
     * Store new field mapping
     */
    public function store(Request $request)
    {
        $request->validate([
            'database_field' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_type' => 'required|string|max:50',
            'max_length' => 'nullable|integer|min:1|max:255',
            'excel_column_mappings' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            // Legacy fields for backward compatibility
            'excel_columns' => 'nullable|array',
            'excel_columns.*' => 'nullable|string|max:255',
            'column_type' => 'nullable|string',
            'column_length' => 'nullable|integer|min:1|max:255',
            'column_nullable' => 'boolean',
            'column_default' => 'nullable|string|max:255',
            'create_database_column' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // 1. Create or update the field configuration record
            $excelMappings = $request->excel_column_mappings;
            if (!$excelMappings && $request->excel_columns) {
                $excelMappings = implode("\n", $request->excel_columns);
            }

            $configData = [
                'database_field' => $request->database_field,
                'field_label' => $request->field_label,
                'description' => $request->description,
                'field_type' => $request->field_type,
                'max_length' => $request->max_length,
                'excel_column_mappings' => $excelMappings,
                'sort_order' => $request->sort_order ?? 999,
                'is_active' => true,
                'is_custom_field' => true
            ];

            $fieldConfig = HB837ImportFieldConfig::updateOrCreate(
                ['database_field' => $request->database_field],
                $configData
            );

            // 2. Create database column if requested
            if ($request->create_database_column) {
                $columnType = $request->column_type ?? $request->field_type ?? 'varchar';
                $this->createDatabaseColumn(
                    $request->database_field,
                    $columnType,
                    $request->column_length ?? $request->max_length,
                    $request->column_nullable ?? true,
                    $request->column_default
                );
            }

            // 3. Update configuration file for backward compatibility
            if ($request->excel_columns) {
                $this->updateFieldMapping(
                    $request->database_field,
                    $request->excel_columns
                );
            }

            DB::commit();

            return redirect()->route('admin.hb837-import-config.index')
                ->with('success', "Field configuration for '{$request->database_field}' created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating field mapping: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Error creating field mapping: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit field mapping
     */
    public function edit($field)
    {
        $fieldMappings = config('hb837_field_mapping.field_mapping', []);

        if (!isset($fieldMappings[$field])) {
            return redirect()->route('admin.hb837.import-config.index')
                ->with('error', 'Field mapping not found.');
        }

        $columnTypes = $this->getAvailableColumnTypes();
        $currentMapping = [
            'database_field' => $field,
            'excel_columns' => $fieldMappings[$field],
            'column_exists' => $this->columnExists($field),
            'column_type' => $this->getColumnType($field)
        ];

        return view('admin.hb837-import-config.edit', compact('currentMapping', 'columnTypes'));
    }

    /**
     * Update field mapping
     */
    public function update(Request $request, $field)
    {
        $request->validate([
            'excel_columns' => 'required|array|min:1',
            'excel_columns.*' => 'required|string|max:255',
            'column_type' => 'nullable|string',
            'column_length' => 'nullable|integer|min:1|max:255',
            'column_nullable' => 'boolean',
            'modify_database_column' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // 1. Modify database column if requested
            if ($request->modify_database_column && $request->column_type) {
                $this->modifyDatabaseColumn(
                    $field,
                    $request->column_type,
                    $request->column_length,
                    $request->column_nullable ?? true
                );
            }

            // 2. Update configuration file
            $this->updateFieldMapping($field, $request->excel_columns);

            DB::commit();

            return redirect()->route('admin.hb837.import-config.index')
                ->with('success', "Field mapping for '{$field}' updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating field mapping: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Error updating field mapping: ' . $e->getMessage());
        }
    }

    /**
     * Delete field mapping
     */
    public function destroy($field)
    {
        try {
            $this->removeFieldMapping($field);

            return redirect()->route('admin.hb837.import-config.index')
                ->with('success', "Field mapping for '{$field}' deleted successfully!");

        } catch (\Exception $e) {
            Log::error('Error deleting field mapping: ' . $e->getMessage());

            return back()->with('error', 'Error deleting field mapping: ' . $e->getMessage());
        }
    }

    /**
     * Create missing database columns
     */
    public function createMissingColumns()
    {
        try {
            $fieldMappings = config('hb837_field_mapping.field_mapping', []);
            $tableColumns = $this->getTableColumns();
            $created = [];

            foreach ($fieldMappings as $field => $mappings) {
                if (!in_array($field, $tableColumns)) {
                    $this->createDatabaseColumn($field, 'string', null, true, null);
                    $created[] = $field;
                }
            }

            if (empty($created)) {
                return back()->with('info', 'All database columns already exist.');
            }

            return back()->with('success', 'Created missing columns: ' . implode(', ', $created));

        } catch (\Exception $e) {
            Log::error('Error creating missing columns: ' . $e->getMessage());
            return back()->with('error', 'Error creating missing columns: ' . $e->getMessage());
        }
    }

    /**
     * Export current configuration
     */
    public function exportConfig()
    {
        $config = config('hb837_field_mapping');
        $json = json_encode($config, JSON_PRETTY_PRINT);

        return response($json)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="hb837_field_mapping_' . date('Y-m-d_H-i-s') . '.json"');
    }

    /**
     * Import configuration from JSON
     */
    public function importConfig(Request $request)
    {
        $request->validate([
            'config_file' => 'required|file|mimes:json'
        ]);

        try {
            $content = file_get_contents($request->file('config_file')->getRealPath());
            $config = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file');
            }

            // Backup current config
            $backupPath = config_path('hb837_field_mapping_backup_' . date('Y-m-d_H-i-s') . '.php');
            copy($this->configPath, $backupPath);

            // Update config file
            $this->writeConfigFile($config);

            return back()->with('success', 'Configuration imported successfully! Backup saved.');

        } catch (\Exception $e) {
            Log::error('Error importing configuration: ' . $e->getMessage());
            return back()->with('error', 'Error importing configuration: ' . $e->getMessage());
        }
    }

    /**
     * Remove field mapping from configuration file
     */
    private function removeFieldMapping($field)
    {
        $config = include $this->configPath;
        unset($config['field_mapping'][$field]);
        $this->writeConfigFile($config);

        // Clear config cache
        Artisan::call('config:clear');

        Log::info("Removed field mapping for: {$field}");
    }

    /**
     * Display the mapped fields view
     */
    public function mappedFields()
    {
        $fieldMappings = config('hb837_field_mapping.field_mapping', []);
        $importRules = config('hb837_field_mapping.import_rules', []);
        $validationRules = config('hb837_field_mapping.validation_rules', []);
        $transformations = config('hb837_field_mapping.transformations', []);
        $statusMaps = config('hb837_field_mapping.status_maps', []);
        $tableColumns = $this->getTableColumns();

        // Create a detailed view of mappings with additional information
        $mappedFieldsData = [];
        foreach ($fieldMappings as $dbField => $excelColumns) {
            $mappedFieldsData[] = [
                'database_field' => $dbField,
                'excel_columns' => $excelColumns,
                'column_exists' => $this->columnExists($dbField),
                'column_type' => $this->getColumnType($dbField),
                'has_validation' => isset($validationRules[$dbField]),
                'has_transformation' => $this->hasTransformation($dbField, $transformations),
                'has_status_map' => isset($statusMaps[$dbField]),
                'is_required' => $this->isRequiredField($dbField, $importRules),
            ];
        }

        // Debug output (remove after testing)
        Log::info('Mapped Fields Data:', [
            'mappedFieldsData_count' => count($mappedFieldsData),
            'fieldMappings_count' => count($fieldMappings),
            'sample_data' => $mappedFieldsData
        ]);

        return view('admin.hb837-import-config.mapped-fields', compact(
            'mappedFieldsData',
            'fieldMappings',
            'importRules',
            'validationRules',
            'transformations',
            'statusMaps',
            'tableColumns'
        ));
    }

    /**
     * Check if field has any transformation rules
     */
    private function hasTransformation($field, $transformations)
    {
        foreach ($transformations as $transformationType => $fields) {
            if (in_array($field, $fields)) {
                return $transformationType;
            }
        }
        return false;
    }

    /**
     * Check if field is required based on import rules
     */
    private function isRequiredField($field, $importRules)
    {
        $requiredFields = $importRules['required_fields'] ?? [];
        return in_array($field, array_keys($requiredFields)) ||
            in_array($field, ['address', 'property_name']); // These are logically required
    }

    /**
     * Show field mapping details
     */
    public function show($field)
    {
        $fieldMappings = config('hb837_field_mapping.field_mapping', []);

        if (!isset($fieldMappings[$field])) {
            return redirect()->route('admin.hb837-import-config.index')
                ->with('error', 'Field mapping not found.');
        }

        $columnInfo = $this->getColumnInfo($field);
        $fieldDetails = [
            'database_field' => $field,
            'excel_columns' => $fieldMappings[$field],
            'column_exists' => $this->columnExists($field),
            'column_type' => $this->getColumnType($field),
            'column_info' => $columnInfo,
            'is_system_field' => in_array($field, ['id', 'created_at', 'updated_at']),
            'is_foreign_key' => str_ends_with($field, '_id')
        ];

        return view('admin.hb837-import-config.show', compact('fieldDetails'));
    }

    /**
     * Create database column for a specific field
     */
    public function createColumn($field)
    {
        try {
            $fieldMappings = config('hb837_field_mapping.field_mapping', []);

            if (!isset($fieldMappings[$field])) {
                return redirect()->route('admin.hb837-import-config.index')
                    ->with('error', 'Field mapping not found.');
            }

            if ($this->columnExists($field)) {
                return redirect()->route('admin.hb837-import-config.index')
                    ->with('info', "Database column '{$field}' already exists.");
            }

            // Create the column with default string type
            $this->createDatabaseColumn($field, 'string', null, true, null);

            return redirect()->route('admin.hb837-import-config.index')
                ->with('success', "Database column '{$field}' created successfully!");

        } catch (\Exception $e) {
            Log::error('Error creating database column: ' . $e->getMessage());
            return redirect()->route('admin.hb837-import-config.index')
                ->with('error', 'Error creating database column: ' . $e->getMessage());
        }
    }

    // ========================
    // PRIVATE HELPER METHODS
    // ========================

    /**
     * Get all columns in the hb837 table
     */
    private function getTableColumns()
    {
        return Schema::getColumnListing('hb837');
    }

    /**
     * Check if column exists in table
     */
    private function columnExists($column)
    {
        return Schema::hasColumn('hb837', $column);
    }

    /**
     * Get column type from database
     */
    private function getColumnType($column)
    {
        if (!$this->columnExists($column)) {
            return 'N/A';
        }

        try {
            // PostgreSQL compatible query to get column type
            $result = DB::select("
                SELECT data_type, character_maximum_length, numeric_precision, numeric_scale
                FROM information_schema.columns 
                WHERE table_name = 'hb837' 
                AND column_name = ?
            ", [$column]);

            if (empty($result)) {
                return 'Unknown';
            }

            $columnInfo = $result[0];
            $dataType = $columnInfo->data_type;

            // Format the type similar to MySQL DESCRIBE output
            switch ($dataType) {
                case 'character varying':
                    return 'varchar(' . ($columnInfo->character_maximum_length ?: 255) . ')';
                case 'character':
                    return 'char(' . ($columnInfo->character_maximum_length ?: 1) . ')';
                case 'text':
                    return 'text';
                case 'integer':
                    return 'int';
                case 'bigint':
                    return 'bigint';
                case 'smallint':
                    return 'smallint';
                case 'numeric':
                    return 'decimal(' . ($columnInfo->numeric_precision ?: 10) . ',' . ($columnInfo->numeric_scale ?: 0) . ')';
                case 'timestamp without time zone':
                    return 'timestamp';
                case 'timestamp with time zone':
                    return 'timestamptz';
                case 'date':
                    return 'date';
                case 'time without time zone':
                    return 'time';
                case 'boolean':
                    return 'boolean';
                case 'json':
                case 'jsonb':
                    return 'json';
                default:
                    return $dataType;
            }
        } catch (\Exception $e) {
            Log::error('Error getting column type for ' . $column . ': ' . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * Get available column types for forms
     */
    private function getAvailableColumnTypes()
    {
        return [
            'string' => 'String (VARCHAR)',
            'text' => 'Text (TEXT)',
            'integer' => 'Integer (INT)',
            'decimal' => 'Decimal (DECIMAL)',
            'boolean' => 'Boolean (TINYINT)',
            'date' => 'Date (DATE)',
            'datetime' => 'DateTime (DATETIME)',
            'timestamp' => 'Timestamp (TIMESTAMP)',
            'json' => 'JSON (JSON)'
        ];
    }

    /**
     * Create new database column
     */
    private function createDatabaseColumn($name, $type, $length = null, $nullable = true, $default = null)
    {
        Schema::table('hb837', function (Blueprint $table) use ($name, $type, $length, $nullable, $default) {
            $column = null;

            switch ($type) {
                case 'string':
                    $column = $table->string($name, $length ?? 255);
                    break;
                case 'text':
                    $column = $table->text($name);
                    break;
                case 'integer':
                    $column = $table->integer($name);
                    break;
                case 'decimal':
                    $column = $table->decimal($name, 10, 2);
                    break;
                case 'boolean':
                    $column = $table->boolean($name);
                    break;
                case 'date':
                    $column = $table->date($name);
                    break;
                case 'datetime':
                    $column = $table->dateTime($name);
                    break;
                case 'timestamp':
                    $column = $table->timestamp($name);
                    break;
                case 'json':
                    $column = $table->json($name);
                    break;
                default:
                    $column = $table->string($name);
                    break;
            }

            if ($nullable) {
                $column->nullable();
            }

            if ($default !== null) {
                $column->default($default);
            }
        });

        Log::info("Created database column: {$name} ({$type})");
    }

    /**
     * Modify existing database column
     */
    private function modifyDatabaseColumn($name, $type, $length = null, $nullable = true)
    {
        Schema::table('hb837', function (Blueprint $table) use ($name, $type, $length, $nullable) {
            switch ($type) {
                case 'string':
                    $column = $table->string($name, $length ?? 255)->change();
                    break;
                case 'text':
                    $column = $table->text($name)->change();
                    break;
                case 'integer':
                    $column = $table->integer($name)->change();
                    break;
                case 'decimal':
                    $column = $table->decimal($name, 10, 2)->change();
                    break;
                case 'boolean':
                    $column = $table->boolean($name)->change();
                    break;
                case 'date':
                    $column = $table->date($name)->change();
                    break;
                case 'datetime':
                    $column = $table->dateTime($name)->change();
                    break;
                case 'timestamp':
                    $column = $table->timestamp($name)->change();
                    break;
                case 'json':
                    $column = $table->json($name)->change();
                    break;
                default:
                    $column = $table->string($name)->change();
                    break;
            }

            if ($nullable) {
                $column->nullable();
            }
        });

        Log::info("Modified database column: {$name} ({$type})");
    }

    /**
     * Update field mapping in configuration file
     */
    private function updateFieldMapping($field, $excelColumns)
    {
        $config = include $this->configPath;
        $config['field_mapping'][$field] = $excelColumns;
        $this->writeConfigFile($config);

        // Clear config cache
        Artisan::call('config:clear');

        Log::info("Updated field mapping for: {$field}");
    }

    /**
     * Write configuration array to file
     */
    private function writeConfigFile($config)
    {
        $content = "<?php\n\nreturn " . var_export($config, true) . ";";
        File::put($this->configPath, $content);
    }

    /**
     * Get detailed column information including type and max_length
     */
    private function getColumnInfo($columnName)
    {
        try {
            $columnInfo = DB::select("
                SELECT 
                    column_name,
                    data_type,
                    character_maximum_length,
                    is_nullable,
                    column_default
                FROM information_schema.columns 
                WHERE table_name = 'hb837' 
                AND column_name = ?
            ", [$columnName]);

            if (empty($columnInfo)) {
                return ['type' => 'string', 'max_length' => null];
            }

            $info = $columnInfo[0];
            $type = $this->mapPostgresTypeToLaravel($info->data_type);

            return [
                'type' => $type,
                'max_length' => $info->character_maximum_length,
                'is_nullable' => $info->is_nullable === 'YES',
                'default' => $info->column_default
            ];
        } catch (\Exception $e) {
            Log::warning("Could not get column info for {$columnName}: " . $e->getMessage());
            return ['type' => 'string', 'max_length' => null];
        }
    }

    /**
     * Map PostgreSQL data types to Laravel/display types
     */
    private function mapPostgresTypeToLaravel($pgType)
    {
        $mapping = [
            'character varying' => 'string',
            'varchar' => 'string',
            'text' => 'text',
            'integer' => 'integer',
            'bigint' => 'integer',
            'smallint' => 'integer',
            'numeric' => 'decimal',
            'decimal' => 'decimal',
            'boolean' => 'boolean',
            'date' => 'date',
            'timestamp' => 'datetime',
            'timestamptz' => 'datetime',
            'json' => 'json',
            'jsonb' => 'json'
        ];

        return $mapping[$pgType] ?? 'string';
    }

}

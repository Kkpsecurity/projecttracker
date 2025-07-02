<?php

namespace App\Http\Controllers\Admin\HB837;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        return view('admin.hb837.import-config.index', compact('fieldMappings', 'tableColumns'));
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

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin.hb837.import-config.actions', ['field' => $row['database_field']]);
            })
            ->addColumn('status', function ($row) {
                return $row['column_exists']
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

        return view('admin.hb837.import-config.create', compact('columnTypes', 'validationRules'));
    }

    /**
     * Store new field mapping
     */
    public function store(Request $request)
    {
        $request->validate([
            'database_field' => 'required|string|max:255|regex:/^[a-z_]+$/',
            'excel_columns' => 'required|array|min:1',
            'excel_columns.*' => 'required|string|max:255',
            'column_type' => 'required|string',
            'column_length' => 'nullable|integer|min:1|max:255',
            'column_nullable' => 'boolean',
            'column_default' => 'nullable|string|max:255',
            'create_database_column' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // 1. Create database column if requested
            if ($request->create_database_column) {
                $this->createDatabaseColumn(
                    $request->database_field,
                    $request->column_type,
                    $request->column_length,
                    $request->column_nullable ?? true,
                    $request->column_default
                );
            }

            // 2. Update configuration file
            $this->updateFieldMapping(
                $request->database_field,
                $request->excel_columns
            );

            DB::commit();

            return redirect()->route('admin.hb837.import-config.index')
                ->with('success', "Field mapping for '{$request->database_field}' created successfully!");

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

        return view('admin.hb837.import-config.edit', compact('currentMapping', 'columnTypes'));
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

        $columns = DB::select("DESCRIBE hb837 {$column}");
        return isset($columns[0]) ? $columns[0]->Type : 'Unknown';
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
     * Write configuration array to file
     */
    private function writeConfigFile($config)
    {
        $content = "<?php\n\nreturn " . var_export($config, true) . ";";
        File::put($this->configPath, $content);
    }
}

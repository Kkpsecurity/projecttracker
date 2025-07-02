<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HB837ImportFieldConfig extends Model
{
    use HasFactory;

    protected $table = 'hb837_import_field_configs';

    protected $fillable = [
        'database_field',
        'field_label',
        'description',
        'field_type',
        'max_length',
        'nullable',
        'default_value',
        'is_foreign_key',
        'foreign_table',
        'foreign_key_column',
        'is_system_field',
        'is_config_field', // New: field from config (immutable)
        'is_custom_field', // New: custom field (manageable)
        'excel_column_mappings',
        'validation_rules',
        'enum_values',
        'transformation_type',
        'transformation_options',
        'is_required_for_import',
        'is_updatable',
        'is_creatable',
        'is_active',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'excel_column_mappings' => 'array',
        'validation_rules' => 'array',
        'enum_values' => 'array',
        'transformation_options' => 'array',
        'metadata' => 'array',
        'nullable' => 'boolean',
        'is_foreign_key' => 'boolean',
        'is_system_field' => 'boolean',
        'is_config_field' => 'boolean',
        'is_custom_field' => 'boolean',
        'is_required_for_import' => 'boolean',
        'is_updatable' => 'boolean',
        'is_creatable' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * System fields that cannot be deleted
     */
    const SYSTEM_FIELDS = [
        'id',
        'user_id',
        'assigned_consultant_id',
        'owner_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Foreign key fields that cannot be deleted
     */
    const FOREIGN_KEY_FIELDS = [
        'user_id' => ['table' => 'users', 'column' => 'id'],
        'assigned_consultant_id' => ['table' => 'consultants', 'column' => 'id'],
        'owner_id' => ['table' => 'users', 'column' => 'id'], // or owners table if exists
    ];

    /**
     * Get active field configurations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get non-system fields (can be modified/deleted)
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system_field', false);
    }

    /**
     * Get config fields (immutable, from configuration)
     */
    public function scopeConfigFields($query)
    {
        return $query->where('is_config_field', true);
    }

    /**
     * Get custom fields (manageable by users)
     */
    public function scopeCustomFields($query)
    {
        return $query->where('is_custom_field', true);
    }

    /**
     * Get fields that can be deleted (not system, not config)
     */
    public function scopeDeletable($query)
    {
        return $query->where('is_system_field', false)
                    ->where('is_config_field', false);
    }

    /**
     * Get fields that can be edited (not system fields, but config labels can be updated)
     */
    public function scopeEditable($query)
    {
        return $query->where('is_system_field', false);
    }

    /**
     * Get fields by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('field_type', $type);
    }

    /**
     * Get ordered fields
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('field_label');
    }

    /**
     * Check if field can be deleted
     */
    public function canBeDeleted(): bool
    {
        // System fields and config fields cannot be deleted
        return !$this->is_system_field && !$this->is_config_field;
    }

    /**
     * Check if field can be edited
     */
    public function canBeEdited(): bool
    {
        // System fields cannot be edited, but config fields can have limited editing
        return !$this->is_system_field;
    }

    /**
     * Check if field structure can be modified (type, length, etc.)
     */
    public function canModifyStructure(): bool
    {
        // Only custom fields can have their structure modified
        return $this->is_custom_field && !$this->is_system_field;
    }

    /**
     * Check if this is a config field (immutable structure, but labels can be updated)
     */
    public function isConfigField(): bool
    {
        return $this->is_config_field;
    }

    /**
     * Check if this is a custom field (fully manageable)
     */
    public function isCustomField(): bool
    {
        return $this->is_custom_field;
    }

    /**
     * Check if field can be modified (legacy method)
     */
    public function canBeModified(): bool
    {
        return !$this->is_system_field;
    }

    /**
     * Get field type options
     */
    public static function getFieldTypeOptions(): array
    {
        return [
            'string' => 'String/Text (short)',
            'text' => 'Text (long)',
            'integer' => 'Integer/Number',
            'decimal' => 'Decimal/Money',
            'date' => 'Date',
            'boolean' => 'True/False',
            'enum' => 'Multiple Choice',
            'foreign_key' => 'Foreign Key Reference'
        ];
    }

    /**
     * Get transformation type options
     */
    public static function getTransformationTypeOptions(): array
    {
        return [
            'none' => 'No transformation',
            'date' => 'Date formatting',
            'money' => 'Money/Currency',
            'phone' => 'Phone number',
            'email' => 'Email address',
            'uppercase' => 'Convert to uppercase',
            'lowercase' => 'Convert to lowercase',
            'trim' => 'Remove extra spaces',
            'status_normalize' => 'Normalize status values'
        ];
    }

    /**
     * Check if database column exists
     */
    public function columnExists(): bool
    {
        return Schema::hasColumn('hb837', $this->database_field);
    }

    /**
     * Create database column based on field configuration
     */
    public function createDatabaseColumn(): bool
    {
        try {
            Schema::table('hb837', function ($table) {
                $column = null;

                switch ($this->field_type) {
                    case 'string':
                        $column = $table->string($this->database_field, $this->max_length ?? 255);
                        break;
                    case 'text':
                        $column = $table->text($this->database_field);
                        break;
                    case 'integer':
                        $column = $table->integer($this->database_field);
                        break;
                    case 'decimal':
                        $column = $table->decimal($this->database_field, 10, 2);
                        break;
                    case 'date':
                        $column = $table->date($this->database_field);
                        break;
                    case 'boolean':
                        $column = $table->boolean($this->database_field);
                        break;
                    case 'enum':
                        if (!empty($this->enum_values)) {
                            $column = $table->enum($this->database_field, $this->enum_values);
                        } else {
                            $column = $table->string($this->database_field);
                        }
                        break;
                    case 'foreign_key':
                        $column = $table->foreignId($this->database_field);
                        if ($this->foreign_table && $this->foreign_key_column) {
                            $column->constrained($this->foreign_table, $this->foreign_key_column);
                        }
                        break;
                    default:
                        $column = $table->string($this->database_field);
                }

                if ($column && $this->nullable) {
                    $column->nullable();
                }

                if ($column && $this->default_value) {
                    $column->default($this->default_value);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create database column', [
                'field' => $this->database_field,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove database column
     */
    public function removeDatabaseColumn(): bool
    {
        if (!$this->canBeDeleted()) {
            return false;
        }

        try {
            Schema::table('hb837', function ($table) {
                $table->dropColumn($this->database_field);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove database column', [
                'field' => $this->database_field,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate field mapping array for import configuration
     */
    public static function generateImportMapping(): array
    {
        $fields = self::active()->ordered()->get();
        $mapping = [];

        foreach ($fields as $field) {
            $mapping[$field->database_field] = $field->excel_column_mappings ?: [];
        }

        return $mapping;
    }

    /**
     * Update the configuration file
     */
    public static function updateConfigurationFile(): bool
    {
        try {
            $mapping = self::generateImportMapping();

            // Get other configuration sections
            $importRules = config('hb837_field_mapping.import_rules', []);
            $validationRules = config('hb837_field_mapping.validation_rules', []);
            $transformations = config('hb837_field_mapping.transformations', []);
            $statusMaps = config('hb837_field_mapping.status_maps', []);

            $configContent = "<?php\n\nreturn [\n";
            $configContent .= "    'field_mapping' => " . var_export($mapping, true) . ",\n\n";
            $configContent .= "    'import_rules' => " . var_export($importRules, true) . ",\n\n";
            $configContent .= "    'validation_rules' => " . var_export($validationRules, true) . ",\n\n";
            $configContent .= "    'transformations' => " . var_export($transformations, true) . ",\n\n";
            $configContent .= "    'status_maps' => " . var_export($statusMaps, true) . "\n";
            $configContent .= "];\n";

            $configPath = config_path('hb837_field_mapping.php');
            file_put_contents($configPath, $configContent);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update configuration file', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

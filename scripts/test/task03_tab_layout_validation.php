<?php

/**
 * Task 03: HB837 Admin Form Tab Layout Validation Script
 * 
 * This script validates that all HB837 model fields are properly mapped
 * and organized in the admin form tabs as defined in config/tab_layout.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class HB837TabLayoutValidator
{
    private $tabLayout;
    private $tableName = 'hb837';
    private $errors = [];
    private $warnings = [];
    private $validations = [];

    public function __construct()
    {
        $this->tabLayout = config('tab_layout');
        echo "🔍 HB837 Admin Form Tab Layout Validation\n";
        echo "==========================================\n\n";
    }

    public function validateComplete()
    {
        $this->validateDatabaseSchema();
        $this->validateTabStructure();
        $this->validateFieldMapping();
        $this->validateFieldCoverage();
        $this->validateExcelMapping();
        $this->generateReport();
    }

    private function validateDatabaseSchema()
    {
        echo "📊 Validating Database Schema...\n";
        
        if (!Schema::hasTable($this->tableName)) {
            $this->errors[] = "Table '{$this->tableName}' does not exist";
            return;
        }

        $columns = Schema::getColumnListing($this->tableName);
        $this->validations['database_columns'] = $columns;
        
        echo "   ✅ Table '{$this->tableName}' exists with " . count($columns) . " columns\n";
        echo "   📋 Columns: " . implode(', ', $columns) . "\n\n";
    }

    private function validateTabStructure()
    {
        echo "🗂️ Validating Tab Structure...\n";
        
        $tabs = $this->tabLayout['hb837_tabs'] ?? [];
        $requiredTabs = $this->tabLayout['validation_config']['required_tabs'] ?? [];
        
        foreach ($requiredTabs as $requiredTab) {
            if (!isset($tabs[$requiredTab])) {
                $this->errors[] = "Required tab '{$requiredTab}' is missing";
            } else {
                echo "   ✅ Required tab '{$requiredTab}' exists\n";
            }
        }

        foreach ($tabs as $tabKey => $tabConfig) {
            $this->validateSingleTab($tabKey, $tabConfig);
        }

        echo "\n";
    }

    private function validateSingleTab($tabKey, $tabConfig)
    {
        $requiredProperties = ['label', 'fields'];
        
        foreach ($requiredProperties as $property) {
            if (!isset($tabConfig[$property])) {
                $this->errors[] = "Tab '{$tabKey}' is missing required property '{$property}'";
            }
        }

        $fieldCount = isset($tabConfig['fields']) ? count($tabConfig['fields']) : 0;
        $minFields = $this->tabLayout['validation_config']['minimum_fields_per_tab'] ?? 1;
        $maxFields = $this->tabLayout['validation_config']['maximum_fields_per_tab'] ?? 20;

        if ($fieldCount < $minFields) {
            $this->warnings[] = "Tab '{$tabKey}' has only {$fieldCount} fields (minimum: {$minFields})";
        } elseif ($fieldCount > $maxFields) {
            $this->warnings[] = "Tab '{$tabKey}' has {$fieldCount} fields (maximum: {$maxFields})";
        }

        echo "   📂 Tab '{$tabKey}': {$fieldCount} fields";
        if (isset($tabConfig['priority'])) {
            echo " (priority: {$tabConfig['priority']})";
        }
        echo "\n";
    }

    private function validateFieldMapping()
    {
        echo "🔗 Validating Field Mapping...\n";
        
        $databaseColumns = $this->validations['database_columns'] ?? [];
        $configuredFields = $this->getAllConfiguredFields();
        
        // Check for database columns not in config
        $unmappedColumns = array_diff($databaseColumns, $configuredFields);
        foreach ($unmappedColumns as $column) {
            $this->warnings[] = "Database column '{$column}' is not mapped in any tab";
            echo "   ⚠️ Unmapped column: {$column}\n";
        }

        // Check for config fields not in database
        $missingColumns = array_diff($configuredFields, $databaseColumns);
        foreach ($missingColumns as $field) {
            $this->errors[] = "Configured field '{$field}' does not exist in database";
            echo "   ❌ Missing database column: {$field}\n";
        }

        $mappedCount = count($configuredFields) - count($missingColumns);
        $totalColumns = count($databaseColumns);
        $coveragePercent = $totalColumns > 0 ? round(($mappedCount / $totalColumns) * 100, 1) : 0;
        
        echo "   📊 Field Coverage: {$mappedCount}/{$totalColumns} ({$coveragePercent}%)\n\n";
    }

    private function getAllConfiguredFields()
    {
        $fields = [];
        $tabs = $this->tabLayout['hb837_tabs'] ?? [];
        
        foreach ($tabs as $tabKey => $tabConfig) {
            if (isset($tabConfig['fields'])) {
                $fields = array_merge($fields, array_keys($tabConfig['fields']));
            }
        }
        
        return array_unique($fields);
    }

    private function validateFieldCoverage()
    {
        echo "🎯 Validating Field Priority Coverage...\n";
        
        $configuredFields = $this->getAllConfiguredFields();
        $priorities = $this->tabLayout['field_priorities'] ?? [];
        
        foreach ($priorities as $priorityLevel => $priorityFields) {
            $missingPriorityFields = array_diff($priorityFields, $configuredFields);
            
            if (!empty($missingPriorityFields)) {
                $severity = ($priorityLevel === 'critical') ? 'errors' : 'warnings';
                foreach ($missingPriorityFields as $field) {
                    $this->{$severity}[] = "Priority '{$priorityLevel}' field '{$field}' is not configured in any tab";
                }
            }
            
            $coveredCount = count($priorityFields) - count($missingPriorityFields);
            $totalCount = count($priorityFields);
            echo "   📊 {$priorityLevel}: {$coveredCount}/{$totalCount} fields covered\n";
            
            if (!empty($missingPriorityFields)) {
                echo "      Missing: " . implode(', ', $missingPriorityFields) . "\n";
            }
        }
        
        echo "\n";
    }

    private function validateExcelMapping()
    {
        echo "📈 Validating Excel Import Mapping...\n";
        
        $testSheets = $this->tabLayout['test_sheet_mappings'] ?? [];
        $configuredFields = $this->getAllConfiguredFields();
        
        foreach ($testSheets as $sheetName => $mapping) {
            echo "   📋 {$sheetName}:\n";
            
            $mappedFields = array_values($mapping);
            $unmappedFields = array_diff($mappedFields, $configuredFields);
            
            if (!empty($unmappedFields)) {
                foreach ($unmappedFields as $field) {
                    $this->errors[] = "Excel mapping for '{$sheetName}' references unconfigured field '{$field}'";
                    echo "      ❌ Unmapped field: {$field}\n";
                }
            } else {
                echo "      ✅ All fields properly mapped\n";
            }
            
            echo "      📊 {$sheetName}: " . count($mapping) . " columns mapped\n";
        }
        
        echo "\n";
    }

    private function generateReport()
    {
        echo "📋 VALIDATION REPORT\n";
        echo "===================\n\n";

        // Summary statistics
        $tabs = $this->tabLayout['hb837_tabs'] ?? [];
        $totalTabs = count($tabs);
        $totalConfiguredFields = count($this->getAllConfiguredFields());
        $totalDatabaseColumns = count($this->validations['database_columns'] ?? []);

        echo "📊 Summary Statistics:\n";
        echo "   • Total Tabs: {$totalTabs}\n";
        echo "   • Configured Fields: {$totalConfiguredFields}\n";
        echo "   • Database Columns: {$totalDatabaseColumns}\n";
        echo "   • Errors: " . count($this->errors) . "\n";
        echo "   • Warnings: " . count($this->warnings) . "\n\n";

        // Errors
        if (!empty($this->errors)) {
            echo "❌ ERRORS (" . count($this->errors) . "):\n";
            foreach ($this->errors as $error) {
                echo "   • {$error}\n";
            }
            echo "\n";
        }

        // Warnings
        if (!empty($this->warnings)) {
            echo "⚠️ WARNINGS (" . count($this->warnings) . "):\n";
            foreach ($this->warnings as $warning) {
                echo "   • {$warning}\n";
            }
            echo "\n";
        }

        // Overall result
        if (empty($this->errors)) {
            echo "🎉 VALIDATION PASSED!\n";
            echo "All critical validations passed. The tab layout configuration is valid.\n";
            
            if (!empty($this->warnings)) {
                echo "⚠️ However, there are " . count($this->warnings) . " warnings to review.\n";
            }
        } else {
            echo "❌ VALIDATION FAILED!\n";
            echo "There are " . count($this->errors) . " errors that need to be fixed.\n";
        }

        echo "\n";
        $this->generateFieldBreakdown();
    }

    private function generateFieldBreakdown()
    {
        echo "📋 FIELD BREAKDOWN BY TAB:\n";
        echo "==========================\n\n";

        $tabs = $this->tabLayout['hb837_tabs'] ?? [];
        
        foreach ($tabs as $tabKey => $tabConfig) {
            $label = $tabConfig['label'] ?? ucfirst($tabKey);
            $priority = $tabConfig['priority'] ?? 'N/A';
            $fieldCount = isset($tabConfig['fields']) ? count($tabConfig['fields']) : 0;
            
            echo "🗂️ {$label} (priority: {$priority}):\n";
            
            if (isset($tabConfig['fields'])) {
                foreach ($tabConfig['fields'] as $fieldKey => $fieldConfig) {
                    $type = $fieldConfig['type'] ?? 'unknown';
                    $required = isset($fieldConfig['required']) && $fieldConfig['required'] ? '(required)' : '';
                    $readonly = isset($fieldConfig['readonly']) && $fieldConfig['readonly'] ? '(readonly)' : '';
                    
                    echo "   • {$fieldKey} [{$type}] {$required} {$readonly}\n";
                }
            }
            
            echo "   📊 Total: {$fieldCount} fields\n\n";
        }
    }
}

// Run the validation
try {
    $validator = new HB837TabLayoutValidator();
    $validator->validateComplete();
} catch (Exception $e) {
    echo "❌ Validation failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

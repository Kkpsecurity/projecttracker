<?php

namespace App\Imports;

use App\Models\HB837;
use App\Models\Consultant;
use App\Models\HB837ImportFieldConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Enhanced HB837 Import with Smart Update Rules
 * =============================================
 * Rules:
 * 1. If property/address doesn't exist → CREATE it
 * 2. If property/address exists → CHECK what fields changed and UPDATE
 * 3. If field in DB is empty → UPDATE with new value
 * 4. If field in DB has value and import has different value → UPDATE (with logging)
 * 5. Map ALL fields from Excel files completely
 */
class EnhancedHB837Import
{
    public $importedCount = 0;
    public $updatedCount = 0;
    public $skippedCount = 0;
    public $fieldChanges = [];
    public $errors = [];

    /**
     * Field mapping loaded from database configuration
     */
    protected $completeFieldMapping;

    /**
     * Constructor - Load field mapping from database
     */
    public function __construct()
    {
        $this->loadFieldMappingFromDatabase();
    }

    /**
     * Load field mapping from database configuration
     */
    private function loadFieldMappingFromDatabase()
    {
        $fieldConfigs = HB837ImportFieldConfig::active()->get();

        $this->completeFieldMapping = [];

        foreach ($fieldConfigs as $config) {
            if (!empty($config->excel_column_mappings)) {
                $this->completeFieldMapping[$config->database_field] = $config->excel_column_mappings;
            }
        }

        if (empty($this->completeFieldMapping)) {
            // Fallback to config file if database is empty
            $this->completeFieldMapping = config('hb837_field_mapping.field_mapping', []);

            if (empty($this->completeFieldMapping)) {
                throw new \Exception('HB837 field mapping not found in database or config file. Please configure import fields.');
            }

            Log::warning('Using fallback config file for field mapping. Consider seeding the database.');
        }

        Log::info('Field mapping loaded', [
            'source' => 'database',
            'fields_count' => count($this->completeFieldMapping),
            'mapped_fields' => array_keys($this->completeFieldMapping)
        ]);
    }

    /**
     * Process import with enhanced rules
     */
    public function processImport($filePath, $headers, $rows)
    {
        Log::info('Enhanced Import Started', [
            'file' => $filePath,
            'total_rows' => count($rows),
            'headers' => $headers
        ]);

        // Create header mapping
        $headerMap = $this->createHeaderMapping($headers);

        Log::info('Header mapping created', [
            'mapped_fields' => array_keys($headerMap),
            'header_map' => $headerMap
        ]);

        foreach ($rows as $rowIndex => $row) {
            if (empty(array_filter($row))) {
                $this->skippedCount++;
                continue;
            }

            try {
                $this->processRow($row, $headerMap, $rowIndex);
            } catch (\Exception $e) {
                $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                Log::error('Row processing failed', [
                    'row_index' => $rowIndex,
                    'error' => $e->getMessage(),
                    'row_data' => $row
                ]);
            }
        }

        $this->logFinalResults();

        return [
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
            'field_changes' => $this->fieldChanges
        ];
    }

    /**
     * Create mapping between Excel headers and database fields
     */
    private function createHeaderMapping($headers)
    {
        $headerMap = [];

        foreach ($headers as $index => $header) {
            $header = trim($header);
            if (empty($header)) continue;

            // Find matching field
            foreach ($this->completeFieldMapping as $dbField => $possibleHeaders) {
                foreach ($possibleHeaders as $possibleHeader) {
                    if (strcasecmp($header, $possibleHeader) === 0) {
                        $headerMap[$dbField] = $index;
                        break 2; // Break out of both loops
                    }
                }
            }
        }

        return $headerMap;
    }

    /**
     * Process individual row with enhanced update rules
     */
    private function processRow($row, $headerMap, $rowIndex)
    {
        // Extract data based on header mapping
        $recordData = [];

        foreach ($headerMap as $dbField => $columnIndex) {
            if (isset($row[$columnIndex])) {
                $value = trim($row[$columnIndex]);
                if ($value !== '') {
                    $recordData[$dbField] = $this->sanitizeValue($dbField, $value);
                }
            }
        }

        // Set required defaults from configuration
        $defaults = config('hb837_field_mapping.import_rules.default_values', []);
        $recordData['user_id'] = Auth::id() ?? $defaults['user_id'] ?? 1;

        // Default status values if not provided
        if (empty($recordData['report_status'])) {
            $recordData['report_status'] = $defaults['report_status'] ?? 'not-started';
        }
        if (empty($recordData['contracting_status'])) {
            $recordData['contracting_status'] = $defaults['contracting_status'] ?? 'quoted';
        }

        Log::info('Processing row', [
            'row_index' => $rowIndex,
            'extracted_data' => $recordData
        ]);

        // Check required fields
        if (empty($recordData['property_name']) && empty($recordData['address'])) {
            Log::warning('Skipping row - no property name or address', [
                'row_index' => $rowIndex,
                'row_data' => $row
            ]);
            $this->skippedCount++;
            return;
        }

        // Enhanced existing record check
        $existing = $this->findExistingRecord($recordData);

        if ($existing) {
            $this->updateExistingRecord($existing, $recordData, $rowIndex);
        } else {
            $this->createNewRecord($recordData, $rowIndex);
        }
    }

    /**
     * Enhanced logic to find existing records
     * Checks both property_name and address for better matching
     */
    private function findExistingRecord($recordData)
    {
        $query = HB837::query();

        // Primary check: property name (exact match first, then fuzzy)
        if (!empty($recordData['property_name'])) {
            $existing = $query->where('property_name', $recordData['property_name'])->first();
            if ($existing) return $existing;

            // Fuzzy match on property name
            $existing = $query->where('property_name', 'ILIKE', '%' . $recordData['property_name'] . '%')->first();
            if ($existing) return $existing;
        }

        // Secondary check: address
        if (!empty($recordData['address'])) {
            $existing = HB837::where('address', $recordData['address'])->first();
            if ($existing) return $existing;

            // Fuzzy match on address
            $existing = HB837::where('address', 'ILIKE', '%' . $recordData['address'] . '%')->first();
            if ($existing) return $existing;
        }

        return null;
    }

    /**
     * Enhanced update logic following the specified rules
     */
    private function updateExistingRecord($existing, $recordData, $rowIndex)
    {
        $updates = [];
        $changes = [];

        foreach ($recordData as $field => $newValue) {
            if ($field === 'user_id') continue; // Don't update user_id

            $currentValue = $existing->{$field};

            // RULE 1: If field in DB is empty → UPDATE with new value
            if (empty($currentValue) && !empty($newValue)) {
                $updates[$field] = $newValue;
                $changes[$field] = [
                    'type' => 'empty_to_value',
                    'old' => $currentValue,
                    'new' => $newValue
                ];
            }
            // RULE 2: If field has value and import has different value → UPDATE
            else if (!empty($currentValue) && !empty($newValue) && $currentValue != $newValue) {
                $updates[$field] = $newValue;
                $changes[$field] = [
                    'type' => 'value_changed',
                    'old' => $currentValue,
                    'new' => $newValue
                ];
            }
            // RULE 3: If DB is empty and import is empty → SKIP (no change needed)
            // RULE 4: If both have same value → SKIP (no change needed)
        }

        if (!empty($updates)) {
            DB::transaction(function() use ($existing, $updates) {
                $existing->update($updates);
            });

            $this->updatedCount++;
            $this->fieldChanges[$existing->id] = $changes;

            Log::info('Updated existing record', [
                'id' => $existing->id,
                'property_name' => $existing->property_name,
                'updates' => array_keys($updates),
                'changes' => $changes
            ]);
        } else {
            $this->skippedCount++;
            Log::info('No updates needed for existing record', [
                'id' => $existing->id,
                'property_name' => $existing->property_name
            ]);
        }
    }

    /**
     * Create new record
     */
    private function createNewRecord($recordData, $rowIndex)
    {
        DB::transaction(function() use ($recordData, $rowIndex) {
            $created = HB837::create($recordData);

            $this->importedCount++;

            Log::info('Created new record', [
                'id' => $created->id,
                'property_name' => $recordData['property_name'] ?? 'N/A',
                'address' => $recordData['address'] ?? 'N/A',
                'fields_set' => array_keys($recordData)
            ]);
        });
    }

    /**
     * Enhanced value sanitization
     */
    private function sanitizeValue($field, $value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        switch ($field) {
            case 'scheduled_date_of_inspection':
            case 'report_submitted':
            case 'billing_req_sent':
            case 'agreement_submitted':
                return $this->parseDate($value);

            case 'quoted_price':
            case 'sub_fees_estimated_expenses':
            case 'project_net_profit':
                return $this->parseMoneyValue($value);

            case 'units':
                return $this->parseIntegerValue($value);

            case 'report_status':
                return $this->normalizeReportStatus($value);

            case 'contracting_status':
                return $this->normalizeContractingStatus($value);

            case 'property_name':
            case 'address':
            case 'city':
            case 'county':
            case 'state':
                return trim($value);

            case 'zip':
                return preg_replace('/[^\d-]/', '', $value);

            case 'phone':
                return preg_replace('/[^\d\-\(\)\+\s]/', '', $value);

            default:
                return trim($value);
        }
    }

    private function parseDate($value)
    {
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseMoneyValue($value)
    {
        $cleaned = preg_replace('/[^\d.-]/', '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    private function parseIntegerValue($value)
    {
        $cleaned = preg_replace('/[^\d]/', '', $value);
        return is_numeric($cleaned) ? (int) $cleaned : null;
    }

    private function normalizeReportStatus($value)
    {
        $statusMap = config('hb837_field_mapping.status_maps.report_status', []);
        $lower = strtolower(trim($value));
        return $statusMap[$lower] ?? $lower;
    }

    private function normalizeContractingStatus($value)
    {
        $statusMap = config('hb837_field_mapping.status_maps.contracting_status', []);
        $lower = strtolower(trim($value));
        return $statusMap[$lower] ?? $lower;
    }

    private function logFinalResults()
    {
        Log::info('Enhanced Import Completed', [
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'errors' => count($this->errors),
            'total_field_changes' => count($this->fieldChanges)
        ]);
    }
}

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
     * Track which fields were actually present in the import file
     */
    protected $fieldsInImport = [];

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
                        $this->fieldsInImport[] = $dbField; // Track that this field was in import
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

        // NOTE: Status defaults will be handled in create/update logic based on record type
        // For EXISTING records: preserve existing status if import data is empty
        // For NEW records: apply defaults only if no status provided
        // This prevents status downgrades during import


        Log::info('Processing row', [
            'row_index' => $rowIndex,
            'extracted_data' => $recordData
        ]);

        // Check required fields
        if (empty($recordData['property_name']) && empty($recordData['address'])) {
            Log::warning('Skipping row: Missing property_name and address', [
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
     * Priority: Address first, then property name
     * Logic: If same address + same property_name → UPDATE
     *        If same address + different property_name → CREATE (different property at same address)
     */
    private function findExistingRecord($recordData)
    {
        // Primary check: Address first (exact match)
        if (!empty($recordData['address'])) {
            // Find all records with the same address
            $recordsWithSameAddress = HB837::where('address', $recordData['address'])->get();

            if ($recordsWithSameAddress->isNotEmpty()) {
                Log::info('Found records with matching address', [
                    'address' => $recordData['address'],
                    'matching_records_count' => $recordsWithSameAddress->count(),
                    'incoming_property_name' => $recordData['property_name'] ?? 'N/A'
                ]);

                // Check if any record has the same property_name
                if (!empty($recordData['property_name'])) {
                    foreach ($recordsWithSameAddress as $record) {
                        if ($record->property_name === $recordData['property_name']) {
                            Log::info('✅ UPDATE: Same address + same property_name found', [
                                'existing_id' => $record->id,
                                'address' => $recordData['address'],
                                'property_name' => $recordData['property_name'],
                                'action' => 'UPDATE_EXISTING'
                            ]);
                            return $record; // UPDATE existing record
                        }
                    }

                    // Same address but different property_name → CREATE new record
                    Log::info('🆕 CREATE: Same address but different property_name', [
                        'address' => $recordData['address'],
                        'incoming_property_name' => $recordData['property_name'],
                        'existing_properties_at_address' => $recordsWithSameAddress->pluck('property_name')->toArray(),
                        'action' => 'CREATE_NEW'
                    ]);
                    return null; // CREATE new record
                } else {
                    // No property_name provided, check if only one record exists at this address
                    if ($recordsWithSameAddress->count() === 1) {
                        Log::info('✅ UPDATE: Single record at address, no property_name conflict', [
                            'existing_id' => $recordsWithSameAddress->first()->id,
                            'address' => $recordData['address'],
                            'action' => 'UPDATE_EXISTING'
                        ]);
                        return $recordsWithSameAddress->first(); // UPDATE the single record
                    } else {
                        // Multiple records at same address, can't determine which to update
                        Log::warning('🤔 AMBIGUOUS: Multiple records at address, no property_name provided', [
                            'address' => $recordData['address'],
                            'existing_records_count' => $recordsWithSameAddress->count(),
                            'action' => 'CREATE_NEW'
                        ]);
                        return null; // CREATE new record to avoid ambiguity
                    }
                }
            }

            // Try fuzzy address match as fallback
            $fuzzyAddressMatch = HB837::where('address', 'ILIKE', '%' . $recordData['address'] . '%')->first();
            if ($fuzzyAddressMatch) {
                Log::info('📍 FUZZY ADDRESS MATCH found', [
                    'exact_address' => $recordData['address'],
                    'fuzzy_match_address' => $fuzzyAddressMatch->address,
                    'fuzzy_match_id' => $fuzzyAddressMatch->id,
                    'action' => 'UPDATE_EXISTING'
                ]);
                return $fuzzyAddressMatch; // UPDATE fuzzy match
            }
        }

        // Fallback: Property name only check (if no address provided)
        if (!empty($recordData['property_name'])) {
            // Exact property name match
            $existing = HB837::where('property_name', $recordData['property_name'])->first();
            if ($existing) {
                Log::info('✅ UPDATE: Property name match (no address provided)', [
                    'existing_id' => $existing->id,
                    'property_name' => $recordData['property_name'],
                    'action' => 'UPDATE_EXISTING'
                ]);
                return $existing;
            }

            // Fuzzy property name match
            $existing = HB837::where('property_name', 'ILIKE', '%' . $recordData['property_name'] . '%')->first();
            if ($existing) {
                Log::info('🔍 FUZZY PROPERTY NAME MATCH found', [
                    'exact_property_name' => $recordData['property_name'],
                    'fuzzy_match_property_name' => $existing->property_name,
                    'fuzzy_match_id' => $existing->id,
                    'action' => 'UPDATE_EXISTING'
                ]);
                return $existing;
            }
        }

        // No matches found → CREATE new record
        Log::info('🆕 CREATE: No existing record found', [
            'property_name' => $recordData['property_name'] ?? 'N/A',
            'address' => $recordData['address'] ?? 'N/A',
            'action' => 'CREATE_NEW'
        ]);
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

            // CRITICAL: Skip status fields that weren't in the import file
            // This prevents applying default status values to existing records
            if (in_array($field, ['report_status', 'contracting_status'])) {
                // If this field was not explicitly mapped from import headers, skip it entirely
                if (!$this->wasFieldInImport($field)) {
                    Log::info('Skipping status field - not in import file', [
                        'property_id' => $existing->id,
                        'field' => $field,
                        'current_value' => $currentValue,
                        'action' => 'PRESERVE_EXISTING_STATUS'
                    ]);
                    continue;
                }
            }

            // Special handling for contracting_status field with hierarchy protection
            if ($field === 'contracting_status') {
                // Define status hierarchy (higher index = better status)
                $statusHierarchy = ['quoted' => 1, 'started' => 2, 'executed' => 3, 'closed' => 4];

                $currentRank = $statusHierarchy[$currentValue] ?? 0;
                $newRank = $statusHierarchy[$newValue] ?? 0;

                // RULE 1: If database field is empty → Update with import value
                if (empty($currentValue) && !empty($newValue)) {
                    $updates[$field] = $newValue;
                    $changes[$field] = [
                        'type' => 'contracting_status_empty_to_value',
                        'old' => $currentValue,
                        'new' => $newValue
                    ];
                }
                // RULE 2: Only update if import has BETTER status (prevent downgrades)
                else if (!empty($currentValue) && !empty($newValue) && $newRank > $currentRank) {
                    $updates[$field] = $newValue;
                    $changes[$field] = [
                        'type' => 'contracting_status_upgraded',
                        'old' => $currentValue,
                        'new' => $newValue
                    ];
                }
                // RULE 3: Prevent status downgrades
                else if (!empty($currentValue) && !empty($newValue) && $newRank <= $currentRank) {
                    Log::info('Preventing contracting_status downgrade', [
                        'property_id' => $existing->id,
                        'current_status' => $currentValue,
                        'import_status' => $newValue,
                        'current_rank' => $currentRank,
                        'import_rank' => $newRank,
                        'action' => 'PRESERVE_HIGHER_STATUS'
                    ]);
                }
                // RULE 4: If import has no value → Don't update database (skip)
                else if (empty($newValue)) {
                    Log::info('Skipping contracting_status update - no import value', [
                        'property_id' => $existing->id,
                        'current_db_value' => $currentValue,
                        'import_value' => $newValue
                    ]);
                }
                continue;
            }

            // Special handling for report_status field with hierarchy protection
            if ($field === 'report_status') {
                // Define status hierarchy (higher index = better status)
                $statusHierarchy = ['not-started' => 1, 'underway' => 2, 'in-review' => 3, 'completed' => 4];

                $currentRank = $statusHierarchy[$currentValue] ?? 0;
                $newRank = $statusHierarchy[$newValue] ?? 0;

                // RULE 1: If database field is empty → Update with import value
                if (empty($currentValue) && !empty($newValue)) {
                    $updates[$field] = $newValue;
                    $changes[$field] = [
                        'type' => 'report_status_empty_to_value',
                        'old' => $currentValue,
                        'new' => $newValue
                    ];
                }
                // RULE 2: Only update if import has BETTER status (prevent downgrades)
                else if (!empty($currentValue) && !empty($newValue) && $newRank > $currentRank) {
                    $updates[$field] = $newValue;
                    $changes[$field] = [
                        'type' => 'report_status_upgraded',
                        'old' => $currentValue,
                        'new' => $newValue
                    ];
                }
                // RULE 3: Prevent status downgrades
                else if (!empty($currentValue) && !empty($newValue) && $newRank <= $currentRank) {
                    Log::info('Preventing report_status downgrade', [
                        'property_id' => $existing->id,
                        'current_status' => $currentValue,
                        'import_status' => $newValue,
                        'current_rank' => $currentRank,
                        'import_rank' => $newRank,
                        'action' => 'PRESERVE_HIGHER_STATUS'
                    ]);
                }
                // RULE 4: If import has no value → Don't update database (skip)
                else if (empty($newValue)) {
                    Log::info('Skipping report_status update - no import value', [
                        'property_id' => $existing->id,
                        'current_db_value' => $currentValue,
                        'import_value' => $newValue
                    ]);
                }
                continue;
            }

            // Standard rules for other fields
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
     * Check if a field was actually present in the import file
     */
    private function wasFieldInImport($field)
    {
        return in_array($field, $this->fieldsInImport);
    }

    /**
     * Create new record
     */
    private function createNewRecord($recordData, $rowIndex)
    {
        $defaults = config('hb837_field_mapping.import_rules.default_values', []);

        // For new records, apply default status values only if not provided in import
        if (!isset($recordData['contracting_status']) || empty($recordData['contracting_status'])) {
            $recordData['contracting_status'] = $defaults['contracting_status'] ?? 'quoted';
        }

        if (!isset($recordData['report_status']) || empty($recordData['report_status'])) {
            $recordData['report_status'] = $defaults['report_status'] ?? 'not-started';
        }

        DB::transaction(function() use ($recordData, $rowIndex) {
            $created = HB837::create($recordData);

            $this->importedCount++;

            Log::info('Created new record', [
                'id' => $created->id,
                'property_name' => $recordData['property_name'] ?? 'N/A',
                'address' => $recordData['address'] ?? 'N/A',
                'contracting_status' => $recordData['contracting_status'],
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

            case 'property_type':
                return $this->normalizePropertyType($value);

            case 'assigned_consultant_id':
                return $this->validateConsultantId($value);

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
        $mapped = $statusMap[$lower] ?? $lower;

        // Debug logging
        Log::info('Report status normalization', [
            'original' => $value,
            'lowercase' => $lower,
            'mapped_to' => $mapped,
            'mapping_exists' => isset($statusMap[$lower])
        ]);

        return $mapped;
    }

    private function normalizeContractingStatus($value)
    {
        $statusMap = config('hb837_field_mapping.status_maps.contracting_status', []);
        $lower = strtolower(trim($value));
        return $statusMap[$lower] ?? $lower;
    }

    private function normalizePropertyType($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // Normalize property types to lowercase to match database enum constraint
        $propertyTypeMap = [
            'garden' => 'garden',
            'midrise' => 'midrise',
            'mid-rise' => 'midrise',
            'mid rise' => 'midrise',
            'highrise' => 'highrise',
            'high-rise' => 'highrise',
            'high rise' => 'highrise',
            'industrial' => 'industrial',
            'bungalo' => 'bungalo',
            'bungalow' => 'bungalo' // Handle common misspelling
        ];
        $lower = strtolower(trim($value));
        return $propertyTypeMap[$lower] ?? $lower;
    }

    private function validateConsultantId($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // If it's a number, check if the consultant exists
        if (is_numeric($value)) {
            $consultantId = (int) $value;
            if (Consultant::where('id', $consultantId)->exists()) {
                return $consultantId;
            }
        }

        // If it's a name, try to find consultant by first_name, last_name, or dba_company_name
        if (is_string($value)) {
            $searchValue = trim($value);
            $consultant = Consultant::where('first_name', 'ilike', '%' . $searchValue . '%')
                ->orWhere('last_name', 'ilike', '%' . $searchValue . '%')
                ->orWhere('dba_company_name', 'ilike', '%' . $searchValue . '%')
                ->first();

            if ($consultant) {
                return $consultant->id;
            }

            // If consultant not found, create a new one
            Log::info('Creating new consultant during import', [
                'value' => $value,
                'action' => 'Creating new consultant from name'
            ]);

            $nameParts = explode(' ', $searchValue);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? end($nameParts) : $firstName;

            // Build a predictable email
            $email = \Illuminate\Support\Str::slug("{$firstName}.{$lastName}") . '@example.com';

            // Create new consultant
            $consultant = Consultant::updateOrCreate(
                ['first_name' => $firstName, 'last_name' => $lastName],
                ['email' => $email]
            );

            Log::info('✅ Consultant created successfully during Enhanced Import', [
                'consultant_id' => $consultant->id,
                'consultant_name' => "{$consultant->first_name} {$consultant->last_name}",
                'email' => $consultant->email,
                'was_created' => $consultant->wasRecentlyCreated
            ]);

            return $consultant->id;
        }

        // If consultant not found and not a string, return null
        Log::warning('Consultant not found during import', [
            'value' => $value,
            'action' => 'Setting assigned_consultant_id to null - invalid value type'
        ]);

        return null;
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

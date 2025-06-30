<?php

namespace App\Imports;

use App\Models\Consultant;
use App\Models\HB837;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HB837Import implements ToModel, WithHeadingRow
{
    public $importedCount = 0;

    public $updatedCount = 0;

    public $skippedCount = 0;

    public $skippedProperties = [];

    /**
     * Import phase: 'initial', 'update', 'review'
     */
    private $phase = 'initial';

    /**
     * Flag to indicate if we're in truncate mode (all records should be treated as new)
     */
    private $truncateMode = false;

    /**
     * Set import phase
     */
    public function setPhase($phase = 'initial')
    {
        $this->phase = $phase;
        return $this;
    }

    /**
     * Get current import phase
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * Set truncate mode - when true, all records are treated as new (no existing record check)
     */
    public function setTruncateMode($enabled = true)
    {
        $this->truncateMode = $enabled;

        return $this;
    }

    /**
     * Updated mapping for new Excel format
     */
    protected $fields = [
        // Status fields
        'report_status' => 'Report Status',
        'contracting_status' => 'Contracting Status',

        // Property details
        'property_name' => 'Property Name',
        'property_type' => 'Property Type',
        'units' => 'Units',

        // Location fields
        'address' => 'Address',
        'city' => 'City',
        'county' => 'County',
        'state' => 'State',
        'zip' => 'Zip',

        // Contact fields
        'phone' => 'Phone',
        'management_company' => 'Management Company',
        'property_manager_name' => 'Property Manager Name',
        'property_manager_email' => 'Property Manager Email',
        'regional_manager_name' => 'Regional Manager Name',
        'regional_manager_email' => 'Regional Manager Email',

        // Ownership & consulting
        'owner_name' => 'Owner Name',
        'assigned_consultant_id' => 'Consultant Name',   // Use name for lookup

        // Date fields
        'scheduled_date_of_inspection' => 'Scheduled Date of Inspection',
        'report_submitted' => 'Report Submitted',
        'agreement_submitted' => 'Agreement Submitted',
        'billing_req_sent' => 'Billing Req Sent',

        // Risk field
        'securitygauge_crime_risk' => 'SecurityGauge Crime Risk',

        // Financial fields
        'quoted_price' => 'Quoted Price',
        'sub_fees_estimated_expenses' => 'Sub Fees Estimated Expenses',
        'project_net_profit' => 'Project Net Profit',

        // Macro fields
        'macro_client' => 'Macro Client',
        'macro_contact' => 'Macro Contact',
        'macro_email' => 'Macro Email',

        // Notes
        'financial_notes' => 'Financial Notes',
        'consultant_notes' => 'Consultant Notes',
        'notes' => 'Notes',
    ];

    /**
     * Model method to map the headers from the Excel file to the database fields.
     * Handles different phases: initial, update, review
     */
    public function model(array $row)
    {
        $mapped = $this->mapHeaders($row);

        // Only skip rows without an address
        if (empty(trim($mapped['address'] ?? ''))) {
            Log::warning('Skipping row: missing address.', ['row' => $row, 'phase' => $this->phase]);
            $this->skippedCount++;
            $this->skippedProperties[] = 'Missing address in row: '.json_encode($row);

            return null;
        }

        // Defaults & validation
        $mapped['user_id'] = Auth::id() ?? 1;
        $mapped['property_type'] = $this->validatePropertyType($mapped['property_type'] ?? '');
        $mapped['contracting_status'] = $this->validateContractingStatus($mapped['contracting_status'] ?? '');
        $mapped['report_status'] = $this->validateReportStatus($mapped['report_status'] ?? '');

        // Handle different import phases
        switch ($this->phase) {
            case 'initial':
                return $this->handleInitialPhase($mapped);

            case 'update':
                return $this->handleUpdatePhase($mapped);

            case 'review':
                return $this->handleReviewPhase($mapped);

            default:
                return $this->handleInitialPhase($mapped);
        }
    }

    /**
     * Handle Phase 1: Initial Import & Quotation
     * Creates new records or updates existing ones
     */
    private function handleInitialPhase($mapped)
    {
        // In initial phase, skip existing record check in truncate mode
        if (! $this->truncateMode) {
            $existing = HB837::firstWhere('address', $mapped['address']);

            if ($existing) {
                $updates = [];
                foreach ($mapped as $key => $value) {
                    // Only patch non-null, non-empty, non-zero values that differ
                    if (! is_null($value) && $value !== '' && $value !== 0.0 && $existing->{$key} != $value) {
                        $updates[$key] = $value;
                    }
                }
                if (count($updates) === 0) {
                    $this->skippedCount++;
                    return null;
                }
                $existing->update($updates);
                Log::info('HB837 Record Updated (Initial Phase)', ['id' => $existing->id]);
                $this->updatedCount++;

                return $existing->fresh();
            }
        }

        // New record (always create when in truncate mode or when no existing record found)
        $new = HB837::create($mapped);
        Log::info('HB837 Record Created (Initial Phase)', ['id' => $new->id, 'address' => $mapped['address']]);
        $this->importedCount++;

        return $new;
    }

    /**
     * Handle Phase 2: Executed & Contacts
     * Updates existing records with execution details and contact information
     */
    private function handleUpdatePhase($mapped)
    {
        $existing = HB837::firstWhere('address', $mapped['address']);

        if (!$existing) {
            Log::warning('No existing record found for update phase', ['address' => $mapped['address']]);
            $this->skippedCount++;
            $this->skippedProperties[] = 'No existing record for address: ' . $mapped['address'];
            return null;
        }

        // Focus on execution and contact fields for update phase
        $updateFields = [
            'contracting_status',
            'report_status',
            'agreement_submitted',
            'report_submitted',
            'billing_req_sent',
            'property_manager_name',
            'property_manager_email',
            'regional_manager_name',
            'regional_manager_email',
            'management_company',
            'phone',
            'consultant_notes',
            'notes'
        ];

        $updates = [];
        foreach ($updateFields as $field) {
            if (
                isset($mapped[$field]) &&
                $mapped[$field] !== null &&
                $mapped[$field] !== '' &&
                $mapped[$field] !== 0.0 &&
                $existing->{$field} != $mapped[$field]
            ) {
                $updates[$field] = $mapped[$field];
            }
        }

        if (count($updates) === 0) {
            $this->skippedCount++;
            return null;
        }

        $existing->update($updates);
        Log::info('HB837 Record Updated (Update Phase)', ['id' => $existing->id, 'updates' => array_keys($updates)]);
        $this->updatedCount++;

        return $existing->fresh();
    }

    /**
     * Handle Phase 3: Details Updated (Review & Compare)
     * Performs detailed comparison and selective updates
     */
    private function handleReviewPhase($mapped)
    {
        $existing = HB837::firstWhere('address', $mapped['address']);

        if (!$existing) {
            Log::warning('No existing record found for review phase', ['address' => $mapped['address']]);
            $this->skippedCount++;
            $this->skippedProperties[] = 'No existing record for review: ' . $mapped['address'];
            return null;
        }

        // In review phase, we're more selective about what gets updated
        $reviewFields = [
            'quoted_price',
            'sub_fees_estimated_expenses',
            'project_net_profit',
            'securitygauge_crime_risk',
            'units',
            'property_type',
            'financial_notes',
            'macro_client',
            'macro_contact',
            'macro_email'
        ];

        $updates = [];
        $changes = [];

        foreach ($reviewFields as $field) {
            if (
                isset($mapped[$field]) &&
                $mapped[$field] !== null &&
                $mapped[$field] !== '' &&
                $mapped[$field] !== 0.0 &&
                $existing->{$field} != $mapped[$field]
            ) {

                $changes[$field] = [
                    'old' => $existing->{$field},
                    'new' => $mapped[$field]
                ];

                // Only update if the new value is considered "better" or more complete
                if ($this->shouldUpdateInReviewPhase($field, $existing->{$field}, $mapped[$field])) {
                    $updates[$field] = $mapped[$field];
                }
            }
        }

        // Log all changes detected for review
        if (!empty($changes)) {
            Log::info('HB837 Review Phase Changes Detected', [
                'id' => $existing->id,
                'address' => $mapped['address'],
                'changes' => $changes,
                'applied_updates' => array_keys($updates)
            ]);
        }

        if (count($updates) === 0) {
            $this->skippedCount++;
            return null;
        }

        $existing->update($updates);
        Log::info('HB837 Record Updated (Review Phase)', ['id' => $existing->id, 'updates' => array_keys($updates)]);
        $this->updatedCount++;

        return $existing->fresh();
    }

    /**
     * Determine if a field should be updated during review phase
     */
    private function shouldUpdateInReviewPhase($field, $oldValue, $newValue)
    {
        // Financial fields: only update if new value is greater (assuming more accurate pricing)
        if (in_array($field, ['quoted_price', 'sub_fees_estimated_expenses', 'project_net_profit'])) {
            return is_null($oldValue) || $oldValue == 0 || $newValue > $oldValue;
        }

        // Units: only update if new value is greater
        if ($field === 'units') {
            return is_null($oldValue) || $oldValue == 0 || $newValue > $oldValue;
        }

        // Text fields: only update if old value is empty
        if (in_array($field, ['financial_notes', 'macro_client', 'macro_contact', 'macro_email'])) {
            return is_null($oldValue) || trim($oldValue) === '';
        }

        // For other fields, update if old value is null or empty
        return is_null($oldValue) || $oldValue === '';
    }

    /**
     * Check if an existing record is different from the mapped data.
     */
    private function isRecordUpdated($existingRecord, $newData)
    {
        $existingData = $existingRecord->toArray();

        // Compare only fields that exist in the new data
        $differences = array_diff_assoc($newData, array_intersect_key($existingData, $newData));

        return ! empty($differences); // Returns true if there are changes
    }

    /**
     * Compare the uploaded file with existing records in detail.
     * Enhanced for three-phase workflow
     */
    public function compare($file, $phase = 'initial')
    {
        // Load spreadsheet rows
        $rows = \Maatwebsite\Excel\Facades\Excel::toArray($this, $file)[0];

        // Key existing HB837 models by "address|property_name"
        $existingRecords = HB837::all()->keyBy(function ($m) {
            return strtolower($m->address.'|'.$m->property_name);
        });

        $total = count($rows);
        $existing = 0;
        $updated = 0;
        $newCount = 0;
        $updatesLog = [];
        $newLog = [];
        $phaseSpecificChanges = [];

        foreach ($rows as $row) {
            // Map headers → DB fields
            $mapped = $this->mapHeaders($row);

            $addr = trim($mapped['address'] ?? '');
            $prop = trim($mapped['property_name'] ?? '');

            // Skip rows missing address
            if (! $addr) {
                continue;
            }

            $key = strtolower($addr.'|'.$prop);
            if (isset($existingRecords[$key])) {
                $model = $existingRecords[$key];
                $changes = [];

                // Get phase-specific fields to check
                $fieldsToCheck = $this->getPhaseSpecificFields($phase);

                // Detect changes: non-null, non-empty, non-zero, and different
                foreach ($mapped as $field => $value) {
                    // Only check relevant fields for this phase
                    if (!in_array($field, $fieldsToCheck)) {
                        continue;
                    }

                    if (
                        $value !== null
                        && $value !== ''
                        && $value !== 0.0
                        && $model->{$field} != $value
                    ) {
                        $changes[$field] = [
                            'old' => $model->{$field},
                            'new' => $value,
                            'recommended' => $phase === 'review' ?
                                $this->shouldUpdateInReviewPhase($field, $model->{$field}, $value) : true
                        ];
                    }
                }

                if (count($changes)) {
                    $updated++;
                    $updatesLog[] = [
                        'address' => $addr,
                        'property_name' => $prop,
                        'changes' => $changes,
                        'phase' => $phase
                    ];
                } else {
                    $existing++;
                }
            } else {
                $newCount++;
                $newLog[] = [
                    'address' => $addr,
                    'property_name' => $prop,
                    'phase' => $phase
                ];
            }
        }

        return [
            'phase' => $phase,
            'total_uploaded' => $total,
            'existing_count' => $existing,
            'updated_count' => $updated,
            'new_count' => $newCount,
            'updated_properties' => $updatesLog,
            'new_properties' => $newLog,
            'phase_description' => $this->getPhaseDescription($phase)
        ];
    }

    /**
     * Get fields relevant to each phase
     */
    private function getPhaseSpecificFields($phase)
    {
        switch ($phase) {
            case 'initial':
                return [
                    'property_name',
                    'address',
                    'city',
                    'county',
                    'state',
                    'zip',
                    'property_type',
                    'units',
                    'owner_name',
                    'assigned_consultant_id',
                    'scheduled_date_of_inspection',
                    'quoted_price',
                    'sub_fees_estimated_expenses'
                ];

            case 'update':
                return [
                    'contracting_status',
                    'report_status',
                    'agreement_submitted',
                    'report_submitted',
                    'billing_req_sent',
                    'property_manager_name',
                    'property_manager_email',
                    'regional_manager_name',
                    'regional_manager_email',
                    'management_company',
                    'phone',
                    'consultant_notes',
                    'notes'
                ];

            case 'review':
                return [
                    'quoted_price',
                    'sub_fees_estimated_expenses',
                    'project_net_profit',
                    'securitygauge_crime_risk',
                    'units',
                    'property_type',
                    'financial_notes',
                    'macro_client',
                    'macro_contact',
                    'macro_email'
                ];

            default:
                return array_keys($this->fields);
        }
    }

    /**
     * Get description for each phase
     */
    private function getPhaseDescription($phase)
    {
        switch ($phase) {
            case 'initial':
                return 'Initial Import & Quotation - Creates new records with basic property and quote information';

            case 'update':
                return 'Executed & Contacts - Updates execution status and contact information for existing records';

            case 'review':
                return 'Details Updated - Reviews and selectively updates financial and detailed property information';

            default:
                return 'Standard import process';
        }
    }

    private function mapHeaders(array $row): array
    {
        $mapped = [];

        foreach ($this->fields as $field => $expectedKey) {
            // Determine the raw value by trying each expected header, snake-cased
            $value = null;
            $candidates = is_array($expectedKey) ? $expectedKey : [$expectedKey];
            foreach ($candidates as $candidate) {
                $snake = Str::snake($candidate);
                if (array_key_exists($snake, $row) && $row[$snake] !== null && $row[$snake] !== '') {
                    $value = $row[$snake];
                    break;
                }
            }

            // Now transform per field
            switch ($field) {
                // simple trimmed strings
                case 'management_company':
                case 'property_manager_name':
                case 'property_manager_email':
                case 'regional_manager_name':
                case 'regional_manager_email':
                case 'macro_client':
                case 'macro_contact':
                case 'macro_email':
                case 'financial_notes':
                case 'consultant_notes':
                case 'notes':
                case 'address':
                case 'city':
                case 'county':
                case 'phone':
                case 'property_name':
                case 'property_type':
                    $mapped[$field] = is_string($value) ? trim($value) : $value;
                    break;
                case 'securitygauge_crime_risk':
                    $allowedRisks = array_map(function ($v) {
                        return strtolower(trim($v));
                    }, array_values(config('hb837.security_gauge', [])));
                    $val = is_string($value) ? trim(strtolower($value)) : strtolower($value);

                    if ($val === '' || $val === null) {
                        // Default to "Moderate" if no value
                        $mapped[$field] = 'Moderate';
                    } elseif (in_array($val, $allowedRisks, true)) {
                        // Map back to the original case from config
                        $original = array_search($val, $allowedRisks, true);
                        $mapped[$field] = array_values(config('hb837.security_gauge', []))[$original];
                    } else {
                        $mapped[$field] = 'Moderate';
                        Log::warning('Invalid securitygauge_crime_risk value skipped, defaulted to Moderate', [
                            'input' => $value,
                            'normalized' => $val,
                            'allowed' => $allowedRisks,
                        ]);
                    }
                    break;

                    // integer
                case 'units':
                    $mapped[$field] = is_numeric($value) ? (int) $value : 0;
                    break;

                    // currency -> float
                case 'quoted_price':
                case 'sub_fees_estimated_expenses':
                    $mapped[$field] = $value !== null
                        ? floatval(preg_replace('/[^\d\.]/', '', $value))
                        : 0.0;
                    break;

                    // net profit based on quoted & sub fees
                case 'project_net_profit':
                    $mapped[$field] = $this->calculateNetProfit(
                        $mapped['quoted_price'] ?? 0,
                        $mapped['sub_fees_estimated_expenses'] ?? 0
                    );
                    break;

                    // dates
                case 'scheduled_date_of_inspection':
                case 'report_submitted':
                case 'agreement_submitted':
                case 'billing_req_sent':
                    $mapped[$field] = $this->parseDate($value);
                    break;

                    // validations
                case 'contracting_status':
                    $mapped[$field] = $this->validateContractingStatus($value);
                    break;

                case 'report_status':
                    $mapped[$field] = $this->validateReportStatus($value);
                    break;

                case 'state':
                    $mapped[$field] = $this->validateState($value);
                    break;

                case 'zip':
                    $mapped[$field] = $this->validateZip($value);
                    break;

                    // relations
                case 'assigned_consultant_id':
                    $mapped[$field] = $this->processConsultantId($value);
                    break;

                case 'owner_name':
                    $mapped['owner_name'] = is_string($value) ? trim($value) : $value;
                    break;

                default:
                    // catch-all (should not hit)
                    $mapped[$field] = is_string($value) ? trim($value) : $value;
                    break;
            }
        }

        return $mapped;
    }

    /** helper to pull the first non-empty key out of the row */
    private function firstNonEmpty(array $row, array $keys)
    {
        foreach ($keys as $k) {
            if (isset($row[$k]) && trim((string) $row[$k]) !== '') {
                return $row[$k];
            }
        }

        return null;
    }

    /**
     * Get a value from the row, trimming and cleaning it.
     */
    private function getRowValue($row, $key)
    {
        return isset($row[$key]) ? trim($row[$key]) : null;
    }

    /**
     * Trim a string value.
     */
    private function trimString($value)
    {
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Parse an integer value.
     */
    private function parseInteger($value)
    {
        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Check if a value is a date.
     */
    protected function is_date($name)
    {
        return (bool) strtotime($name);
    }

    /**
     * Parse a currency value.
     */
    private function parseCurrency($value)
    {
        return $value ? floatval(preg_replace('/[^\d.]/', '', $value)) : null;
    }

    /**
     * Parse a date value.
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Check if the value is a valid date
        if (strtotime($value) !== false) {
            return Carbon::parse(trim($value))->format('Y-m-d');
        }

        // Handle Excel date format (e.g., 45359)
        if (is_numeric($value)) {
            try {
                return Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($value - 25569) * 86400))->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning('Date Parsing Failed', ['value' => $value]);

                return null;
            }
        }

        Log::warning('Invalid date format', ['value' => $value]);

        return null;
    }

    /**
     * Validate and set property type.
     */
    private function validatePropertyType($value)
    {
        $allowedTypes = config('hb837.property_types');
        $cleanedValue = strtolower(trim($value));

        return in_array($cleanedValue, $allowedTypes) ? $cleanedValue : 'garden';
    }

    /**
     * Validate and set contracting status.
     */
    private function validateContractingStatus($value)
    {
        $allowedStatuses = config('hb837.contracting_statuses', ['executed', 'quoted', 'started', 'closed']);
        $cleanedValue = strtolower(trim($value));

        Log::info('Contracting Status Before Validation', ['value' => $cleanedValue]);

        // Ensure the value is valid
        if (in_array($cleanedValue, $allowedStatuses, true)) {
            return $cleanedValue;
        }

        Log::warning('Invalid Contracting Status Found', ['original' => $value, 'cleaned' => $cleanedValue]);

        return 'quoted'; // Default value if invalid
    }

    /**
     * Validate and set report status.
     */
    private function validateReportStatus($value)
    {
        $allowedStatuses = config('hb837.report_statuses');
        $cleanedValue = strtolower(trim($value));

        if ($cleanedValue === 'underway') {
            $cleanedValue = 'in-progress';
        } elseif ($cleanedValue === 'complete') {
            $cleanedValue = 'completed';
        } elseif ($cleanedValue === 'not started') {
            $cleanedValue = 'not-started';
        } elseif ($cleanedValue === 'in review') {
            $cleanedValue = 'in-review';
        }

        return in_array($cleanedValue, $allowedStatuses) ? $cleanedValue : 'not-started';
    }

    /**
     * Validate state abbreviation.
     */
    private function validateState($value)
    {
        return (strlen($value) === 2) ? strtoupper($value) : null;
    }

    /**
     * Validate ZIP code.
     */
    private function validateZip($value)
    {
        return is_numeric($value) ? $value : null;
    }

    /**
     * Calculate net profit based on quoted price and sub fees.
     */
    private function calculateNetProfit($quotedPrice, $subFees)
    {
        if (! $quotedPrice || ! $subFees) {
            return null;
        }

        return floatval($quotedPrice) - floatval($subFees);
    }

    /**
     * Get or create an Owner ID.
     */
    private function processConsultantId(?string $name = null): ?int
    {
        // Bail on empty or non-alphabetic values (e.g. a date)
        if (empty($name) || ! preg_match('/[A-Za-z]/', $name)) {
            return null;
        }

        // Split into first/last (if no last, mirror first)
        [$first, $last] = array_pad(explode(' ', trim($name), 2), 2, trim($name));

        // Build a predictable email
        $email = Str::slug("{$first}.{$last}").'@example.com';

        // Find or create by first & last name; update email if missing
        $consultant = Consultant::updateOrCreate(
            ['first_name' => $first, 'last_name' => $last],
            ['email' => $email]
        );

        return $consultant->id;
    }

    /**
     * Resolve Consultant ID from name.
     */
    private function resolveConsultant($name)
    {
        if (empty($name)) {
            return null; // No consultant assigned
        }

        $consultant = Consultant::where('first_name', 'LIKE', '%'.trim($name).'%')->first();

        if ($consultant) {
            return $consultant->id;
        }

        // Create a new consultant if not found
        return $this->processConsultantId(trim($name));
    }

    /**
     * Import from Excel file with three-phase support - for testing and manual import runs.
     */
    public function importFromExcel($filePath, $phase = 'initial')
    {
        try {
            $import = new self;
            $import->setPhase($phase);
            $import->setTruncateMode(false); // Set as needed
            
            $result = \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

            Log::info('Three-Phase Import completed', [
                'phase' => $phase,
                'imported' => $import->importedCount,
                'updated' => $import->updatedCount,
                'skipped' => $import->skippedCount,
                'skipped_properties' => $import->skippedProperties,
            ]);

            return [
                'phase' => $phase,
                'phase_description' => $import->getPhaseDescription($phase),
                'imported' => $import->importedCount,
                'updated' => $import->updatedCount,
                'skipped' => $import->skippedCount,
                'skipped_properties' => $import->skippedProperties,
            ];
        } catch (\Exception $e) {
            Log::error('Three-Phase Import failed', ['phase' => $phase, 'error' => $e->getMessage()]);

            return [
                'error' => $e->getMessage(),
                'phase' => $phase
            ];
        }
    }

    /**
     * Execute all three phases in sequence
     */
    public function executeThreePhaseImport($file1Path, $file2Path, $file3Path)
    {
        $results = [];

        // Phase 1: Initial Import & Quotation
        $results['phase1'] = $this->importFromExcel($file1Path, 'initial');

        // Phase 2: Executed & Contacts (only if Phase 1 was successful)
        if (!isset($results['phase1']['error'])) {
            $results['phase2'] = $this->importFromExcel($file2Path, 'update');
        }

        // Phase 3: Details Updated (only if Phase 2 was successful)
        if (!isset($results['phase2']['error'])) {
            $results['phase3'] = $this->importFromExcel($file3Path, 'review');
        }

        // Summary
        $results['summary'] = [
            'total_imported' => ($results['phase1']['imported'] ?? 0) + ($results['phase2']['imported'] ?? 0) + ($results['phase3']['imported'] ?? 0),
            'total_updated' => ($results['phase1']['updated'] ?? 0) + ($results['phase2']['updated'] ?? 0) + ($results['phase3']['updated'] ?? 0),
            'total_skipped' => ($results['phase1']['skipped'] ?? 0) + ($results['phase2']['skipped'] ?? 0) + ($results['phase3']['skipped'] ?? 0),
            'phases_completed' => count(array_filter($results, function ($result, $key) {
                return strpos($key, 'phase') === 0 && !isset($result['error']);
            }, ARRAY_FILTER_USE_BOTH))
        ];

        return $results;
    }
}

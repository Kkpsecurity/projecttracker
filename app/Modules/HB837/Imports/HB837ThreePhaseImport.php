<?php

namespace App\Modules\HB837\Imports;

use App\Models\HB837;
use App\Models\Consultant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class HB837ThreePhaseImport implements ToModel, WithHeadingRow, WithValidation
{
    private $mappings = [];
    private $options = [];
    private $phase = 'import';

    // Counters
    private $importedCount = 0;
    private $updatedCount = 0;
    private $skippedCount = 0;
    private $totalProcessed = 0;
    private $totalRows = 0;

    // Error tracking
    private $errors = [];
    private $criticalErrors = [];
    private $validationErrors = [];
    private $fieldErrors = [];
    private $skippedRecords = [];

    // Preview mode
    private $previewMode = false;
    private $previewLimit = 10;
    private $previewData = [];

    // Validation mode
    private $validationOnlyMode = false;
    private $validRowCount = 0;
    private $invalidRowCount = 0;

    // Modes
    private $truncateMode = false;

    /**
     * Set field mappings for import
     */
    public function setMappings(array $mappings): self
    {
        $this->mappings = $mappings;
        return $this;
    }

    /**
     * Set import options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set truncate mode
     */
    public function setTruncateMode(bool $enabled = true): self
    {
        $this->truncateMode = $enabled;
        return $this;
    }

    /**
     * Set preview mode
     */
    public function setPreviewMode(bool $enabled = true, int $limit = 10): self
    {
        $this->previewMode = $enabled;
        $this->previewLimit = $limit;
        return $this;
    }

    /**
     * Set validation only mode
     */
    public function setValidationOnlyMode(bool $enabled = true): self
    {
        $this->validationOnlyMode = $enabled;
        return $this;
    }

    /**
     * Transform a row into a model
     */
    public function model(array $row)
    {
        $this->totalProcessed++;
        $this->totalRows++;

        try {
            // Map fields according to provided mappings
            $mappedData = $this->mapRowData($row);

            // Validate the mapped data
            $validation = $this->validateRowData($mappedData);

            if (!$validation['valid']) {
                $this->invalidRowCount++;
                $this->validationErrors[] = [
                    'row' => $this->totalProcessed,
                    'data' => $mappedData,
                    'errors' => $validation['errors']
                ];

                if (!$this->validationOnlyMode) {
                    $this->skippedCount++;
                    $this->skippedRecords[] = [
                        'row' => $this->totalProcessed,
                        'reason' => 'Validation failed',
                        'errors' => $validation['errors'],
                        'data' => $mappedData
                    ];
                }

                return null;
            }

            $this->validRowCount++;

            // If in validation-only mode, don't create records
            if ($this->validationOnlyMode) {
                return null;
            }

            // If in preview mode, collect data and return
            if ($this->previewMode && count($this->previewData) < $this->previewLimit) {
                $this->previewData[] = $mappedData;
                return null;
            } elseif ($this->previewMode) {
                return null;
            }

            // Process the data for actual import
            return $this->processRecord($mappedData);

        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->totalProcessed,
                'error' => $e->getMessage(),
                'data' => $row
            ];

            Log::error('HB837 import row processing failed', [
                'row' => $this->totalProcessed,
                'error' => $e->getMessage(),
                'data' => $row
            ]);

            return null;
        }
    }

    /**
     * Map row data according to field mappings
     */
    private function mapRowData(array $row): array
    {
        $mappedData = [];

        foreach ($this->mappings as $csvField => $dbField) {
            $value = $row[$csvField] ?? null;

            // Clean and transform the value
            $mappedData[$dbField] = $this->transformFieldValue($dbField, $value);
        }

        // Apply default values
        $defaults = config('hb837.defaults', []);
        foreach ($defaults as $field => $defaultValue) {
            if (!isset($mappedData[$field]) || empty($mappedData[$field])) {
                $mappedData[$field] = $defaultValue;
            }
        }

        // Add metadata
        $mappedData['user_id'] = Auth::id();

        return $mappedData;
    }

    /**
     * Transform field value based on field type
     */
    private function transformFieldValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Date fields
        if (in_array($field, ['scheduled_date_of_inspection', 'report_submitted', 'billing_req_sent', 'agreement_submitted'])) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Numeric fields
        if (in_array($field, ['quoted_price', 'sub_fees_estimated_expenses', 'project_net_profit'])) {
            $numericValue = preg_replace('/[^0-9.]/', '', $value);
            return is_numeric($numericValue) ? (float) $numericValue : null;
        }

        // Integer fields
        if (in_array($field, ['units'])) {
            return is_numeric($value) ? (int) $value : null;
        }

        // Consultant lookup
        if ($field === 'assigned_consultant_id') {
            return $this->lookupConsultant($value);
        }

        // String fields - clean and trim
        return trim($value);
    }

    /**
     * Look up consultant by name
     */
    private function lookupConsultant($consultantName)
    {
        if (empty($consultantName)) {
            return null;
        }

        $consultant = Consultant::where(function ($query) use ($consultantName) {
            $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$consultantName}%"])
                  ->orWhere('first_name', 'LIKE', "%{$consultantName}%")
                  ->orWhere('last_name', 'LIKE', "%{$consultantName}%");
        })->first();

        return $consultant ? $consultant->id : null;
    }

    /**
     * Validate row data
     */
    private function validateRowData(array $data): array
    {
        $rules = [
            'property_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'property_type' => 'in:garden,midrise,highrise,industrial,bungalo',
            'contracting_status' => 'in:executed,quoted,started,closed',
            'report_status' => 'in:not-started,in-progress,in-review,completed',
            'quoted_price' => 'nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
        ];

        $validator = Validator::make($data, $rules);

        return [
            'valid' => !$validator->fails(),
            'errors' => $validator->errors()->all()
        ];
    }

    /**
     * Process record for import
     */
    private function processRecord(array $data)
    {
        try {
            // Check for existing record if not in truncate mode
            $existing = null;
            if (!$this->truncateMode) {
                $existing = HB837::where('property_name', $data['property_name'])
                    ->where('address', $data['address'])
                    ->first();
            }

            if ($existing) {
                // Update existing record
                $existing->update($data);
                $this->updatedCount++;
                return $existing;
            } else {
                // Create new record
                $record = HB837::create($data);
                $this->importedCount++;
                return $record;
            }

        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->totalProcessed,
                'error' => 'Database error: ' . $e->getMessage(),
                'data' => $data
            ];

            throw $e;
        }
    }

    /**
     * Validation rules for Excel import
     */
    public function rules(): array
    {
        return [
            // Basic validation rules can be added here
        ];
    }

    // Getters
    public function getImportedCount(): int { return $this->importedCount; }
    public function getUpdatedCount(): int { return $this->updatedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
    public function getTotalProcessed(): int { return $this->totalProcessed; }
    public function getTotalRows(): int { return $this->totalRows; }
    public function getErrors(): array { return $this->errors; }
    public function getCriticalErrors(): array { return $this->criticalErrors; }
    public function getValidationErrors(): array { return $this->validationErrors; }
    public function getFieldErrors(): array { return $this->fieldErrors; }
    public function getSkippedRecords(): array { return $this->skippedRecords; }
    public function getPreviewData(): array { return $this->previewData; }
    public function isValid(): bool { return empty($this->criticalErrors) && empty($this->validationErrors); }
    public function getValidRowCount(): int { return $this->validRowCount; }
    public function getInvalidRowCount(): int { return $this->invalidRowCount; }
}

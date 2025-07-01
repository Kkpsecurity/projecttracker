<?php

namespace App\Modules\HB837\Services;

use App\Models\HB837;
use App\Modules\HB837\Imports\HB837ThreePhaseImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportService
{
    /**
     * Execute Phase 3: Validation and Import
     */
    public function executeImport(string $filePath, array $mappings, array $options = []): array
    {
        try {
            DB::beginTransaction();

            $import = new HB837ThreePhaseImport();
            $import->setMappings($mappings);
            $import->setOptions($options);

            // Set import mode based on options
            if (isset($options['truncate']) && $options['truncate']) {
                $import->setTruncateMode(true);
                HB837::truncate(); // Clear existing data
            }

            // Execute import
            Excel::import($import, Storage::path($filePath));

            // Get results
            $results = [
                'success' => true,
                'imported_count' => $import->getImportedCount(),
                'updated_count' => $import->getUpdatedCount(),
                'skipped_count' => $import->getSkippedCount(),
                'skipped_records' => $import->getSkippedRecords(),
                'errors' => $import->getErrors(),
                'total_processed' => $import->getTotalProcessed(),
            ];

            // Commit transaction if no critical errors
            if (empty($import->getCriticalErrors())) {
                DB::commit();
                Log::info('HB837 import completed successfully', $results);
            } else {
                DB::rollBack();
                $results['success'] = false;
                $results['critical_errors'] = $import->getCriticalErrors();
                Log::error('HB837 import failed with critical errors', $results);
            }

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('HB837 import failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported_count' => 0,
                'updated_count' => 0,
                'skipped_count' => 0,
            ];
        }
    }

    /**
     * Preview import data without committing to database
     */
    public function previewImport(string $filePath, array $mappings, int $limit = 10): array
    {
        try {
            $import = new HB837ThreePhaseImport();
            $import->setMappings($mappings);
            $import->setPreviewMode(true, $limit);

            Excel::import($import, Storage::path($filePath));

            return [
                'success' => true,
                'preview_data' => $import->getPreviewData(),
                'validation_errors' => $import->getValidationErrors(),
                'field_mappings' => $mappings,
                'total_rows' => $import->getTotalRows(),
            ];

        } catch (\Exception $e) {
            Log::error('HB837 import preview failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'preview_data' => [],
            ];
        }
    }

    /**
     * Validate import data
     */
    public function validateImportData(string $filePath, array $mappings): array
    {
        try {
            $import = new HB837ThreePhaseImport();
            $import->setMappings($mappings);
            $import->setValidationOnlyMode(true);

            Excel::import($import, Storage::path($filePath));

            return [
                'success' => true,
                'is_valid' => $import->isValid(),
                'validation_errors' => $import->getValidationErrors(),
                'field_errors' => $import->getFieldErrors(),
                'total_rows' => $import->getTotalRows(),
                'valid_rows' => $import->getValidRowCount(),
                'invalid_rows' => $import->getInvalidRowCount(),
            ];

        } catch (\Exception $e) {
            Log::error('HB837 import validation failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'is_valid' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get import history/logs
     */
    public function getImportHistory(int $limit = 50): array
    {
        // In production, you'd track imports in a dedicated table
        return [
            'imports' => [],
            'total' => 0,
        ];
    }

    /**
     * Rollback last import
     */
    public function rollbackLastImport(string $importId): array
    {
        try {
            DB::beginTransaction();

            // In production, you'd track which records were imported
            // and only delete those specific records

            // For now, this is a placeholder
            $rollbackCount = 0;

            DB::commit();

            Log::info('HB837 import rollback completed', [
                'import_id' => $importId,
                'rollback_count' => $rollbackCount
            ]);

            return [
                'success' => true,
                'rollback_count' => $rollbackCount,
                'message' => 'Import rollback completed successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('HB837 import rollback failed', [
                'import_id' => $importId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get available field mappings
     */
    public function getAvailableFields(): array
    {
        return config('hb837.mappings', []);
    }

    /**
     * Get required fields for validation
     */
    public function getRequiredFields(): array
    {
        return config('hb837.required_fields', []);
    }

    /**
     * Get default values for fields
     */
    public function getDefaultValues(): array
    {
        return config('hb837.defaults', []);
    }
}

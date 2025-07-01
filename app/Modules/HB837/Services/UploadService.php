<?php

namespace App\Modules\HB837\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Three phases of upload workflow
     */
    const PHASE_UPLOAD = 'upload';
    const PHASE_MAPPING = 'mapping';
    const PHASE_VALIDATION = 'validation';

    /**
     * Store uploaded file and return file info for Phase 1
     */
    public function storeUploadedFile(UploadedFile $file): array
    {
        try {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = 'uploads/hb837/' . date('Y/m/d');

            // Store file
            $fullPath = $file->storeAs($path, $filename, 'local');

            // Get file info
            $fileInfo = [
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $fullPath,
                'filename' => $filename,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'upload_session' => Str::uuid(),
                'uploaded_at' => now(),
            ];

            Log::info('HB837 file uploaded successfully', $fileInfo);

            return $fileInfo;

        } catch (\Exception $e) {
            Log::error('HB837 file upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            throw $e;
        }
    }

    /**
     * Analyze uploaded file structure for Phase 2 (Field Mapping)
     */
    public function analyzeFileStructure(string $filePath): array
    {
        try {
            $fullPath = Storage::path($filePath);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            if (strtolower($extension) === 'csv') {
                return $this->analyzeCsvStructure($fullPath);
            } elseif (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                return $this->analyzeExcelStructure($fullPath);
            }

            throw new \Exception('Unsupported file type: ' . $extension);

        } catch (\Exception $e) {
            Log::error('File structure analysis failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Analyze CSV file structure
     */
    private function analyzeCsvStructure(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception('Could not read CSV headers');
        }

        // Read a few sample rows
        $sampleRows = [];
        $rowCount = 0;
        while (($row = fgetcsv($handle)) && $rowCount < 5) {
            $sampleRows[] = array_combine($headers, $row);
            $rowCount++;
        }

        // Count total rows
        $totalRows = $rowCount;
        while (fgetcsv($handle)) {
            $totalRows++;
        }

        fclose($handle);

        return [
            'headers' => $headers,
            'sample_data' => $sampleRows,
            'total_rows' => $totalRows,
            'file_type' => 'csv'
        ];
    }

    /**
     * Analyze Excel file structure
     */
    private function analyzeExcelStructure(string $filePath): array
    {
        // For now, treat as CSV conversion needed
        // In production, you'd use PhpSpreadsheet
        return [
            'headers' => [],
            'sample_data' => [],
            'total_rows' => 0,
            'file_type' => 'excel',
            'message' => 'Excel files need to be converted to CSV format'
        ];
    }

    /**
     * Get suggested field mappings based on column names
     */
    public function getSuggestedMappings(array $headers): array
    {
        $mappings = config('hb837.mappings', []);
        $suggestions = [];

        foreach ($headers as $header) {
            $normalized = $this->normalizeHeaderName($header);

            // Direct match
            if (array_key_exists($normalized, $mappings)) {
                $suggestions[$header] = $normalized;
                continue;
            }

            // Fuzzy matching
            foreach ($mappings as $field => $description) {
                if ($this->fuzzyMatch($normalized, $field) ||
                    $this->fuzzyMatch($normalized, $description)) {
                    $suggestions[$header] = $field;
                    break;
                }
            }
        }

        return $suggestions;
    }

    /**
     * Normalize header name for matching
     */
    private function normalizeHeaderName(string $header): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $header), '_'));
    }

    /**
     * Fuzzy matching for field names
     */
    private function fuzzyMatch(string $needle, string $haystack): bool
    {
        $needle = strtolower($needle);
        $haystack = strtolower($haystack);

        // Exact match
        if ($needle === $haystack) {
            return true;
        }

        // Contains match
        if (strpos($haystack, $needle) !== false || strpos($needle, $haystack) !== false) {
            return true;
        }

        // Similar match (at least 80% similarity)
        $similarity = 0;
        similar_text($needle, $haystack, $similarity);
        return $similarity >= 80;
    }

    /**
     * Clean up uploaded file
     */
    public function cleanupFile(string $filePath): bool
    {
        try {
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                Log::info('HB837 upload file cleaned up', ['file_path' => $filePath]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup HB837 upload file', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate upload session
     */
    public function validateSession(string $sessionId): bool
    {
        // In production, you'd check session storage or database
        return !empty($sessionId) && Str::isUuid($sessionId);
    }
}

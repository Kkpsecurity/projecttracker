<?php

namespace App\Services;

use App\Imports\HB837Import;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ImportService
{
    public function previewImport(string $filePath): array
    {
        $import = new HB837Import();

        try {
            $preview = Excel::toArray($import, $filePath)[0];
            $firstRow = $preview[0] ?? [];

            return [
                'headers_found' => array_keys($firstRow),
                'headers_expected' => $this->getExpectedHeaders(),
                'sample_data' => array_slice($preview, 0, 5),
                'validation' => $this->validateHeaders($firstRow),
            ];
        } catch (\Exception $e) {
            Log::error("Import preview failed", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function executeImport(string $filePath): array
    {
        $import = new HB837Import();

        try {
            Excel::import($import, $filePath);

            return [
                'import_id' => $import->getImportId(),
                'stats' => $import->getStats(),
                'errors' => $import->getRowErrors(),
            ];
        } catch (\Exception $e) {
            Log::error("Import failed", [
                'import_id' => $import->getImportId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function getExpectedHeaders(): array
    {
        $mappings = config('hb837.mappings');

        return array_unique(array_reduce($mappings, function($carry, $item) {
            return array_merge($carry, (array)$item);
        }, []));
    }

    protected function validateHeaders(array $headers): array
    {
        $required = config('hb837.required_fields');
        $missing = array_diff($required, $headers);

        return [
            'is_valid' => empty($missing),
            'missing_required' => array_values($missing),
            'extra_headers' => array_diff($headers, $this->getExpectedHeaders()),
        ];
    }
}

<?php

namespace App\Imports;

/**
 * BaseImport class provides a foundation for importing data from Excel files.
 * It includes methods for handling common import tasks such as validation,
 * mapping, and error handling.
 *
 * @version 2.0.0
 */

use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

abstract class BaseImport implements WithHeadingRow
{
    protected $importId;

    protected $rowErrors = [];

    protected $stats = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'failed' => 0,
    ];

    protected $currentRow;

    protected $modelClass;

    protected $identifierFields = ['address', 'property_name'];

    public function __construct()
    {
        $this->importId = Str::uuid();
        $this->init();
    }

    abstract protected function init();

    abstract protected function mapData(array $row);

    abstract protected function validateRow(array $row): bool;

    abstract protected function findExistingModel(array $data);

    abstract protected function handleImportRecord(array $data);

    public function getImportId()
    {
        return $this->importId;
    }

    public function getStats()
    {
        return $this->stats;
    }

    public function getRowErrors()
    {
        return $this->rowErrors;
    }

    protected function addRowError(string $reason, array $context = [])
    {
        $this->rowErrors[] = [
            'row' => $this->currentRow,
            'reason' => $reason,
            'context' => $context,
        ];
        $this->stats['failed']++;
    }

    protected function validateHeaders(array $row): bool
    {
        $required = config('hb837.required_fields');
        $missing = array_diff($required, array_keys($row));

        if (! empty($missing)) {
            $this->addRowError(
                'Missing required headers',
                ['missing_headers' => $missing]
            );

            return false;
        }

        return true;
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($value - 25569) * 86400))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseCurrency($value): float
    {
        return $value ? (float) preg_replace('/[^\d.]/', '', $value) : 0.0;
    }

    protected function parseInteger($value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    protected function cleanString($value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}

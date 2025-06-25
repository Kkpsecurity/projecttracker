<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SelectedTablesExport implements WithMultipleSheets
{
    protected array $tables;

    public function __construct(array $tables)
    {
        $this->tables = $tables;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->tables as $table) {
            switch ($table) {
                case 'hb837':
                    $sheets[] = new HB837Export(); break;
                case 'consultants':
                    $sheets[] = new \App\Exports\GenericTableExport('consultants'); break;
                case 'hb837_files':
                    $sheets[] = new \App\Exports\GenericTableExport('hb837_files'); break;
                case 'consultant_files':
                    $sheets[] = new \App\Exports\GenericTableExport('consultant_files'); break;
                default:
                    continue;
            }
        }

        return $sheets;
    }
}

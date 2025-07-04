<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DynamicBackupExport implements WithMultipleSheets
{
    /**
     * @var array
     */
    protected $tables;

    public function __construct(array $tables)
    {
        $this->tables = $tables;
    }

    public function sheets(): array
    {
        $sheets = [];
        $include_files = in_array('consultants', $this->tables);

        foreach ($this->tables as $table) {
            switch ($table) {
                case 'consultants':
                    $sheets[] = new ConsultantExport(false);
                    break;

                case 'hb837':
                    $sheets[] = new HB837Export(false); // or handle this smart later
                    break;

                default:
                    $sheets[] = new GenericTableExport($table);
                    break;
            }
        }

        return $sheets;
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GenericTableExport implements FromCollection, WithHeadings, WithTitle
{
    protected $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function collection()
    {
        return DB::table($this->table)->get();
    }

    public function headings(): array
    {
        $firstRow = DB::table($this->table)->first();

        if ($firstRow) {
            return array_keys((array) $firstRow);
        }

        // fallback: get column names from schema if table is empty
        return Schema::getColumnListing($this->table);
    }

    public function title(): string
    {
        $safe = preg_replace('/[:\\\\\\/\\?\\*\\[\\]]/', '', $this->table);

        return substr($safe, 0, 31);
    }
}

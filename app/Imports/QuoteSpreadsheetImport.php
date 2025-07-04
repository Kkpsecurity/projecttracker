<?php

namespace App\Imports;

use App\Models\Quote;
use Maatwebsite\Excel\Concerns\ToModel;

class QuoteSpreadsheetImport implements ToModel
{
    public function model(array $row)
    {
        return new Quote([
            // ...existing mappings...
            // Map column "W" (index 22: zero-indexed) to quoted_price:
            'quoted_price' => $row[22],
            // ...existing code...
        ]);
    }
}

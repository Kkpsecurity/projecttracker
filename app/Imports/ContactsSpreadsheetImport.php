<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;

class ContactsSpreadsheetImport implements ToModel
{
    public function model(array $row)
    {
        // Assume 'property_name' is a unique identifier for contact records.
        $contact = Contact::firstOrNew(['property_name' => $row[0]]);
        $contact->fill([
            // Map spreadsheet columns to contact fields.
            'contact_name' => $row[1],
            'phone' => $row[2],
            // ...existing mappings...
        ]);

        return $contact;
    }
}

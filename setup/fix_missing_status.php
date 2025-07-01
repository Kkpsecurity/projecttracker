<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Fixing Records with Missing Status Values\n";
echo "========================================\n\n";

// Find records with missing status values
$recordsToFix = HB837::where(function($query) {
    $query->whereNull('report_status')
          ->orWhere('report_status', '')
          ->orWhereNull('contracting_status')
          ->orWhere('contracting_status', '');
})->get();

echo "Found " . $recordsToFix->count() . " records with missing status values:\n\n";

foreach ($recordsToFix as $record) {
    echo "ID {$record->id}: {$record->property_name}\n";
    echo "  Before - Report: '" . ($record->report_status ?? 'NULL') . "', Contracting: '" . ($record->contracting_status ?? 'NULL') . "'\n";

    $updates = [];

    if (empty($record->report_status)) {
        $updates['report_status'] = 'not-started';
    }

    if (empty($record->contracting_status)) {
        $updates['contracting_status'] = 'quoted';
    }

    if (!empty($updates)) {
        $record->update($updates);
        echo "  After  - Report: '{$record->fresh()->report_status}', Contracting: '{$record->fresh()->contracting_status}'\n";
        echo "  ✓ Updated\n";
    } else {
        echo "  - No updates needed\n";
    }

    echo "\n";
}

echo "All records have been updated with default status values!\n";
echo "Records will now appear in the datatable tabs.\n";

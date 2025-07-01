<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Status Values of Recent Records\n";
echo "=======================================\n\n";

// Get the TEST PROPERTY records that were just updated
$testRecords = HB837::whereIn('property_name', ['TEST PROPERTY A', 'TEST PROPERTY B'])->get();

foreach ($testRecords as $record) {
    echo "ID: {$record->id}\n";
    echo "Property: {$record->property_name}\n";
    echo "Report Status: '" . ($record->report_status ?? 'NULL') . "'\n";
    echo "Contracting Status: '" . ($record->contracting_status ?? 'NULL') . "'\n";
    echo "Would show in tabs:\n";

    // Check active tab
    $activeMatch = in_array($record->report_status, ['not-started', 'in-progress', 'in-review']) &&
                   $record->contracting_status === 'executed';
    echo "  - Active: " . ($activeMatch ? "✓ YES" : "✗ NO") . "\n";

    // Check quoted tab
    $quotedMatch = in_array($record->contracting_status, ['quoted', 'started']);
    echo "  - Quoted: " . ($quotedMatch ? "✓ YES" : "✗ NO") . "\n";

    // Check completed tab
    $completedMatch = $record->report_status === 'completed';
    echo "  - Completed: " . ($completedMatch ? "✓ YES" : "✗ NO") . "\n";

    // Check closed tab
    $closedMatch = $record->contracting_status === 'closed';
    echo "  - Closed: " . ($closedMatch ? "✓ YES" : "✗ NO") . "\n";

    echo "----------------------------------------\n";
}

// Also check all records to see which ones would show in each tab
echo "\nAll Records Tab Visibility:\n";
echo "===========================\n";

$allRecords = HB837::all();
$tabs = ['active', 'quoted', 'completed', 'closed'];

foreach ($tabs as $tab) {
    echo "\n{$tab} tab:\n";

    foreach ($allRecords as $record) {
        $visible = false;

        switch ($tab) {
            case 'active':
                $visible = in_array($record->report_status, ['not-started', 'in-progress', 'in-review']) &&
                          $record->contracting_status === 'executed';
                break;
            case 'quoted':
                $visible = in_array($record->contracting_status, ['quoted', 'started']);
                break;
            case 'completed':
                $visible = $record->report_status === 'completed';
                break;
            case 'closed':
                $visible = $record->contracting_status === 'closed';
                break;
        }

        if ($visible) {
            echo "  ✓ ID {$record->id}: {$record->property_name}\n";
        }
    }
}

echo "\nRecords that don't appear in any tab:\n";
foreach ($allRecords as $record) {
    $inActive = in_array($record->report_status, ['not-started', 'in-progress', 'in-review']) &&
                $record->contracting_status === 'executed';
    $inQuoted = in_array($record->contracting_status, ['quoted', 'started']);
    $inCompleted = $record->report_status === 'completed';
    $inClosed = $record->contracting_status === 'closed';

    if (!$inActive && !$inQuoted && !$inCompleted && !$inClosed) {
        echo "  ✗ ID {$record->id}: {$record->property_name} (report: '{$record->report_status}', contracting: '{$record->contracting_status}')\n";
    }
}

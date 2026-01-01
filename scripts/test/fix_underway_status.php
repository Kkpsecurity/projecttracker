<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HB837;

echo "=== CHECKING UNDERWAY vs IN-PROGRESS ===\n";
$underwayRecords = HB837::where('report_status', 'underway')->get();
echo "Records with 'underway' status: " . $underwayRecords->count() . "\n";
foreach ($underwayRecords as $record) {
    echo "[{$record->id}] {$record->property_name}: {$record->report_status}\n";
}

$inProgressRecords = HB837::where('report_status', 'in-progress')->get();
echo "\nRecords with 'in-progress' status: " . $inProgressRecords->count() . "\n";
foreach ($inProgressRecords as $record) {
    echo "[{$record->id}] {$record->property_name}: {$record->report_status}\n";
}

// Check if we need to fix the underway records
if ($underwayRecords->count() > 0) {
    echo "\n=== FIXING UNDERWAY RECORDS ===\n";
    foreach ($underwayRecords as $record) {
        $record->report_status = 'in-progress';
        $record->save();
        echo "✅ Fixed [{$record->id}] {$record->property_name}: underway → in-progress\n";
    }
    
    echo "\n=== VERIFICATION AFTER FIX ===\n";
    $underwayAfter = HB837::where('report_status', 'underway')->count();
    $inProgressAfter = HB837::where('report_status', 'in-progress')->count();
    echo "Records with 'underway' status: $underwayAfter\n";
    echo "Records with 'in-progress' status: $inProgressAfter\n";
} else {
    echo "\n✅ No 'underway' records found that need fixing.\n";
}

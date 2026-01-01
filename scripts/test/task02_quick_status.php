<?php
/**
 * Task 02 Testing Script: Quick Status Check
 * Quick overview of current system state
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$totalRecords = App\Models\HB837::count();
$testRecords = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')->count();
$latestRecord = App\Models\HB837::latest()->first();

echo "==========================================\n";
echo "        TASK 02: QUICK STATUS CHECK       \n";
echo "==========================================\n";
echo "Total HB837 records: {$totalRecords}\n";
echo "Test property records: {$testRecords}\n";
echo "Latest record: " . ($latestRecord ? "#{$latestRecord->id} - {$latestRecord->property_name}" : "None") . "\n";
echo "Latest update: " . ($latestRecord ? $latestRecord->updated_at : "N/A") . "\n";
echo "==========================================\n";

// Quick check if import is ready
if ($totalRecords == 3 && $testRecords == 2) {
    echo "STATUS: ✅ Ready for TEST SHEET 01 import\n";
    echo "NEXT: Run pre-import check, then import\n";
} elseif ($totalRecords > 3) {
    echo "STATUS: 📊 Records added - validate import results\n";
    echo "NEXT: Run post-import validation\n";
} else {
    echo "STATUS: ⚠️  Unexpected state\n";
    echo "NEXT: Check system status\n";
}
echo "==========================================\n";

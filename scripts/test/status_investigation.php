<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HB837;

echo "=== DETAILED STATUS BREAKDOWN INVESTIGATION ===\n\n";

// Get all records with details
$records = HB837::select('id', 'property_name', 'report_status', 'contracting_status')->get();

echo "Total Records: " . $records->count() . "\n\n";

echo "=== REPORT STATUS BREAKDOWN ===\n";
$reportStatuses = $records->groupBy('report_status');
foreach ($reportStatuses as $status => $group) {
    echo "- " . ($status ?: 'NULL') . ": " . $group->count() . "\n";
    foreach ($group as $record) {
        echo "  * [{$record->id}] {$record->property_name}\n";
    }
}

echo "\n=== CONTRACTING STATUS BREAKDOWN ===\n";
$contractingStatuses = $records->groupBy('contracting_status');
foreach ($contractingStatuses as $status => $group) {
    echo "- " . ($status ?: 'NULL') . ": " . $group->count() . "\n";
    foreach ($group as $record) {
        echo "  * [{$record->id}] {$record->property_name}\n";
    }
}

echo "\n=== PROJECT STATUS CALCULATION ===\n";
// Check what determines project status (Active/Quoted/Completed/Closed)
$active = HB837::where('report_status', 'not-started')->orWhere('report_status', 'in-progress')->count();
$quoted = HB837::where('contracting_status', 'quoted')->count();
$completed = HB837::where('report_status', 'completed')->count();
$closed = HB837::where('contracting_status', 'closed')->count();

echo "Calculated Project Status:\n";
echo "- Active (not-started + in-progress): $active\n";
echo "- Quoted (contracting_status = quoted): $quoted\n";
echo "- Completed (report_status = completed): $completed\n";
echo "- Closed (contracting_status = closed): $closed\n";

echo "\n=== DASHBOARD EXPECTED COUNTS ===\n";
echo "From screenshot:\n";
echo "- Active Projects: 0\n";
echo "- Quoted Projects: 4\n";
echo "- Completed: 2\n";
echo "- Closed: 1\n";

echo "\n=== NULL VALUES CHECK ===\n";
$nullReport = HB837::whereNull('report_status')->count();
$nullContracting = HB837::whereNull('contracting_status')->count();
echo "Records with NULL report_status: $nullReport\n";
echo "Records with NULL contracting_status: $nullContracting\n";

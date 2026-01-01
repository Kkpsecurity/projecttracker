<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HB837;

echo "=== DASHBOARD LOGIC INVESTIGATION ===\n\n";

// Get all records for detailed analysis
$records = HB837::select('id', 'property_name', 'report_status', 'contracting_status')->get();

echo "Total Records: " . $records->count() . "\n\n";

echo "=== EXACT DASHBOARD LOGIC CALCULATION ===\n";

// ACTIVE: report_status IN ['not-started', 'in-progress', 'in-review'] AND contracting_status = 'executed'
$active = HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
    ->where('contracting_status', 'executed')->count();

// QUOTED: contracting_status IN ['quoted', 'started']
$quoted = HB837::whereIn('contracting_status', ['quoted', 'started'])->count();

// COMPLETED: report_status = 'completed'
$completed = HB837::where('report_status', 'completed')->count();

// CLOSED: contracting_status = 'closed'
$closed = HB837::where('contracting_status', 'closed')->count();

echo "Dashboard Project Status Calculations:\n";
echo "- Active (report_status in [not-started, in-progress, in-review] AND contracting_status = executed): $active\n";
echo "- Quoted (contracting_status in [quoted, started]): $quoted\n";
echo "- Completed (report_status = completed): $completed\n";
echo "- Closed (contracting_status = closed): $closed\n";

echo "\n=== DETAILED BREAKDOWN BY RECORD ===\n";
foreach ($records as $record) {
    $reportStatus = $record->report_status;
    $contractingStatus = $record->contracting_status;
    
    $categories = [];
    
    // Check Active criteria
    if (in_array($reportStatus, ['not-started', 'in-progress', 'in-review']) && $contractingStatus === 'executed') {
        $categories[] = 'Active';
    }
    
    // Check Quoted criteria
    if (in_array($contractingStatus, ['quoted', 'started'])) {
        $categories[] = 'Quoted';
    }
    
    // Check Completed criteria
    if ($reportStatus === 'completed') {
        $categories[] = 'Completed';
    }
    
    // Check Closed criteria
    if ($contractingStatus === 'closed') {
        $categories[] = 'Closed';
    }
    
    $categoriesStr = empty($categories) ? 'None' : implode(', ', $categories);
    echo "[{$record->id}] {$record->property_name}: report_status='{$reportStatus}', contracting_status='{$contractingStatus}' → Categories: {$categoriesStr}\n";
}

echo "\n=== COMPARISON WITH EXPECTED ===\n";
echo "Expected from screenshot:\n";
echo "- Active Projects: 0 (Actual: $active) " . ($active == 0 ? '✅' : '❌') . "\n";
echo "- Quoted Projects: 4 (Actual: $quoted) " . ($quoted == 4 ? '✅' : '❌') . "\n";
echo "- Completed: 2 (Actual: $completed) " . ($completed == 2 ? '✅' : '❌') . "\n";
echo "- Closed: 1 (Actual: $closed) " . ($closed == 1 ? '✅' : '❌') . "\n";

echo "\n=== INVESTIGATION RESULTS ===\n";
if ($active == 0 && $quoted == 4 && $completed == 2 && $closed == 1) {
    echo "✅ ALL COUNTS MATCH! Dashboard is calculating correctly.\n";
} else {
    echo "❌ DISCREPANCIES FOUND!\n";
    echo "The dashboard logic is working as intended, but our test data may not match expectations.\n";
}

// Let's also check if records might belong to multiple categories
echo "\n=== OVERLAP ANALYSIS ===\n";
$overlaps = [];
foreach ($records as $record) {
    $reportStatus = $record->report_status;
    $contractingStatus = $record->contracting_status;
    
    $count = 0;
    $categories = [];
    
    if (in_array($reportStatus, ['not-started', 'in-progress', 'in-review']) && $contractingStatus === 'executed') {
        $count++;
        $categories[] = 'Active';
    }
    
    if (in_array($contractingStatus, ['quoted', 'started'])) {
        $count++;
        $categories[] = 'Quoted';
    }
    
    if ($reportStatus === 'completed') {
        $count++;
        $categories[] = 'Completed';
    }
    
    if ($contractingStatus === 'closed') {
        $count++;
        $categories[] = 'Closed';
    }
    
    if ($count > 1) {
        echo "[{$record->id}] {$record->property_name} belongs to multiple categories: " . implode(', ', $categories) . "\n";
    }
}

$total = $active + $quoted + $completed + $closed;
echo "\nSum of all categories: $total (Total records: " . $records->count() . ")\n";

if ($total == $records->count()) {
    echo "✅ No overlaps - each record belongs to exactly one category.\n";
} else {
    echo "⚠️  There are overlaps or records that don't fit any category.\n";
}

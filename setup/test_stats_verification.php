<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "HB837 Statistics Verification\n";
echo "============================\n\n";

// Get all records to analyze
$allRecords = HB837::all();
echo "Total records in database: " . $allRecords->count() . "\n\n";

// Current stats calculation (from controller)
$currentStats = [
    'active' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
        ->where('contracting_status', 'executed')->count(),
    'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
    'completed' => HB837::where('report_status', 'completed')->count(),
    'closed' => HB837::where('contracting_status', 'closed')->count(),
    'total' => HB837::count()
];

echo "Current Controller Stats:\n";
echo "-------------------------\n";
foreach ($currentStats as $key => $value) {
    echo ucfirst($key) . ": " . $value . "\n";
}

echo "\nDetailed Analysis:\n";
echo "==================\n";

// Analyze each record
$manualCounts = [
    'active' => 0,
    'quoted' => 0,
    'completed' => 0,
    'closed' => 0,
    'unmatched' => 0
];

echo "\nRecord-by-record analysis:\n";
echo "---------------------------\n";

foreach ($allRecords as $record) {
    echo "ID {$record->id}: {$record->property_name}\n";
    echo "  Report Status: '" . ($record->report_status ?: 'NULL') . "'\n";
    echo "  Contracting Status: '" . ($record->contracting_status ?: 'NULL') . "'\n";

    // Check which category this record falls into
    $matched = false;

    // Active: report_status in ['not-started', 'in-progress', 'in-review'] AND contracting_status = 'executed'
    if (in_array($record->report_status, ['not-started', 'in-progress', 'in-review']) &&
        $record->contracting_status === 'executed') {
        echo "  → ACTIVE\n";
        $manualCounts['active']++;
        $matched = true;
    }

    // Quoted: contracting_status in ['quoted', 'started']
    if (in_array($record->contracting_status, ['quoted', 'started'])) {
        echo "  → QUOTED\n";
        $manualCounts['quoted']++;
        $matched = true;
    }

    // Completed: report_status = 'completed'
    if ($record->report_status === 'completed') {
        echo "  → COMPLETED\n";
        $manualCounts['completed']++;
        $matched = true;
    }

    // Closed: contracting_status = 'closed'
    if ($record->contracting_status === 'closed') {
        echo "  → CLOSED\n";
        $manualCounts['closed']++;
        $matched = true;
    }

    if (!$matched) {
        echo "  → UNMATCHED (doesn't fit any category)\n";
        $manualCounts['unmatched']++;
    }

    echo "\n";
}

echo "Manual Count Results:\n";
echo "---------------------\n";
foreach ($manualCounts as $key => $value) {
    echo ucfirst($key) . ": " . $value . "\n";
}

echo "\nComparison:\n";
echo "-----------\n";
echo "Category     | Controller | Manual | Match\n";
echo "-------------|------------|--------|-------\n";
foreach (['active', 'quoted', 'completed', 'closed'] as $category) {
    $controller = $currentStats[$category];
    $manual = $manualCounts[$category];
    $match = $controller === $manual ? "✓" : "✗";
    echo sprintf("%-12s | %-10s | %-6s | %s\n", ucfirst($category), $controller, $manual, $match);
}

if ($manualCounts['unmatched'] > 0) {
    echo "\n⚠️  Warning: {$manualCounts['unmatched']} records don't match any category!\n";
    echo "These records won't appear in any tab and won't be counted in the cards.\n";
}

echo "\nStats verification completed!\n";

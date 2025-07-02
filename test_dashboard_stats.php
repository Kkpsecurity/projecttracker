<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "HB837 Dashboard Statistics Diagnostic\n";
echo "=====================================\n\n";

try {
    // Get actual counts from database
    echo "1. Actual Database Counts:\n";
    echo "-------------------------\n";

    $totalProjects = HB837::count();
    echo "Total Projects: {$totalProjects}\n";

    // Count by report_status
    $statusCounts = HB837::selectRaw('report_status, COUNT(*) as count')
                          ->groupBy('report_status')
                          ->pluck('count', 'report_status')
                          ->toArray();

    echo "\nBy Report Status:\n";
    foreach ($statusCounts as $status => $count) {
        $statusLabel = $status ?: 'NULL/Empty';
        echo "  {$statusLabel}: {$count}\n";
    }

    // Expected dashboard mappings (common Laravel status mappings)
    echo "\n2. Expected Dashboard Mapping:\n";
    echo "------------------------------\n";

    $activeCount = HB837::where('report_status', 'active')->count();
    $quotedCount = HB837::where('report_status', 'quoted')->count();
    $completedCount = HB837::where('report_status', 'completed')->count();
    $closedCount = HB837::where('report_status', 'closed')->count();

    echo "Active Projects: {$activeCount}\n";
    echo "Quoted Projects: {$quotedCount}\n";
    echo "Completed Projects: {$completedCount}\n";
    echo "Closed Projects: {$closedCount}\n";

    // Check for alternative status field names
    echo "\n3. Checking Alternative Status Fields:\n";
    echo "--------------------------------------\n";

    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('hb837');
    $statusFields = array_filter($columns, function($col) {
        return strpos(strtolower($col), 'status') !== false;
    });

    echo "Status-related columns found: " . implode(', ', $statusFields) . "\n";

    // Sample data to understand the structure
    echo "\n4. Sample Records:\n";
    echo "------------------\n";

    $samples = HB837::select('id', 'property_name', 'report_status', 'created_at')
                    ->limit(5)
                    ->get();

    foreach ($samples as $sample) {
        echo "ID: {$sample->id}, Name: {$sample->property_name}, Status: '{$sample->report_status}', Created: {$sample->created_at}\n";
    }

    // Check if there are records without proper status
    echo "\n5. Data Quality Check:\n";
    echo "----------------------\n";

    $nullStatus = HB837::whereNull('report_status')->count();
    $emptyStatus = HB837::where('report_status', '')->count();

    echo "Records with NULL status: {$nullStatus}\n";
    echo "Records with empty status: {$emptyStatus}\n";

    if ($nullStatus > 0 || $emptyStatus > 0) {
        echo "\n⚠️  WARNING: Found records without proper status values!\n";
        echo "This could explain why dashboard cards don't match table data.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETED ===\n";
echo "\nNext Steps:\n";
echo "1. Check the dashboard controller logic\n";
echo "2. Update status values if needed\n";
echo "3. Verify status field mapping\n";
echo "4. Test dashboard refresh\n";

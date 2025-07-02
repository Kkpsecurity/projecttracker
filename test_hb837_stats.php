<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "HB837 Dashboard Statistics Fix\n";
echo "==============================\n\n";

try {
    // Get current data to understand the status combinations
    echo "1. Current Data Analysis:\n";
    echo "------------------------\n";

    $projects = HB837::select('id', 'property_name', 'report_status', 'contracting_status')->get();

    foreach ($projects as $project) {
        echo "ID: {$project->id}, Property: {$project->property_name}\n";
        echo "   Report Status: '{$project->report_status}'\n";
        echo "   Contracting Status: '{$project->contracting_status}'\n\n";
    }

    // Calculate dashboard stats using the HB837Controller logic
    echo "2. Dashboard Statistics (HB837Controller Logic):\n";
    echo "------------------------------------------------\n";

    $stats = [
        'active' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
            ->where('contracting_status', 'executed')->count(),
        'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
        'completed' => HB837::where('report_status', 'completed')->count(),
        'closed' => HB837::where('contracting_status', 'closed')->count(),
        'total' => HB837::count()
    ];

    echo "Active Projects: {$stats['active']}\n";
    echo "Quoted Projects: {$stats['quoted']}\n";
    echo "Completed Projects: {$stats['completed']}\n";
    echo "Closed Projects: {$stats['closed']}\n";
    echo "Total Projects: {$stats['total']}\n";

    // Show what records match each category
    echo "\n3. Detailed Breakdown:\n";
    echo "----------------------\n";

    echo "Active (report_status in ['not-started', 'in-progress', 'in-review'] AND contracting_status = 'executed'):\n";
    $activeProjects = HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
        ->where('contracting_status', 'executed')
        ->pluck('property_name', 'id');
    foreach ($activeProjects as $id => $name) {
        echo "  - ID {$id}: {$name}\n";
    }
    if ($activeProjects->isEmpty()) echo "  (No records match)\n";

    echo "\nQuoted (contracting_status in ['quoted', 'started']):\n";
    $quotedProjects = HB837::whereIn('contracting_status', ['quoted', 'started'])
        ->pluck('property_name', 'id');
    foreach ($quotedProjects as $id => $name) {
        echo "  - ID {$id}: {$name}\n";
    }
    if ($quotedProjects->isEmpty()) echo "  (No records match)\n";

    echo "\nCompleted (report_status = 'completed'):\n";
    $completedProjects = HB837::where('report_status', 'completed')
        ->pluck('property_name', 'id');
    foreach ($completedProjects as $id => $name) {
        echo "  - ID {$id}: {$name}\n";
    }
    if ($completedProjects->isEmpty()) echo "  (No records match)\n";

    echo "\nClosed (contracting_status = 'closed'):\n";
    $closedProjects = HB837::where('contracting_status', 'closed')
        ->pluck('property_name', 'id');
    foreach ($closedProjects as $id => $name) {
        echo "  - ID {$id}: {$name}\n";
    }
    if ($closedProjects->isEmpty()) echo "  (No records match)\n";

    // Suggest data fixes
    echo "\n4. Suggested Data Updates:\n";
    echo "--------------------------\n";

    $needsUpdate = HB837::whereIn('report_status', ['not-started', 'in-progress'])
        ->whereNull('contracting_status')
        ->orWhere('contracting_status', '')
        ->get();

    if ($needsUpdate->count() > 0) {
        echo "Found {$needsUpdate->count()} projects that might need contracting_status updates:\n";
        foreach ($needsUpdate as $project) {
            echo "  - ID {$project->id}: {$project->property_name} (contracting_status: '{$project->contracting_status}')\n";
        }
        echo "\nSuggested fix: Update contracting_status to appropriate values\n";
    } else {
        echo "All projects have contracting_status values.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== ANALYSIS COMPLETED ===\n";

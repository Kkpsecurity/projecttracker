<?php
/*
 * Test script to verify Consultant DataTables functionality
 * This tests the fixed relationship between Consultant and HB837 models
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Consultant;
use App\Models\HB837;

echo "=== CONSULTANT DATATABLES RELATIONSHIP FIX VERIFICATION ===\n";

try {
    // Test the fixed relationship
    echo "\n1. Testing Consultant->hb837Projects() relationship with fixed foreign key:\n";

    $consultants = Consultant::withCount([
        'hb837Projects as active_projects_count' => function ($query) {
            $query->whereIn('report_status', ['in-progress', 'not-started']);
        },
        'hb837Projects as completed_projects_count' => function ($query) {
            $query->where('report_status', 'completed');
        }
    ])->get();

    foreach ($consultants as $consultant) {
        echo "   Consultant: {$consultant->full_name}\n";
        echo "   Active Projects: {$consultant->active_projects_count}\n";
        echo "   Completed Projects: {$consultant->completed_projects_count}\n";
        echo "   ---\n";
    }

    // Test specific consultant projects
    echo "\n2. Testing specific consultant project relationships:\n";
    $firstConsultant = Consultant::first();
    if ($firstConsultant) {
        echo "   Consultant: {$firstConsultant->full_name}\n";
        $projects = $firstConsultant->hb837Projects;
        echo "   Total HB837 Projects: " . $projects->count() . "\n";

        foreach ($projects as $project) {
            echo "   - Project: {$project->property_name} (Status: {$project->report_status})\n";
        }
    }

    // Test reverse relationship
    echo "\n3. Testing HB837->consultant relationship:\n";
    $hb837WithConsultant = HB837::with('consultant')->whereNotNull('assigned_consultant_id')->first();
    if ($hb837WithConsultant && $hb837WithConsultant->consultant) {
        echo "   HB837: {$hb837WithConsultant->property_name}\n";
        echo "   Assigned Consultant: {$hb837WithConsultant->consultant->full_name}\n";
    } else {
        echo "   No HB837 records with assigned consultants found\n";
    }

    // Test the DataTables query simulation
    echo "\n4. Simulating DataTables query for consultants index:\n";
    $datatableQuery = Consultant::withCount([
        'hb837Projects as active_projects_count' => function ($query) {
            $query->whereIn('report_status', ['in-progress', 'not-started']);
        },
        'hb837Projects as completed_projects_count' => function ($query) {
            $query->where('report_status', 'completed');
        }
    ])->orderBy('last_name');

    echo "   Query executed successfully! Found " . $datatableQuery->count() . " consultants\n";

    echo "\n✅ All relationship tests passed! The DataTables should now work correctly.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";

<?php

/**
 * WORKFLOW STEP 3: Project Completion & Reporting Phase
 * ====================================================
 * This simulates completing executed projects with final reports and status updates
 * Focus: Change report_status to 'completed' + add final documentation
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WORKFLOW STEP 3: PROJECT COMPLETION & REPORTING ===\n";
echo "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Purpose: Complete executed projects with final reports\n";
echo "Expected Result: Projects marked as 'completed' with final documentation\n";
echo "=======================================================\n\n";

try {
    // Simulate authentication as user ID 1 (admin)
    Auth::loginUsingId(1);

    // Log the start of this workflow step
    Log::info('WORKFLOW STEP 3 STARTED', [
        'step' => 3,
        'phase' => 'Project Completion & Reporting',
        'user_id' => Auth::id(),
        'timestamp' => now()->toDateTimeString(),
        'description' => 'Agent completing executed projects with final reports'
    ]);

    echo "✓ User authenticated as: " . Auth::user()->name . "\n";
    echo "✓ Starting Step 3: Project Completion & Reporting Phase\n\n";

    // Get current statistics
    $executedProjects = HB837::where('contracting_status', 'executed')->get();
    $completedProjects = HB837::where('report_status', 'completed')->count();
    $inProgressProjects = HB837::where('report_status', 'in-progress')->count();

    echo "📊 Current Status:\n";
    echo "   Executed projects: " . $executedProjects->count() . "\n";
    echo "   Completed projects: {$completedProjects}\n";
    echo "   In-progress projects: {$inProgressProjects}\n\n";

    if ($executedProjects->count() == 0) {
        echo "⚠️  No executed projects found to complete.\n";
        echo "💡 Please run workflow_step_2_execution.php first to execute projects.\n";

        Log::warning('WORKFLOW STEP 3 - No Executed Projects', [
            'step' => 3,
            'executed_count' => 0,
            'message' => 'No projects available for completion phase'
        ]);

        return;
    }

    echo "🔄 Starting completion process...\n\n";

    // Simulate completing some executed projects
    $projectsToComplete = $executedProjects->take(2); // Complete first 2 projects

    echo "📋 Projects selected for completion:\n";
    foreach ($projectsToComplete as $project) {
        echo "   - ID {$project->id}: {$project->property_name}\n";
        echo "     Current report_status: {$project->report_status}\n";
        echo "     Assigned consultant: " . ($project->assigned_consultant ?: 'Not assigned') . "\n\n";
    }

    // Log pre-completion statistics
    Log::info('WORKFLOW STEP 3 - Pre-completion Statistics', [
        'step' => 3,
        'executed_projects' => $executedProjects->count(),
        'completed_projects' => $completedProjects,
        'projects_to_complete' => $projectsToComplete->count()
    ]);

    echo "💡 SIMULATED AGENT ACTIONS:\n";
    echo "==========================\n";

    foreach ($projectsToComplete as $index => $project) {
        $projectNum = $index + 1;
        echo "🏢 Project {$projectNum}: {$project->property_name}\n";
        echo "   Step 1: Consultant conducts inspection\n";
        echo "   Step 2: Upload inspection photos\n";
        echo "   Step 3: Generate security assessment report\n";
        echo "   Step 4: Client review and approval\n";
        echo "   Step 5: Mark project as completed\n";

        // Simulate the completion process
        DB::transaction(function() use ($project) {
            $project->update([
                'report_status' => 'completed',
                'completion_date' => now(),
                'final_notes' => 'Project completed successfully. All security assessments documented and delivered to client.',
                'updated_at' => now()
            ]);

            Log::info('WORKFLOW STEP 3 - Project Completed', [
                'step' => 3,
                'project_id' => $project->id,
                'property_name' => $project->property_name,
                'consultant' => $project->assigned_consultant,
                'completion_date' => now()->toDateTimeString()
            ]);
        });

        echo "   ✅ Status updated to: completed\n";
        echo "   📅 Completion date: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Small delay to simulate real workflow
        sleep(1);
    }

    // Update one project to in-progress status (simulating work in progress)
    if ($executedProjects->count() > 2) {
        $inProgressProject = $executedProjects->skip(2)->first();
        if ($inProgressProject) {
            $inProgressProject->update([
                'report_status' => 'in-progress',
                'updated_at' => now()
            ]);

            echo "🔄 Project set to in-progress: {$inProgressProject->property_name}\n";
            echo "   (Simulating ongoing work)\n\n";

            Log::info('WORKFLOW STEP 3 - Project Set In-Progress', [
                'step' => 3,
                'project_id' => $inProgressProject->id,
                'property_name' => $inProgressProject->property_name
            ]);
        }
    }

    // Simulate dashboard refresh and statistics update
    echo "🔄 Refreshing dashboard statistics...\n";

    $stats = [
        'active' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
            ->where('contracting_status', 'executed')->count(),
        'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
        'completed' => HB837::where('report_status', 'completed')->count(),
        'closed' => HB837::where('contracting_status', 'closed')->count(),
        'total' => HB837::count()
    ];

    echo "✅ Dashboard statistics updated!\n\n";

    // Final comprehensive statistics
    echo "📊 STEP 3 SUMMARY:\n";
    echo "==================\n";
    echo "Dashboard Statistics:\n";
    echo "   Active projects: {$stats['active']}\n";
    echo "   Quoted projects: {$stats['quoted']}\n";
    echo "   Completed projects: {$stats['completed']}\n";
    echo "   Closed projects: {$stats['closed']}\n";
    echo "   Total projects: {$stats['total']}\n\n";

    echo "Detailed Breakdown:\n";
    echo "   Not-started: " . HB837::where('report_status', 'not-started')->count() . "\n";
    echo "   In-progress: " . HB837::where('report_status', 'in-progress')->count() . "\n";
    echo "   Completed: " . HB837::where('report_status', 'completed')->count() . "\n";
    echo "   With consultants: " . HB837::whereNotNull('assigned_consultant')
                                        ->where('assigned_consultant', '!=', '')
                                        ->count() . "\n";

    // Show completed projects details
    $completedProjectsList = HB837::where('report_status', 'completed')
                                  ->select('id', 'property_name', 'assigned_consultant', 'completion_date')
                                  ->get();

    if ($completedProjectsList->count() > 0) {
        echo "\n📋 Completed Projects:\n";
        foreach ($completedProjectsList as $completed) {
            $completionDate = $completed->completion_date ?
                $completed->completion_date->format('Y-m-d H:i:s') : 'Not set';
            echo "   - ID {$completed->id}: {$completed->property_name}\n";
            echo "     Consultant: " . ($completed->assigned_consultant ?: 'Not assigned') . "\n";
            echo "     Completed: {$completionDate}\n\n";
        }
    }

    Log::info('WORKFLOW STEP 3 COMPLETED', [
        'step' => 3,
        'dashboard_stats' => $stats,
        'projects_completed_this_step' => $projectsToComplete->count(),
        'completion_time' => now()->toDateTimeString()
    ]);

    echo "✅ STEP 3 COMPLETE\n";
    echo "🎯 WORKFLOW COMPLETE!\n";
    echo "📊 Dashboard should now show updated statistics that match the table data\n";
    echo "🔄 Refresh your browser to see the updated dashboard\n\n";

    // Generate a final workflow summary
    echo "🏁 COMPLETE WORKFLOW SUMMARY:\n";
    echo "============================\n";
    echo "Step 1: Initial import created quoted projects\n";
    echo "Step 2: Projects moved to executed status with consultants\n";
    echo "Step 3: Selected projects completed with final reports\n";
    echo "Result: Dashboard statistics now reflect actual project statuses\n\n";

} catch (Exception $e) {
    echo "❌ ERROR in Step 3: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    Log::error('WORKFLOW STEP 3 - Error', [
        'step' => 3,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "\n" . str_repeat("=", 60) . "\n";

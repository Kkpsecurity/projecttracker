<?php

/**
 * WORKFLOW STEP 2: Contract Execution & Contact Details Phase
 * ==========================================================
 * This simulates updating quoted projects to executed status with full contact details
 * Focus: Change status to 'executed' + add consultant assignments + contact information
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WORKFLOW STEP 2: CONTRACT EXECUTION & CONTACT DETAILS ===\n";
echo "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Purpose: Update quoted projects to executed with full details\n";
echo "Expected Result: Projects updated to 'executed' contracting status\n";
echo "===============================================================\n\n";

try {
    // Simulate authentication as user ID 1 (admin)
    Auth::loginUsingId(1);

    // Log the start of this workflow step
    Log::info('WORKFLOW STEP 2 STARTED', [
        'step' => 2,
        'phase' => 'Contract Execution & Contact Details',
        'user_id' => Auth::id(),
        'timestamp' => now()->toDateTimeString(),
        'description' => 'Agent updating quoted projects to executed status with contact details'
    ]);

    echo "✓ User authenticated as: " . Auth::user()->name . "\n";
    echo "✓ Starting Step 2: Contract Execution & Contact Details Phase\n\n";

    // Get current statistics
    $quotedProjects = HB837::where('contracting_status', 'quoted')->get();
    $executedProjects = HB837::where('contracting_status', 'executed')->count();

    echo "📊 Current Status:\n";
    echo "   Quoted projects: " . $quotedProjects->count() . "\n";
    echo "   Executed projects: {$executedProjects}\n\n";

    if ($quotedProjects->count() == 0) {
        echo "⚠️  No quoted projects found to update.\n";
        echo "💡 Please run workflow_step_1_quotation.php first to create quoted projects.\n";

        Log::warning('WORKFLOW STEP 2 - No Quoted Projects', [
            'step' => 2,
            'quoted_count' => 0,
            'message' => 'No projects available for execution phase'
        ]);

        return;
    }

    // Check if sample file exists for execution phase
    $sampleFile = storage_path('app/temp/imports/TEST SHEET 02 - Executed & Contacts.xlsx');

    if (file_exists($sampleFile)) {
        echo "✓ Found execution phase file: " . basename($sampleFile) . "\n";

        // Log pre-execution statistics
        Log::info('WORKFLOW STEP 2 - Pre-execution Statistics', [
            'step' => 2,
            'quoted_projects' => $quotedProjects->count(),
            'executed_projects' => $executedProjects,
            'file_path' => $sampleFile,
            'file_size' => filesize($sampleFile)
        ]);

        echo "\n🔄 Processing execution phase import...\n";
        echo "   This will update existing projects with:\n";
        echo "   ✓ contracting_status: 'executed'\n";
        echo "   ✓ Consultant assignments\n";
        echo "   ✓ Contact information (property managers, regional managers)\n";
        echo "   ✓ Scheduled inspection dates\n";
        echo "   ✓ Additional financial details\n\n";

        echo "💡 AGENT ACTION REQUIRED:\n";
        echo "   1. Navigate to: /admin/hb837/smart-import\n";
        echo "   2. Upload file: TEST SHEET 02 - Executed & Contacts.xlsx\n";
        echo "   3. Review the mapping (should update existing records)\n";
        echo "   4. Confirm import to update projects to 'executed' status\n\n";

        // Show projects that should be updated
        echo "📋 Projects eligible for execution update:\n";
        foreach ($quotedProjects as $project) {
            echo "   - ID {$project->id}: {$project->property_name} (Current: {$project->contracting_status})\n";
        }
        echo "\n";

        // Monitor for status changes
        $attempts = 0;
        $maxAttempts = 12; // 1 minute monitoring

        echo "⏱️  Monitoring for execution updates (checking every 5 seconds)...\n";

        while ($attempts < $maxAttempts) {
            sleep(5);
            $currentExecuted = HB837::where('contracting_status', 'executed')->count();
            $currentQuoted = HB837::where('contracting_status', 'quoted')->count();

            if ($currentExecuted > $executedProjects) {
                $newExecuted = $currentExecuted - $executedProjects;
                echo "✅ SUCCESS! {$newExecuted} projects updated to executed status!\n";

                // Log successful execution update
                Log::info('WORKFLOW STEP 2 - Execution Update Successful', [
                    'step' => 2,
                    'executed_before' => $executedProjects,
                    'executed_after' => $currentExecuted,
                    'newly_executed' => $newExecuted,
                    'success' => true
                ]);

                // Show the updated projects
                $executedProjects = HB837::where('contracting_status', 'executed')
                                         ->select('id', 'property_name', 'contracting_status', 'assigned_consultant', 'scheduled_date_of_inspection')
                                         ->get();

                echo "\n📋 Projects now in executed status:\n";
                foreach ($executedProjects as $project) {
                    $consultant = $project->assigned_consultant ?: 'Not assigned';
                    $scheduleDate = $project->scheduled_date_of_inspection ?: 'Not scheduled';
                    echo "   - ID {$project->id}: {$project->property_name}\n";
                    echo "     Consultant: {$consultant}\n";
                    echo "     Scheduled: {$scheduleDate}\n\n";
                }

                break;
            }

            $attempts++;
            echo "   Attempt {$attempts}/{$maxAttempts} - Executed: {$currentExecuted}, Quoted: {$currentQuoted}\n";
        }

        if ($attempts >= $maxAttempts) {
            echo "⚠️  No status changes detected. Import may need to be done manually.\n";

            Log::warning('WORKFLOW STEP 2 - Update Timeout', [
                'step' => 2,
                'executed_count' => HB837::where('contracting_status', 'executed')->count(),
                'quoted_count' => HB837::where('contracting_status', 'quoted')->count(),
                'timeout_reached' => true
            ]);
        }

    } else {
        echo "❌ Execution phase file not found at: {$sampleFile}\n";
        echo "💡 Please ensure the test file exists or create it first.\n";

        Log::error('WORKFLOW STEP 2 - File Not Found', [
            'step' => 2,
            'expected_file' => $sampleFile,
            'file_exists' => false
        ]);
    }

    // Final statistics
    echo "\n📊 STEP 2 SUMMARY:\n";
    echo "==================\n";
    $finalTotal = HB837::count();
    $finalQuoted = HB837::where('contracting_status', 'quoted')->count();
    $finalExecuted = HB837::where('contracting_status', 'executed')->count();
    $withConsultants = HB837::whereNotNull('assigned_consultant')
                            ->where('assigned_consultant', '!=', '')
                            ->count();

    echo "Total projects: {$finalTotal}\n";
    echo "Quoted projects: {$finalQuoted}\n";
    echo "Executed projects: {$finalExecuted}\n";
    echo "With assigned consultants: {$withConsultants}\n";

    Log::info('WORKFLOW STEP 2 COMPLETED', [
        'step' => 2,
        'final_project_count' => $finalTotal,
        'quoted_projects' => $finalQuoted,
        'executed_projects' => $finalExecuted,
        'with_consultants' => $withConsultants,
        'completion_time' => now()->toDateTimeString()
    ]);

    echo "\n✅ STEP 2 COMPLETE\n";
    echo "📁 Next: Run workflow_step_3_completion.php\n";
    echo "🎯 Goal: Mark some projects as completed with final reports\n\n";

} catch (Exception $e) {
    echo "❌ ERROR in Step 2: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    Log::error('WORKFLOW STEP 2 - Error', [
        'step' => 2,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "\n" . str_repeat("=", 70) . "\n";

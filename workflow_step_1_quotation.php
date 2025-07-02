<?php

/**
 * WORKFLOW STEP 1: Initial Import & Quotation Phase
 * ================================================
 * This simulates an agent uploading the initial spreadsheet with quoted projects
 * Focus: Basic property info + quoted prices
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WORKFLOW STEP 1: INITIAL IMPORT & QUOTATION ===\n";
echo "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Purpose: Upload initial properties with quoted prices\n";
echo "Expected Result: Projects created with 'quoted' contracting status\n";
echo "====================================================\n\n";

try {
    // Simulate authentication as user ID 1 (admin)
    Auth::loginUsingId(1);

    // Log the start of this workflow step
    Log::info('WORKFLOW STEP 1 STARTED', [
        'step' => 1,
        'phase' => 'Initial Import & Quotation',
        'user_id' => Auth::id(),
        'timestamp' => now()->toDateTimeString(),
        'description' => 'Agent uploading initial property list with quoted prices'
    ]);

    echo "✓ User authenticated as: " . Auth::user()->name . "\n";
    echo "✓ Starting Step 1: Initial Import & Quotation Phase\n\n";

    // Check if sample file exists
    $sampleFile = storage_path('app/temp/imports/TEST SHEET 01 - Initial Import & Quotation.xlsx');

    if (file_exists($sampleFile)) {
        echo "✓ Found sample file: " . basename($sampleFile) . "\n";

        // Get current project count before import
        $beforeCount = \App\Models\HB837::count();
        echo "📊 Projects before import: {$beforeCount}\n";

        // Log pre-import statistics
        Log::info('WORKFLOW STEP 1 - Pre-import Statistics', [
            'step' => 1,
            'projects_before' => $beforeCount,
            'file_path' => $sampleFile,
            'file_size' => filesize($sampleFile)
        ]);

        echo "\n🔄 Processing import file...\n";
        echo "   This will create new projects with quoted prices\n";
        echo "   Expected contracting_status: 'quoted'\n";
        echo "   Expected report_status: 'not-started'\n\n";

        // Simulate the import process would happen here via the web interface
        echo "💡 AGENT ACTION REQUIRED:\n";
        echo "   1. Navigate to: /admin/hb837/smart-import\n";
        echo "   2. Upload file: TEST SHEET 01 - Initial Import & Quotation.xlsx\n";
        echo "   3. Review the mapping and confirm import\n";
        echo "   4. Verify projects are created with 'quoted' status\n\n";

        // Monitor for changes
        $attempts = 0;
        $maxAttempts = 10;

        echo "⏱️  Monitoring for import completion (checking every 5 seconds)...\n";

        while ($attempts < $maxAttempts) {
            sleep(5);
            $currentCount = \App\Models\HB837::count();

            if ($currentCount > $beforeCount) {
                $newRecords = $currentCount - $beforeCount;
                echo "✅ SUCCESS! {$newRecords} new projects imported!\n";

                // Log successful import
                Log::info('WORKFLOW STEP 1 - Import Successful', [
                    'step' => 1,
                    'projects_before' => $beforeCount,
                    'projects_after' => $currentCount,
                    'new_records' => $newRecords,
                    'success' => true
                ]);

                // Show the new projects
                $newProjects = \App\Models\HB837::latest()->take($newRecords)->get(['id', 'property_name', 'contracting_status', 'quoted_price']);
                echo "\n📋 Newly imported projects:\n";
                foreach ($newProjects as $project) {
                    echo "   - ID {$project->id}: {$project->property_name} (Status: {$project->contracting_status}, Price: $" . number_format((float)$project->quoted_price, 2) . ")\n";
                }

                break;
            }

            $attempts++;
            echo "   Attempt {$attempts}/{$maxAttempts} - Still {$beforeCount} projects (waiting for import...)\n";
        }

        if ($attempts >= $maxAttempts) {
            echo "⚠️  No changes detected after monitoring. Import may need to be done manually.\n";

            Log::warning('WORKFLOW STEP 1 - Import Timeout', [
                'step' => 1,
                'projects_count' => \App\Models\HB837::count(),
                'timeout_reached' => true
            ]);
        }

    } else {
        echo "❌ Sample file not found at: {$sampleFile}\n";
        echo "💡 Please ensure the test file exists or create it first.\n";

        Log::error('WORKFLOW STEP 1 - File Not Found', [
            'step' => 1,
            'expected_file' => $sampleFile,
            'file_exists' => false
        ]);
    }

    // Final statistics
    echo "\n📊 STEP 1 SUMMARY:\n";
    echo "==================\n";
    $finalCount = \App\Models\HB837::count();
    $quotedCount = \App\Models\HB837::where('contracting_status', 'quoted')->count();

    echo "Total projects: {$finalCount}\n";
    echo "Quoted projects: {$quotedCount}\n";

    Log::info('WORKFLOW STEP 1 COMPLETED', [
        'step' => 1,
        'final_project_count' => $finalCount,
        'quoted_projects' => $quotedCount,
        'completion_time' => now()->toDateTimeString()
    ]);

    echo "\n✅ STEP 1 COMPLETE\n";
    echo "📁 Next: Run workflow_step_2_execution.php\n";
    echo "🎯 Goal: Update projects to 'executed' status with full details\n\n";

} catch (Exception $e) {
    echo "❌ ERROR in Step 1: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    Log::error('WORKFLOW STEP 1 - Error', [
        'step' => 1,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "\n" . str_repeat("=", 60) . "\n";

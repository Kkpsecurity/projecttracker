<?php

/**
 * MASTER WORKFLOW RUNNER - Agent Document Upload Simulation
 * ========================================================
 * This orchestrates the complete 3-step workflow that mimics an agent
 * uploading and processing documents in the correct order
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 AGENT WORKFLOW SIMULATION - COMPLETE PROCESS\n";
echo "===============================================\n";
echo "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Purpose: Simulate complete agent document upload workflow\n";
echo "Process: Quotation → Execution → Completion\n";
echo "===============================================\n\n";

// Log the start of the complete workflow
Log::info('MASTER WORKFLOW STARTED', [
    'workflow' => 'Agent Document Upload Simulation',
    'timestamp' => now()->toDateTimeString(),
    'steps' => ['quotation', 'execution', 'completion']
]);

echo "📋 WORKFLOW OVERVIEW:\n";
echo "=====================\n";
echo "Step 1: Initial Import & Quotation\n";
echo "   - Upload initial property list\n";
echo "   - Create projects with 'quoted' status\n";
echo "   - Set basic property information\n\n";

echo "Step 2: Contract Execution & Contact Details\n";
echo "   - Update quoted projects to 'executed'\n";
echo "   - Add consultant assignments\n";
echo "   - Include contact information\n\n";

echo "Step 3: Project Completion & Reporting\n";
echo "   - Complete executed projects\n";
echo "   - Add final reports and documentation\n";
echo "   - Update dashboard statistics\n\n";

$choice = '';
while (!in_array($choice, ['1', '2', '3', 'all', 'status', 'logs', 'exit'])) {
    echo "🎛️  WORKFLOW CONTROL:\n";
    echo "====================\n";
    echo "1. Run Step 1 (Quotation Phase)\n";
    echo "2. Run Step 2 (Execution Phase)\n";
    echo "3. Run Step 3 (Completion Phase)\n";
    echo "all. Run All Steps Sequentially\n";
    echo "status. Check Current Status\n";
    echo "logs. View Recent Logs\n";
    echo "exit. Exit\n\n";

    echo "Enter your choice: ";
    $choice = trim(fgets(STDIN));
}

switch ($choice) {
    case '1':
        echo "\n🎯 Running Step 1: Quotation Phase\n";
        echo str_repeat("-", 50) . "\n";
        passthru('php workflow_step_1_quotation.php');
        break;

    case '2':
        echo "\n🎯 Running Step 2: Execution Phase\n";
        echo str_repeat("-", 50) . "\n";
        passthru('php workflow_step_2_execution.php');
        break;

    case '3':
        echo "\n🎯 Running Step 3: Completion Phase\n";
        echo str_repeat("-", 50) . "\n";
        passthru('php workflow_step_3_completion.php');
        break;

    case 'all':
        echo "\n🎯 Running Complete Workflow (All Steps)\n";
        echo str_repeat("=", 60) . "\n";

        echo "\n📌 STEP 1: QUOTATION PHASE\n";
        echo str_repeat("-", 30) . "\n";
        passthru('php workflow_step_1_quotation.php');

        echo "\n⏱️  Waiting 3 seconds before next step...\n";
        sleep(3);

        echo "\n📌 STEP 2: EXECUTION PHASE\n";
        echo str_repeat("-", 30) . "\n";
        passthru('php workflow_step_2_execution.php');

        echo "\n⏱️  Waiting 3 seconds before next step...\n";
        sleep(3);

        echo "\n📌 STEP 3: COMPLETION PHASE\n";
        echo str_repeat("-", 30) . "\n";
        passthru('php workflow_step_3_completion.php');

        echo "\n🏁 COMPLETE WORKFLOW FINISHED!\n";
        echo "🔄 Refresh your dashboard to see updated statistics\n";
        break;

    case 'status':
        echo "\n📊 CURRENT PROJECT STATUS\n";
        echo str_repeat("-", 30) . "\n";

        $stats = [
            'total' => \App\Models\HB837::count(),
            'quoted' => \App\Models\HB837::where('contracting_status', 'quoted')->count(),
            'executed' => \App\Models\HB837::where('contracting_status', 'executed')->count(),
            'not_started' => \App\Models\HB837::where('report_status', 'not-started')->count(),
            'in_progress' => \App\Models\HB837::where('report_status', 'in-progress')->count(),
            'completed' => \App\Models\HB837::where('report_status', 'completed')->count(),
        ];

        echo "Total Projects: {$stats['total']}\n\n";
        echo "By Contracting Status:\n";
        echo "  Quoted: {$stats['quoted']}\n";
        echo "  Executed: {$stats['executed']}\n\n";
        echo "By Report Status:\n";
        echo "  Not Started: {$stats['not_started']}\n";
        echo "  In Progress: {$stats['in_progress']}\n";
        echo "  Completed: {$stats['completed']}\n\n";

        // Dashboard calculations
        $dashboardStats = [
            'active' => \App\Models\HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => \App\Models\HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => \App\Models\HB837::where('report_status', 'completed')->count(),
            'closed' => \App\Models\HB837::where('contracting_status', 'closed')->count(),
        ];

        echo "Dashboard Card Values:\n";
        echo "  Active: {$dashboardStats['active']}\n";
        echo "  Quoted: {$dashboardStats['quoted']}\n";
        echo "  Completed: {$dashboardStats['completed']}\n";
        echo "  Closed: {$dashboardStats['closed']}\n";
        break;

    case 'logs':
        echo "\n📝 RECENT WORKFLOW LOGS\n";
        echo str_repeat("-", 30) . "\n";

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            echo "Showing last 20 workflow-related log entries:\n\n";
            $logs = file($logFile);
            $workflowLogs = array_filter($logs, function($line) {
                return strpos($line, 'WORKFLOW') !== false;
            });

            $recentLogs = array_slice($workflowLogs, -20);
            foreach ($recentLogs as $log) {
                echo trim($log) . "\n";
            }
        } else {
            echo "No log file found.\n";
        }
        break;

    case 'exit':
        echo "\n👋 Exiting workflow runner.\n";
        exit(0);

    default:
        echo "\n❌ Invalid choice. Please try again.\n";
}

echo "\n📚 HELPFUL INFORMATION:\n";
echo "========================\n";
echo "• Log file location: storage/logs/laravel.log\n";
echo "• Import files location: storage/app/temp/imports/\n";
echo "• Dashboard URL: /admin/hb837\n";
echo "• To run individual steps, use the specific workflow files\n";
echo "• All actions are logged with timestamps for monitoring\n\n";

Log::info('MASTER WORKFLOW SESSION COMPLETED', [
    'action' => $choice,
    'timestamp' => now()->toDateTimeString()
]);

echo "✅ Workflow session completed.\n";

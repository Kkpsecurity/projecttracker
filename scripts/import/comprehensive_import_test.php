<?php

require_once 'vendor/autoload.php';

use App\Imports\EnhancedHB837Import;
use App\Models\HB837;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

$app = require_once 'bootstrap/app.php';

echo "🧪 COMPREHENSIVE HB837 IMPORT TEST\n";
echo str_repeat('=', 50) . "\n";

// Set up authentication (use the first available user)
Auth::loginUsingId(20);
echo "✅ Authenticated as user ID: " . Auth::id() . "\n";

// Record initial state
$initialCount = HB837::count();
echo "📊 Initial HB837 count: $initialCount\n\n";

// Test Phase 1: Initial Import & Quotation
echo "🔄 PHASE 1: Testing Initial Import & Quotation\n";
echo str_repeat('-', 40) . "\n";

$testFile1 = 'docs/bugfixes_extracted/TEST SHEET 01 - Initial Import & Quotation.xlsx';
if (!file_exists($testFile1)) {
    echo "❌ Test file not found: $testFile1\n";
    exit(1);
}

try {
    $import1 = new EnhancedHB837Import();
    // Load and process the file
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($testFile1);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Extract headers and data
    $headers = [];
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    for ($col = 1; $col <= $highestColumnIndex; $col++) {
        $header = $worksheet->getCell([$col, 1])->getValue();
        if (!empty($header)) {
            $headers[] = trim($header);
        }
    }
    
    $rows = [];
    $highestRow = $worksheet->getHighestRow();
    for ($row = 2; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $rowData[] = $worksheet->getCell([$col, $row])->getValue();
        }
        $rows[] = $rowData;
    }
    
    $result1 = $import1->processImport($testFile1, $headers, $rows);
    
    echo "📈 PHASE 1 RESULTS:\n";
    echo "  - Imported: {$result1['imported']} new records\n";
    echo "  - Updated: {$result1['updated']} existing records\n";
    echo "  - Skipped: {$result1['skipped']} records\n";
    echo "  - Errors: " . count($result1['errors']) . "\n";
    
    if (!empty($result1['errors'])) {
        echo "  ⚠️ Errors found:\n";
        foreach (array_slice($result1['errors'], 0, 5) as $error) {
            echo "    - $error\n";
        }
    }
    
    // Check that report_status defaults were applied
    $notStartedCount = HB837::where('report_status', 'not-started')->count();
    echo "  ✅ Records with 'not-started' status: $notStartedCount\n";
    
    // Check a sample record
    $sampleRecord = HB837::where('property_name', 'TEST PROPERTY A')->first();
    if ($sampleRecord) {
        echo "  📋 Sample record 'TEST PROPERTY A':\n";
        echo "    - Report Status: {$sampleRecord->report_status}\n";
        echo "    - Contracting Status: {$sampleRecord->contracting_status}\n";
        echo "    - Property Type: {$sampleRecord->property_type}\n";
        echo "    - Units: {$sampleRecord->units}\n";
        echo "    - Quoted Price: {$sampleRecord->quoted_price}\n";
    }
    
} catch (Exception $e) {
    echo "❌ PHASE 1 FAILED: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";

// Test Phase 2: Executed & Contacts
echo "🔄 PHASE 2: Testing Executed & Contacts Updates\n";
echo str_repeat('-', 40) . "\n";

$testFile2 = 'docs/bugfixes_extracted/TEST SHEET 02 - Executed & Contacts - VERIFY.XLSX';
if (!file_exists($testFile2)) {
    echo "❌ Test file not found: $testFile2\n";
    exit(1);
}

try {
    $import2 = new EnhancedHB837Import();
    $result2 = $import2->import($testFile2);
    
    echo "📈 PHASE 2 RESULTS:\n";
    echo "  - Imported: {$result2['imported']} new records\n";
    echo "  - Updated: {$result2['updated']} existing records\n";
    echo "  - Skipped: {$result2['skipped']} records\n";
    echo "  - Errors: " . count($result2['errors']) . "\n";
    
    if (!empty($result2['errors'])) {
        echo "  ⚠️ Errors found:\n";
        foreach (array_slice($result2['errors'], 0, 5) as $error) {
            echo "    - $error\n";
        }
    }
    
    // Check contracting status updates
    $executedCount = HB837::where('contracting_status', 'executed')->count();
    echo "  ✅ Records with 'executed' contracting status: $executedCount\n";
    
    // Check updated sample record
    $sampleRecord = HB837::where('property_name', 'TEST PROPERTY A')->first();
    if ($sampleRecord) {
        echo "  📋 Updated sample record 'TEST PROPERTY A':\n";
        echo "    - Report Status: {$sampleRecord->report_status}\n";
        echo "    - Contracting Status: {$sampleRecord->contracting_status}\n";
        echo "    - Assigned Consultant ID: {$sampleRecord->assigned_consultant_id}\n";
        echo "    - Property Manager: {$sampleRecord->property_manager_name}\n";
        echo "    - Regional Manager: {$sampleRecord->regional_manager_name}\n";
        echo "    - Owner Name: {$sampleRecord->owner_name}\n";
        echo "    - SecurityGauge Risk: {$sampleRecord->securitygauge_crime_risk}\n";
    }
    
} catch (Exception $e) {
    echo "❌ PHASE 2 FAILED: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";

// Test Phase 3: Details Updated (INCLUDING REPORT STATUS!)
echo "🔄 PHASE 3: Testing Details Updated (REPORT STATUS!)\n";
echo str_repeat('-', 40) . "\n";

$testFile3 = 'docs/bugfixes_extracted/TEST SHEET 03 - Details Updated - VERIFY.XLSX';
if (!file_exists($testFile3)) {
    echo "❌ Test file not found: $testFile3\n";
    exit(1);
}

try {
    $import3 = new EnhancedHB837Import();
    $result3 = $import3->import($testFile3);
    
    echo "📈 PHASE 3 RESULTS:\n";
    echo "  - Imported: {$result3['imported']} new records\n";
    echo "  - Updated: {$result3['updated']} existing records\n";
    echo "  - Skipped: {$result3['skipped']} records\n";
    echo "  - Errors: " . count($result3['errors']) . "\n";
    
    if (!empty($result3['errors'])) {
        echo "  ⚠️ Errors found:\n";
        foreach (array_slice($result3['errors'], 0, 5) as $error) {
            echo "    - $error\n";
        }
    }
    
    // *** THIS IS THE CRITICAL TEST FOR REPORT STATUS ***
    $completedCount = HB837::where('report_status', 'completed')->count();
    echo "  🎯 CRITICAL: Records with 'completed' report status: $completedCount\n";
    
    // Check final sample record
    $sampleRecord = HB837::where('property_name', 'TEST PROPERTY A')->first();
    if ($sampleRecord) {
        echo "  📋 Final sample record 'TEST PROPERTY A':\n";
        echo "    - 🎯 Report Status: {$sampleRecord->report_status} (SHOULD BE 'completed')\n";
        echo "    - Contracting Status: {$sampleRecord->contracting_status}\n";
        echo "    - Assigned Consultant ID: {$sampleRecord->assigned_consultant_id}\n";
        echo "    - Report Submitted: {$sampleRecord->report_submitted}\n";
        echo "    - Billing Req Sent: {$sampleRecord->billing_req_sent}\n";
        echo "    - Project Net Profit: {$sampleRecord->project_net_profit}\n";
        
        // CRITICAL CHECK: Has report_status progressed correctly?
        if ($sampleRecord->report_status === 'completed') {
            echo "  ✅ SUCCESS: Report status correctly updated to 'completed'!\n";
        } else {
            echo "  ❌ FAILURE: Report status is '{$sampleRecord->report_status}', expected 'completed'!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ PHASE 3 FAILED: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";

// Final Summary
$finalCount = HB837::count();
$totalProcessed = $finalCount - $initialCount;

echo "🏁 FINAL SUMMARY\n";
echo str_repeat('=', 50) . "\n";
echo "📊 Initial count: $initialCount\n";
echo "📊 Final count: $finalCount\n";
echo "📊 Net change: $totalProcessed\n";

// Status breakdown
echo "\n📈 REPORT STATUS BREAKDOWN:\n";
$statusCounts = HB837::selectRaw('report_status, COUNT(*) as count')
    ->groupBy('report_status')
    ->get();

foreach ($statusCounts as $status) {
    echo "  - {$status->report_status}: {$status->count}\n";
}

echo "\n🎯 CRITICAL REPORT STATUS TESTS:\n";
$testPropertyA = HB837::where('property_name', 'TEST PROPERTY A')->first();
if ($testPropertyA && $testPropertyA->report_status === 'completed') {
    echo "  ✅ PASS: TEST PROPERTY A report_status progression (not-started → completed)\n";
} else {
    echo "  ❌ FAIL: TEST PROPERTY A report_status issue\n";
}

echo "\n🧪 TEST COMPLETE!\n";

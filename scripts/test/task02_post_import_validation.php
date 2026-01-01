<?php
/**
 * Task 02 Testing Script: Post-Import Validation
 * Validates the results after TEST SHEET 01 import
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  TASK 02: POST-IMPORT VALIDATION CHECK  \n";
echo "==========================================\n\n";

// Get baseline counts (before import)
$expectedTotal = isset($argv[1]) ? (int)$argv[1] : 3; // Pass previous count as argument
$expectedTestCount = isset($argv[2]) ? (int)$argv[2] : 2; // Pass previous test count as argument

// 1. Record Count Validation
echo "=== 1. RECORD COUNT VALIDATION ===\n";
$currentTotal = App\Models\HB837::count();
$currentTestCount = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')->count();

$expectedNewTotal = $expectedTotal + 352;
$expectedNewTestTotal = $expectedTestCount + 352;

echo "Current total records: {$currentTotal}\n";
echo "Expected total records: {$expectedNewTotal}\n";
echo "Records imported: " . ($currentTotal - $expectedTotal) . "\n\n";

echo "Current test property records: {$currentTestCount}\n";
echo "Expected test property records: {$expectedNewTestTotal}\n";
echo "Test properties imported: " . ($currentTestCount - $expectedTestCount) . "\n\n";

// Validation results
if ($currentTotal == $expectedNewTotal) {
    echo "✅ Total record count matches expected\n";
} else {
    echo "❌ Record count mismatch! Expected: {$expectedNewTotal}, Got: {$currentTotal}\n";
}

if ($currentTestCount == $expectedNewTestTotal) {
    echo "✅ Test property count matches expected\n";
} else {
    echo "❌ Test property count mismatch! Expected: {$expectedNewTestTotal}, Got: {$currentTestCount}\n";
}

// 2. New Records Analysis
echo "\n=== 2. NEW RECORDS ANALYSIS ===\n";
$newRecords = App\Models\HB837::where('id', '>', $expectedTotal)->orderBy('id')->get();

echo "New records found: " . $newRecords->count() . "\n\n";

if ($newRecords->count() > 0) {
    echo "Sample of new records:\n";
    foreach ($newRecords->take(5) as $record) {
        echo "ID {$record->id}: {$record->property_name}\n";
        echo "  ├─ Address: " . ($record->address ?? 'NULL') . "\n";
        echo "  ├─ Property Type: " . ($record->property_type ?? 'NULL') . "\n";
        echo "  ├─ Units: " . ($record->units ?? 'NULL') . "\n";
        echo "  ├─ Quoted Price: " . ($record->quoted_price ?? 'NULL') . "\n";
        echo "  ├─ Report Status: " . ($record->report_status ?? 'NULL') . "\n";
        echo "  └─ Contract Status: " . ($record->contracting_status ?? 'NULL') . "\n\n";
    }
    
    if ($newRecords->count() > 5) {
        echo "... and " . ($newRecords->count() - 5) . " more records\n\n";
    }
}

// 3. Field Validation
echo "=== 3. FIELD VALIDATION ===\n";
$validation = [
    'property_name' => App\Models\HB837::where('id', '>', $expectedTotal)->whereNull('property_name')->count(),
    'address' => App\Models\HB837::where('id', '>', $expectedTotal)->whereNull('address')->count(),
    'property_type' => App\Models\HB837::where('id', '>', $expectedTotal)->whereNull('property_type')->count(),
    'units' => App\Models\HB837::where('id', '>', $expectedTotal)->whereNull('units')->count(),
];

foreach ($validation as $field => $nullCount) {
    if ($nullCount == 0) {
        echo "✅ {$field}: All records have values\n";
    } else {
        echo "⚠️  {$field}: {$nullCount} records have NULL values\n";
    }
}

// 4. Default Status Validation
echo "\n=== 4. DEFAULT STATUS VALIDATION ===\n";
$statusCounts = App\Models\HB837::where('id', '>', $expectedTotal)
    ->selectRaw('report_status, contracting_status, COUNT(*) as count')
    ->groupBy(['report_status', 'contracting_status'])
    ->get();

echo "Status distribution for new records:\n";
foreach ($statusCounts as $status) {
    $report = $status->report_status ?? 'NULL';
    $contract = $status->contracting_status ?? 'NULL';
    echo "├─ Report: {$report}, Contract: {$contract} → {$status->count} records\n";
}

// 5. Data Quality Check
echo "\n=== 5. DATA QUALITY CHECK ===\n";

// Check for duplicate property names
$duplicates = App\Models\HB837::where('id', '>', $expectedTotal)
    ->selectRaw('property_name, COUNT(*) as count')
    ->groupBy('property_name')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() == 0) {
    echo "✅ No duplicate property names found\n";
} else {
    echo "⚠️  Duplicate property names found:\n";
    foreach ($duplicates as $dup) {
        echo "  ├─ {$dup->property_name}: {$dup->count} occurrences\n";
    }
}

// Check for reasonable numeric values
$invalidUnits = App\Models\HB837::where('id', '>', $expectedTotal)
    ->where(function($q) {
        $q->where('units', '<', 0)->orWhere('units', '>', 10000);
    })->count();

if ($invalidUnits == 0) {
    echo "✅ All unit counts appear reasonable\n";
} else {
    echo "⚠️  {$invalidUnits} records have unusual unit counts\n";
}

// 6. Summary
echo "\n=== 6. VALIDATION SUMMARY ===\n";
$totalIssues = 0;

if ($currentTotal != $expectedNewTotal) $totalIssues++;
if ($currentTestCount != $expectedNewTestTotal) $totalIssues++;
if ($validation['property_name'] > 0) $totalIssues++;
if ($validation['address'] > 0) $totalIssues++;
if ($duplicates->count() > 0) $totalIssues++;
if ($invalidUnits > 0) $totalIssues++;

if ($totalIssues == 0) {
    echo "🎉 IMPORT VALIDATION PASSED! All checks successful.\n";
    echo "✅ Ready to proceed to Task 03: Field Mapping Validation\n";
} else {
    echo "⚠️  {$totalIssues} issues found that require attention.\n";
    echo "❌ Review and fix issues before proceeding to Task 03.\n";
}

echo "\nNEXT STEP: Update Task 02 with these results and move to Task 03\n";
echo "==========================================\n";

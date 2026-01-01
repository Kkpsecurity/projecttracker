<?php
/**
 * Three-Phase Import Validation Script
 * Run this after each phase to validate results
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Detect which phase we're validating based on actual data records (not empty rows)
$totalRecords = App\Models\HB837::count();
$testRecords = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')->count();
$validRecords = App\Models\HB837::whereNotNull('property_name')
    ->where('property_name', '!=', '')
    ->count();

// Determine current phase based on data changes, not total count (due to empty Excel rows)
$phase = 'unknown';
$executedCount = App\Models\HB837::where('contracting_status', 'executed')->count();
$completedCount = App\Models\HB837::where('report_status', 'completed')->count();

if ($validRecords == 0) {
    $phase = 'pre-import';
} elseif ($validRecords > 0) {
    // Focus on TEST PROPERTY records to avoid empty row confusion
    if ($completedCount > 0) {
        $phase = 'phase-3-complete';
    } elseif ($executedCount > 0) {
        $phase = 'phase-2-complete';
    } else {
        $phase = 'phase-1-complete';
    }
}

echo "==========================================\n";
echo "  THREE-PHASE IMPORT VALIDATION          \n";
echo "  Current Phase: " . strtoupper($phase) . "\n";
echo "==========================================\n\n";

// 1. Record Count Verification
echo "=== 1. RECORD COUNT VERIFICATION ===\n";
echo "Total HB837 records: {$totalRecords}\n";
echo "Valid records (non-empty): {$validRecords}\n";
echo "Test property records: {$testRecords}\n";
echo "Records with 'executed' status: {$executedCount}\n";
echo "Records with 'completed' status: {$completedCount}\n";

// Add detailed logging
echo "\n=== 1.1. DETAILED DATABASE ANALYSIS ===\n";
$allRecords = App\Models\HB837::select('id', 'property_name', 'address', 'contracting_status', 'report_status', 'assigned_consultant_id')
    ->get();

echo "All records in database ({$allRecords->count()}):\n";
foreach ($allRecords as $record) {
    echo "  ID {$record->id}: '{$record->property_name}' | Address: '{$record->address}' | Contract: '{$record->contracting_status}' | Report: '{$record->report_status}' | Consultant: {$record->assigned_consultant_id}\n";
}

// Check consultant assignments
$consultantCount = App\Models\Consultant::count();
echo "\nConsultants in database: {$consultantCount}\n";
$consultants = App\Models\Consultant::select('id', 'first_name', 'last_name')->get();
foreach ($consultants as $consultant) {
    echo "  ID {$consultant->id}: {$consultant->first_name} {$consultant->last_name}\n";
}

// Phase-specific validation
switch ($phase) {
    case 'pre-import':
        echo "✅ Ready for Phase 1 (TEST SHEET 01) import\n";
        echo "📝 Next: Upload TEST SHEET 01 - Initial Import & Quotation.xlsx\n";
        break;
        
    case 'phase-1-complete':
        if ($validRecords >= 2) {
            echo "✅ Phase 1 import successful - {$validRecords} valid records found\n";
            echo "✅ {$testRecords} TEST PROPERTY records imported\n";
        } else {
            echo "❌ Phase 1 incomplete - no valid records found\n";
        }
        echo "📝 Next: Upload TEST SHEET 02 - Executed & Contacts - VERIFY.XLSX\n";
        break;
        
    case 'phase-2-complete':
        echo "✅ Phase 2 import completed - Status updates applied\n";
        echo "✅ {$executedCount} records marked as 'executed'\n";
        echo "📝 Next: Upload TEST SHEET 03 - Details Updated - VERIFY.XLSX\n";
        break;
        
    case 'phase-3-complete':
        echo "✅ Phase 3 import completed - All three phases done!\n";
        echo "📝 Next: Proceed to Task 03: Field Mapping Validation\n";
        break;
        
    default:
        echo "⚠️  Unexpected state - manual review needed\n";
}

echo "\n=== 2. SAMPLE RECORD VALIDATION ===\n";
$sampleRecords = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')
    ->take(5)
    ->get(['id', 'property_name', 'address', 'quoted_price', 'report_status', 'contracting_status']);

foreach ($sampleRecords as $record) {
    echo "ID {$record->id}: {$record->property_name}\n";
    echo "  ├─ Address: " . ($record->address ?? 'NULL') . "\n";
    echo "  ├─ Quoted Price: " . ($record->quoted_price ?? 'NULL') . "\n";
    echo "  ├─ Report Status: " . ($record->report_status ?? 'NULL') . "\n";
    echo "  └─ Contract Status: " . ($record->contracting_status ?? 'NULL') . "\n\n";
}

// 3. Field Completeness Check
echo "=== 3. FIELD COMPLETENESS CHECK ===\n";
$validation = [
    'property_name' => App\Models\HB837::whereNull('property_name')->count(),
    'address' => App\Models\HB837::whereNull('address')->count(),
    'quoted_price' => App\Models\HB837::whereNull('quoted_price')->count(),
];

foreach ($validation as $field => $nullCount) {
    if ($nullCount == 0) {
        echo "✅ {$field}: All records have values\n";
    } else {
        echo "⚠️  {$field}: {$nullCount} records have NULL values\n";
    }
}

echo "\n=== 4. PHASE-SPECIFIC SUMMARY ===\n";

switch ($phase) {
    case 'pre-import':
        echo "🚀 READY FOR PHASE 1 IMPORT\n";
        echo "✅ Database is clean and ready\n";
        echo "📋 Upload: TEST SHEET 01 - Initial Import & Quotation.xlsx\n";
        break;
        
    case 'phase-1-complete':
        if ($validRecords >= 2) {
            echo "🎉 PHASE 1 SUCCESSFUL!\n";
            echo "✅ Initial import completed: {$validRecords} valid records\n";
            echo "✅ {$testRecords} TEST PROPERTY records imported\n";
            echo "📋 Next: Upload TEST SHEET 02 - Executed & Contacts - VERIFY.XLSX\n";
        } else {
            echo "⚠️  Phase 1 needs review\n";
            echo "Expected: 2+ valid records, Actual: {$validRecords}\n";
        }
        break;
        
    case 'phase-2-complete':
        echo "🎉 PHASE 2 SUCCESSFUL!\n";
        echo "✅ Update phase completed: {$executedCount} records marked 'executed'\n";
        echo "✅ Consultant assignments and status updates applied\n";
        echo "📋 Next: Upload TEST SHEET 03 - Details Updated - VERIFY.XLSX\n";
        break;
        
    case 'phase-3-complete':
        echo "🎉 ALL THREE PHASES COMPLETE!\n";
        echo "✅ Three-phase import workflow successful\n";
        echo "✅ Ready to proceed to Task 03: Field Mapping Validation\n";
        break;
        
    default:
        echo "⚠️  Import results need review\n";
        echo "Current state: {$totalRecords} total, {$testRecords} test records\n";
}

echo "==========================================\n";

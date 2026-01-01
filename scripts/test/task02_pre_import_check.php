<?php
/**
 * Task 02 Testing Script: Pre-Import Environment Check
 * Validates the database state before running TEST SHEET 01 import
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  TASK 02: PRE-IMPORT ENVIRONMENT CHECK  \n";
echo "==========================================\n\n";

// 1. Current Database State
echo "=== 1. CURRENT DATABASE STATE ===\n";
$totalCount = App\Models\HB837::count();
$testCount = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')->count();

echo "Total HB837 records: {$totalCount}\n";
echo "Test property records: {$testCount}\n\n";

// 2. Existing Records Detail
echo "=== 2. EXISTING RECORDS ===\n";
$records = App\Models\HB837::select('id', 'property_name', 'report_status', 'contracting_status', 'created_at')
    ->orderBy('id')
    ->get();

foreach ($records as $record) {
    $reportStatus = $record->report_status ?? 'NULL';
    $contractStatus = $record->contracting_status ?? 'NULL';
    echo "ID {$record->id}: {$record->property_name}\n";
    echo "  ├─ Report Status: {$reportStatus}\n";
    echo "  ├─ Contract Status: {$contractStatus}\n";
    echo "  └─ Created: {$record->created_at}\n\n";
}

// 3. Test Sheet File Check
echo "=== 3. TEST SHEET FILE VERIFICATION ===\n";
$testSheet = 'docs/tasks/extracted_reports/TEST SHEET 01 - Initial Import & Quotation.xlsx';
if (file_exists($testSheet)) {
    $fileSize = round(filesize($testSheet) / 1024, 2);
    echo "✅ TEST SHEET 01 found: {$fileSize} KB\n";
} else {
    echo "❌ TEST SHEET 01 NOT FOUND: {$testSheet}\n";
}

// 4. Import Route Check
echo "\n=== 4. IMPORT INTERFACE CHECK ===\n";
try {
    $routes = app('router')->getRoutes();
    $importRouteFound = false;
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'hb837/smart-import') !== false) {
            echo "✅ Smart Import Route found: /{$route->uri()}\n";
            $importRouteFound = true;
            break;
        }
    }
    
    if (!$importRouteFound) {
        echo "⚠️  Smart Import Route not found - check manually\n";
    }
} catch (Exception $e) {
    echo "⚠️  Could not check routes: " . $e->getMessage() . "\n";
}

// 5. Field Mapping Config Check
echo "\n=== 5. FIELD MAPPING CONFIG CHECK ===\n";
$configFile = 'config/hb837_field_mapping.php';
if (file_exists($configFile)) {
    echo "✅ Field mapping config found\n";
    try {
        $config = require $configFile;
        $mappingCount = is_array($config) ? count($config) : 0;
        echo "✅ Field mappings available: {$mappingCount}\n";
    } catch (Exception $e) {
        echo "⚠️  Could not load config: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Field mapping config NOT FOUND: {$configFile}\n";
}

// 6. Database Schema Check
echo "\n=== 6. DATABASE SCHEMA VALIDATION ===\n";
try {
    $columns = Illuminate\Support\Facades\Schema::getColumnListing('hb837');
    $requiredFields = ['property_name', 'address', 'property_type', 'units', 'quoted_price', 'report_status', 'contracting_status'];
    
    echo "HB837 table columns found: " . count($columns) . "\n";
    
    foreach ($requiredFields as $field) {
        if (in_array($field, $columns)) {
            echo "✅ {$field}\n";
        } else {
            echo "❌ MISSING: {$field}\n";
        }
    }
} catch (Exception $e) {
    echo "⚠️  Could not check schema: " . $e->getMessage() . "\n";
}

// 7. Environment Summary
echo "\n=== 7. ENVIRONMENT SUMMARY ===\n";
echo "Ready for TEST SHEET 01 import:\n";
echo "├─ Current records will increase from {$totalCount} to " . ($totalCount + 352) . "\n";
echo "├─ Test properties will increase from {$testCount} to " . ($testCount + 352) . "\n";
echo "└─ Expected new record IDs: " . ($records->max('id') + 1) . " to " . ($records->max('id') + 352) . "\n\n";

echo "NEXT STEP: Navigate to /admin/hb837/smart-import to begin import\n";
echo "==========================================\n";

<?php
/**
 * Task 02 Simple Consultant Check
 * Quick analysis of consultant assignment issue
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  CONSULTANT ASSIGNMENT ISSUE ANALYSIS   \n";
echo "==========================================\n\n";

// 1. List all consultants
echo "=== 1. AVAILABLE CONSULTANTS ===\n";
try {
    $consultants = Illuminate\Support\Facades\DB::table('consultants')->get();
    foreach ($consultants as $consultant) {
        $name = $consultant->first_name . ' ' . $consultant->last_name;
        echo "ID {$consultant->id}: {$name} ({$consultant->email})\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// 2. Check current assignments
echo "=== 2. CURRENT CONSULTANT ASSIGNMENTS ===\n";
try {
    $records = App\Models\HB837::select('id', 'property_name', 'assigned_consultant_id')->get();
    
    foreach ($records as $record) {
        $consultantId = $record->assigned_consultant_id;
        $status = '';
        
        if (empty($consultantId) || $consultantId === '' || $consultantId === '0') {
            $status = '❌ UNASSIGNED';
        } elseif (is_numeric($consultantId)) {
            // Look up consultant name
            $consultant = Illuminate\Support\Facades\DB::table('consultants')->where('id', $consultantId)->first();
            if ($consultant) {
                $name = $consultant->first_name . ' ' . $consultant->last_name;
                $status = "✅ {$name} (ID: {$consultantId})";
            } else {
                $status = "❌ INVALID ID: {$consultantId}";
            }
        } else {
            $status = "⚠️  TEXT VALUE: '{$consultantId}'";
        }
        
        echo "ID {$record->id}: {$record->property_name} → {$status}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// 3. Check field mapping
echo "=== 3. FIELD MAPPING CONFIGURATION ===\n";
$configFile = 'config/hb837_field_mapping.php';
if (file_exists($configFile)) {
    $config = require $configFile;
    echo "Looking for consultant-related mappings:\n";
    
    foreach ($config as $csvField => $dbField) {
        $dbFieldDisplay = is_array($dbField) ? implode(', ', $dbField) : $dbField;
        if (stripos($csvField, 'consultant') !== false || stripos($dbFieldDisplay, 'consultant') !== false ||
            stripos($csvField, 'assigned') !== false || stripos($dbFieldDisplay, 'assigned') !== false) {
            echo "✅ '{$csvField}' → '{$dbFieldDisplay}'\n";
        }
    }
    echo "\n";
}

// 4. Test name lookups
echo "=== 4. TEST CONSULTANT NAME LOOKUPS ===\n";
$testNames = [
    'Craig Gundry',
    'craig gundry', 
    'Craig',
    'Gundry',
    'Michael Torres',
    'michael torres',
    'Jennifer Chen',
    'Robert Williams'
];

foreach ($testNames as $testName) {
    echo "Testing: '{$testName}'\n";
    
    // Exact match
    $exact = Illuminate\Support\Facades\DB::table('consultants')
        ->whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$testName])
        ->count();
    
    // Case insensitive match
    $caseInsensitive = Illuminate\Support\Facades\DB::table('consultants')
        ->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) = LOWER(?)", [$testName])
        ->count();
    
    // Partial match
    $partial = Illuminate\Support\Facades\DB::table('consultants')
        ->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE LOWER(?)", ["%{$testName}%"])
        ->count();
    
    echo "├─ Exact match: {$exact}\n";
    echo "├─ Case insensitive: {$caseInsensitive}\n";
    echo "└─ Partial match: {$partial}\n\n";
}

// 5. Recommendations
echo "=== 5. ISSUE SUMMARY & RECOMMENDATIONS ===\n";
echo "IDENTIFIED ISSUES:\n";
echo "├─ Empty string values in assigned_consultant_id field\n";
echo "├─ Need to verify consultant name format in import files\n";
echo "├─ Import process needs robust name matching logic\n";
echo "\nRECOMMENDED FIXES:\n";
echo "├─ 1. Clean up existing empty assigned_consultant_id values (set to NULL)\n";
echo "├─ 2. Implement case-insensitive name matching in import\n";
echo "├─ 3. Add fallback to partial name matching\n";
echo "├─ 4. Add import logging for failed consultant lookups\n";
echo "└─ 5. Test with actual consultant names from TEST SHEET files\n";

echo "\nNEXT STEP: Check TEST SHEET files for consultant name format\n";
echo "==========================================\n";

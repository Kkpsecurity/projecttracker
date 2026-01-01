<?php
/**
 * Task 02 CRITICAL ISSUE: Consultant Field Import Investigation
 * Focuses specifically on consultant field update behavior across multiple imports
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  CRITICAL ISSUE: CONSULTANT FIELD CHECK \n";
echo "==========================================\n\n";

// 1. Current Consultant Field Analysis
echo "=== 1. CURRENT CONSULTANT FIELD STATE ===\n";
$allRecords = App\Models\HB837::select('id', 'property_name', 'consultant', 'macro_consultant', 'created_at', 'updated_at')
    ->orderBy('id')
    ->get();

echo "Total records with consultant data:\n";
foreach ($allRecords as $record) {
    $consultant = $record->consultant ?? 'NULL';
    $macroConsultant = $record->macro_consultant ?? 'NULL';
    $isUpdated = $record->created_at != $record->updated_at ? '(UPDATED)' : '';
    
    echo "ID {$record->id}: {$record->property_name}\n";
    echo "  ├─ consultant: {$consultant}\n";
    echo "  ├─ macro_consultant: {$macroConsultant}\n";
    echo "  ├─ created: {$record->created_at}\n";
    echo "  └─ updated: {$record->updated_at} {$isUpdated}\n\n";
}

// 2. Consultant Field Schema Check
echo "=== 2. CONSULTANT FIELD SCHEMA ===\n";
try {
    $columns = Illuminate\Support\Facades\Schema::getColumnListing('hb837');
    $consultantFields = array_filter($columns, function($col) {
        return stripos($col, 'consultant') !== false;
    });
    
    echo "Consultant-related fields found:\n";
    foreach ($consultantFields as $field) {
        echo "✅ {$field}\n";
    }
    
    if (empty($consultantFields)) {
        echo "❌ No consultant fields found in schema!\n";
    }
} catch (Exception $e) {
    echo "❌ Could not check schema: " . $e->getMessage() . "\n";
}

// 3. Field Mapping Configuration for Consultant
echo "\n=== 3. CONSULTANT FIELD MAPPING CONFIG ===\n";
$configFile = 'config/hb837_field_mapping.php';
if (file_exists($configFile)) {
    $config = require $configFile;
    
    echo "Checking for consultant mappings in config:\n";
    $consultantMappings = [];
    
    foreach ($config as $csvField => $dbField) {
        if (stripos($csvField, 'consultant') !== false || stripos($dbField, 'consultant') !== false) {
            $consultantMappings[$csvField] = $dbField;
            echo "✅ '{$csvField}' → '{$dbField}'\n";
        }
    }
    
    if (empty($consultantMappings)) {
        echo "⚠️  No consultant field mappings found in config!\n";
        echo "Available mappings:\n";
        foreach ($config as $csvField => $dbField) {
            echo "  ├─ '{$csvField}' → '{$dbField}'\n";
        }
    }
} else {
    echo "❌ Field mapping config not found!\n";
}

// 4. Test Records Consultant Analysis
echo "\n=== 4. TEST RECORDS CONSULTANT PATTERN ===\n";
$testRecords = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')
    ->orderBy('id')
    ->get();

if ($testRecords->count() > 0) {
    echo "Analyzing consultant patterns in test records:\n";
    
    $consultantValues = [];
    foreach ($testRecords as $record) {
        $consultant = $record->consultant ?? 'NULL';
        $macroConsultant = $record->macro_consultant ?? 'NULL';
        
        if (!isset($consultantValues[$consultant])) {
            $consultantValues[$consultant] = 0;
        }
        $consultantValues[$consultant]++;
        
        echo "ID {$record->id}: {$record->property_name}\n";
        echo "  ├─ consultant: {$consultant}\n";
        echo "  └─ macro_consultant: {$macroConsultant}\n";
    }
    
    echo "\nConsultant value distribution:\n";
    foreach ($consultantValues as $value => $count) {
        echo "├─ '{$value}': {$count} records\n";
    }
} else {
    echo "No test records found for analysis\n";
}

// 5. Import History Simulation Check
echo "\n=== 5. IMPORT BEHAVIOR SIMULATION ===\n";
echo "Checking if records show signs of multiple import attempts:\n";

// Check for records with same property names (duplicates)
$duplicates = App\Models\HB837::selectRaw('property_name, COUNT(*) as count, GROUP_CONCAT(id) as ids')
    ->groupBy('property_name')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "⚠️  Duplicate property names found (indicates multiple imports):\n";
    foreach ($duplicates as $dup) {
        echo "├─ '{$dup->property_name}': {$dup->count} records (IDs: {$dup->ids})\n";
        
        // Get consultant values for these duplicates
        $dupRecords = App\Models\HB837::whereIn('id', explode(',', $dup->ids))->get();
        foreach ($dupRecords as $dupRecord) {
            $consultant = $dupRecord->consultant ?? 'NULL';
            echo "  └─ ID {$dupRecord->id}: consultant = '{$consultant}'\n";
        }
    }
} else {
    echo "✅ No duplicate property names found\n";
}

// 6. Craig Gundry Specific Check
echo "\n=== 6. CRAIG GUNDRY CONSULTANT CHECK ===\n";
$craigRecords = App\Models\HB837::where(function($query) {
    $query->where('consultant', 'LIKE', '%Craig%')
          ->orWhere('consultant', 'LIKE', '%Gundry%')
          ->orWhere('macro_consultant', 'LIKE', '%Craig%')
          ->orWhere('macro_consultant', 'LIKE', '%Gundry%');
})->get();

if ($craigRecords->count() > 0) {
    echo "Records with Craig Gundry found:\n";
    foreach ($craigRecords as $record) {
        echo "ID {$record->id}: {$record->property_name}\n";
        echo "  ├─ consultant: " . ($record->consultant ?? 'NULL') . "\n";
        echo "  └─ macro_consultant: " . ($record->macro_consultant ?? 'NULL') . "\n";
    }
} else {
    echo "⚠️  No records with Craig Gundry found\n";
    
    // Check what consultant values we do have
    $existingConsultants = App\Models\HB837::whereNotNull('consultant')
        ->where('consultant', '!=', '')
        ->distinct()
        ->pluck('consultant');
    
    echo "Existing consultant values:\n";
    foreach ($existingConsultants as $consultant) {
        echo "├─ '{$consultant}'\n";
    }
    
    $existingMacroConsultants = App\Models\HB837::whereNotNull('macro_consultant')
        ->where('macro_consultant', '!=', '')
        ->distinct()
        ->pluck('macro_consultant');
    
    echo "Existing macro_consultant values:\n";
    foreach ($existingMacroConsultants as $consultant) {
        echo "├─ '{$consultant}'\n";
    }
}

// 7. Recommendations
echo "\n=== 7. CONSULTANT ISSUE DIAGNOSIS ===\n";

if (empty($consultantMappings)) {
    echo "🔴 CRITICAL: No consultant field mappings configured!\n";
    echo "SOLUTION: Add consultant field mappings to config/hb837_field_mapping.php\n";
}

if ($craigRecords->count() == 0) {
    echo "🔴 ISSUE: Craig Gundry not found in any records\n";
    echo "SOLUTION: Check import data source and field mapping\n";
}

if ($duplicates->count() > 0) {
    echo "🟡 WARNING: Duplicate records indicate import issues\n";
    echo "SOLUTION: Implement proper upsert logic for subsequent imports\n";
}

echo "\nRECOMMENDED NEXT STEPS:\n";
echo "1. Check TEST SHEET 01 for Craig Gundry consultant data\n";
echo "2. Verify field mapping configuration includes consultant fields\n";
echo "3. Test import with consultant field focus\n";
echo "4. Implement upsert logic for subsequent imports\n";

echo "==========================================\n";

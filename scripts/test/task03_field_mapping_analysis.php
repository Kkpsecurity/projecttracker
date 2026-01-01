<?php
/**
 * Task 03: Field Mapping Analysis for TEST SHEET 01
 * Validates field mapping coverage and accuracy
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "==========================================\n";
echo "  TASK 03: FIELD MAPPING ANALYSIS\n";
echo "  TEST SHEET 01 Coverage Validation\n";
echo "==========================================\n";

// TEST SHEET 01 headers (from our previous analysis)
$testHeaders = [
    'Property Name',
    'Address', 
    'City',
    'County',
    'State',
    'Zip',
    'Property Type',
    'Units',
    'Management Company',
    'Macro Client',
    'Macro Contact', 
    'Macro Email',
    'Quoted Price'
];

echo "=== 1. FIELD MAPPING COVERAGE ANALYSIS ===\n";
echo "TEST SHEET 01 Headers: " . count($testHeaders) . "\n\n";

// Load field mapping from HB837Import using reflection
$import = new App\Imports\HB837Import();
$reflection = new ReflectionClass($import);
$fieldsProperty = $reflection->getProperty('fields');
$fieldsProperty->setAccessible(true);
$fieldMapping = $fieldsProperty->getValue($import);

$mapped = 0;
$unmapped = [];

foreach ($testHeaders as $header) {
    $found = false;
    echo "Testing: '$header'\n";
    
    foreach ($fieldMapping as $dbField => $possibleHeaders) {
        $candidates = is_array($possibleHeaders) ? $possibleHeaders : [$possibleHeaders];
        
        foreach ($candidates as $candidate) {
            if (strcasecmp($header, $candidate) === 0) {
                echo "  ✅ '$header' → '$dbField'\n";
                $found = true;
                $mapped++;
                break;
            }
        }
        if ($found) break;
    }
    
    if (!$found) {
        echo "  ❌ '$header' → UNMAPPED\n";
        $unmapped[] = $header;
    }
}

echo "\n=== COVERAGE SUMMARY ===\n";
echo "Total headers: " . count($testHeaders) . "\n";
echo "Mapped: $mapped\n";
echo "Unmapped: " . count($unmapped) . "\n";
echo "Coverage: " . round(($mapped / count($testHeaders)) * 100, 1) . "%\n";

if (!empty($unmapped)) {
    echo "\nUnmapped headers:\n";
    foreach ($unmapped as $header) {
        echo "  - $header\n";
    }
}

echo "\n=== 2. DATABASE FIELD VERIFICATION ===\n";
// Check if we have test data to validate against
$testRecord = App\Models\HB837::where('property_name', 'LIKE', 'TEST PROPERTY%')->first();

if ($testRecord) {
    echo "Found test record: {$testRecord->property_name}\n";
    echo "  - Address: {$testRecord->address}\n";
    echo "  - Property Type: {$testRecord->property_type}\n";
    echo "  - Units: {$testRecord->units}\n";
    echo "  - Quoted Price: " . ($testRecord->quoted_price ?? 'NULL') . "\n";
    echo "  - Macro Client: {$testRecord->macro_client}\n";
    echo "  - Macro Contact: {$testRecord->macro_contact}\n";
    echo "  - Management Company: {$testRecord->management_company}\n";
} else {
    echo "❌ No test records found. Please import TEST SHEET 01 first.\n";
}

echo "\n=== 3. CONFIGURATION VALIDATION ===\n";
// Check config file
$configPath = config_path('hb837_field_mapping.php');
if (file_exists($configPath)) {
    echo "✅ Configuration file exists: $configPath\n";
    $config = config('hb837_field_mapping.field_mapping', []);
    echo "Config mappings: " . count($config) . "\n";
} else {
    echo "❌ Configuration file not found: $configPath\n";
}

echo "\n=== 4. CRITICAL FIELD STATUS ===\n";
$criticalFields = [
    'Property Name' => 'property_name',
    'Address' => 'address', 
    'Quoted Price' => 'quoted_price',
    'Property Type' => 'property_type',
    'Units' => 'units'
];

foreach ($criticalFields as $excelField => $expectedDbField) {
    $mappingFound = false;
    foreach ($fieldMapping as $dbField => $possibleHeaders) {
        $candidates = is_array($possibleHeaders) ? $possibleHeaders : [$possibleHeaders];
        if (in_array($excelField, $candidates) && $dbField === $expectedDbField) {
            echo "✅ CRITICAL: '$excelField' → '$expectedDbField'\n";
            $mappingFound = true;
            break;
        }
    }
    if (!$mappingFound) {
        echo "❌ CRITICAL: '$excelField' → '$expectedDbField' NOT FOUND\n";
    }
}

echo "\n==========================================\n";
echo "Task 03 Field Mapping Analysis Complete\n";
echo "==========================================\n";

<?php
/**
 * Task 02 Test Sheet Analysis
 * Analyzes all three test sheets to understand data progression and expected changes
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Maatwebsite\Excel\Facades\Excel;

echo "==========================================\n";
echo "  TEST SHEET DATA ANALYSIS & COMPARISON  \n";
echo "==========================================\n\n";

// Define the test sheet files
$testSheets = [
    'sheet1' => [
        'file' => 'docs/tasks/extracted_reports/TEST SHEET 01 - Initial Import & Quotation.xlsx',
        'name' => 'TEST SHEET 01 - Initial Import & Quotation',
        'phase' => 'initial',
        'expected_records' => 352
    ],
    'sheet2' => [
        'file' => 'docs/tasks/extracted_reports/TEST SHEET 02 - Executed & Contacts - VERIFY.XLSX',
        'name' => 'TEST SHEET 02 - Executed & Contacts - VERIFY',
        'phase' => 'executed',
        'expected_records' => 376
    ],
    'sheet3' => [
        'file' => 'docs/tasks/extracted_reports/TEST SHEET 03 - Details Updated - VERIFY.XLSX',
        'name' => 'TEST SHEET 03 - Details Updated - VERIFY',
        'phase' => 'details_updated',
        'expected_records' => 372
    ]
];

$sheetData = [];
$commonProperties = [];

echo "=== 1. LOADING AND ANALYZING TEST SHEETS ===\n";

foreach ($testSheets as $sheetKey => $sheetInfo) {
    echo "Loading {$sheetInfo['name']}...\n";
    
    if (!file_exists($sheetInfo['file'])) {
        echo "❌ File not found: {$sheetInfo['file']}\n";
        continue;
    }
    
    try {
        $data = Excel::toArray(null, $sheetInfo['file'])[0];
        $headers = array_shift($data); // Remove header row
        
        echo "✅ Loaded successfully\n";
        echo "   ├─ Headers: " . count($headers) . " columns\n";
        echo "   ├─ Records: " . count($data) . " rows\n";
        echo "   └─ Expected: {$sheetInfo['expected_records']} records\n\n";
        
        // Store processed data
        $sheetData[$sheetKey] = [
            'info' => $sheetInfo,
            'headers' => $headers,
            'data' => $data,
            'records_by_property' => []
        ];
        
        // Index by property name for easy comparison
        foreach ($data as $row) {
            if (!empty($row[0])) { // Assuming first column is property name
                $propertyName = trim($row[0]);
                $record = array_combine($headers, $row);
                $sheetData[$sheetKey]['records_by_property'][$propertyName] = $record;
            }
        }
        
        echo "   Sample headers: " . implode(', ', array_slice($headers, 0, 5)) . "...\n\n";
        
    } catch (Exception $e) {
        echo "❌ Error loading sheet: " . $e->getMessage() . "\n\n";
    }
}

// Find common properties across all sheets
echo "=== 2. IDENTIFYING COMMON PROPERTIES ===\n";

if (count($sheetData) >= 2) {
    $sheet1Properties = array_keys($sheetData['sheet1']['records_by_property'] ?? []);
    $sheet2Properties = array_keys($sheetData['sheet2']['records_by_property'] ?? []);
    $sheet3Properties = array_keys($sheetData['sheet3']['records_by_property'] ?? []);
    
    // Find properties that exist in multiple sheets
    $sheet1And2 = array_intersect($sheet1Properties, $sheet2Properties);
    $sheet2And3 = array_intersect($sheet2Properties, $sheet3Properties);
    $allThree = array_intersect($sheet1Properties, $sheet2Properties, $sheet3Properties);
    
    echo "Properties in Sheet 1: " . count($sheet1Properties) . "\n";
    echo "Properties in Sheet 2: " . count($sheet2Properties) . "\n";
    echo "Properties in Sheet 3: " . count($sheet3Properties) . "\n";
    echo "Common to Sheet 1 & 2: " . count($sheet1And2) . "\n";
    echo "Common to Sheet 2 & 3: " . count($sheet2And3) . "\n";
    echo "Common to all three: " . count($allThree) . "\n\n";
    
    $commonProperties = $allThree;
    
    if (count($commonProperties) > 0) {
        echo "Sample common properties:\n";
        foreach (array_slice($commonProperties, 0, 10) as $property) {
            echo "├─ {$property}\n";
        }
        if (count($commonProperties) > 10) {
            echo "└─ ... and " . (count($commonProperties) - 10) . " more\n";
        }
    }
    echo "\n";
}

// Analyze field changes across sheets
echo "=== 3. FIELD CHANGE ANALYSIS ===\n";

if (count($commonProperties) > 0 && count($sheetData) >= 2) {
    // Take first 5 common properties for detailed analysis
    $sampleProperties = array_slice($commonProperties, 0, 5);
    
    foreach ($sampleProperties as $propertyName) {
        echo "Property: {$propertyName}\n";
        
        $changes = [];
        foreach (['sheet1', 'sheet2', 'sheet3'] as $sheetKey) {
            if (isset($sheetData[$sheetKey]['records_by_property'][$propertyName])) {
                $record = $sheetData[$sheetKey]['records_by_property'][$propertyName];
                $changes[$sheetKey] = $record;
            }
        }
        
        if (count($changes) >= 2) {
            $keys = array_keys($changes);
            
            // Compare key fields between sheets
            $fieldComparisons = [
                'Contracting Status' => [],
                'Report Status' => [],
                'Assigned Consultant' => [],
                'Address' => [],
                'Units' => [],
                'Quoted Price' => []
            ];
            
            foreach ($fieldComparisons as $fieldName => $values) {
                echo "├─ {$fieldName}:\n";
                foreach ($keys as $sheetKey) {
                    $value = $changes[$sheetKey][$fieldName] ?? 'N/A';
                    $sheetName = $sheetData[$sheetKey]['info']['phase'];
                    echo "│  ├─ {$sheetName}: {$value}\n";
                }
            }
        }
        echo "\n";
    }
}

// Create expected progression arrays
echo "=== 4. EXPECTED DATA PROGRESSION ARRAYS ===\n";

$expectedProgression = [];

if (count($commonProperties) > 0) {
    foreach ($commonProperties as $propertyName) {
        $progression = [
            'property_name' => $propertyName,
            'phases' => []
        ];
        
        foreach (['sheet1', 'sheet2', 'sheet3'] as $sheetKey) {
            if (isset($sheetData[$sheetKey]['records_by_property'][$propertyName])) {
                $record = $sheetData[$sheetKey]['records_by_property'][$propertyName];
                $phase = $sheetData[$sheetKey]['info']['phase'];
                
                $progression['phases'][$phase] = [
                    'contracting_status' => $record['Contracting Status'] ?? null,
                    'report_status' => $record['Report Status'] ?? null,
                    'assigned_consultant' => $record['Assigned Consultant'] ?? null,
                    'address' => $record['Address'] ?? null,
                    'units' => $record['Units'] ?? null,
                    'quoted_price' => $record['Quoted Price'] ?? null,
                    'macro_client' => $record['Macro Client'] ?? null,
                ];
            }
        }
        
        $expectedProgression[$propertyName] = $progression;
    }
    
    echo "Created progression data for " . count($expectedProgression) . " properties\n";
    
    // Show sample progression
    echo "\nSample progression data:\n";
    $sampleProperty = array_keys($expectedProgression)[0] ?? null;
    
    if ($sampleProperty) {
        echo "Property: {$sampleProperty}\n";
        foreach ($expectedProgression[$sampleProperty]['phases'] as $phase => $data) {
            echo "├─ Phase '{$phase}':\n";
            foreach ($data as $field => $value) {
                $displayValue = $value ?? 'NULL';
                echo "│  ├─ {$field}: {$displayValue}\n";
            }
        }
    }
}

// Save the analysis results
echo "\n=== 5. SAVING ANALYSIS RESULTS ===\n";

$analysisData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'sheet_info' => array_map(function($sheet) {
        return $sheet['info'];
    }, $sheetData),
    'common_properties' => $commonProperties,
    'expected_progression' => $expectedProgression,
    'field_mappings' => [
        'property_name' => 'Property Name',
        'address' => 'Address', 
        'contracting_status' => 'Contracting Status',
        'report_status' => 'Report Status',
        'assigned_consultant' => 'Assigned Consultant',
        'units' => 'Units',
        'quoted_price' => 'Quoted Price',
        'macro_client' => 'Macro Client'
    ]
];

$outputFile = 'scripts/test/task02_expected_data_progression.json';
file_put_contents($outputFile, json_encode($analysisData, JSON_PRETTY_PRINT));

echo "✅ Analysis saved to: {$outputFile}\n";
echo "✅ Common properties: " . count($commonProperties) . "\n";
echo "✅ Expected progressions: " . count($expectedProgression) . "\n";

// Create validation function
echo "\n=== 6. CREATING DATABASE COMPARISON FUNCTION ===\n";

$validationCode = '<?php
/**
 * Database vs Expected Data Comparison
 * Use this to validate import results against expected progression
 */

function validateImportProgression($phase = "initial") {
    $analysisFile = "scripts/test/task02_expected_data_progression.json";
    
    if (!file_exists($analysisFile)) {
        echo "❌ Analysis file not found. Run test sheet analysis first.\n";
        return false;
    }
    
    $expectedData = json_decode(file_get_contents($analysisFile), true);
    $commonProperties = $expectedData["common_properties"];
    
    echo "=== VALIDATING {$phase} PHASE IMPORT ===\n";
    echo "Checking " . count($commonProperties) . " common properties...\n\n";
    
    $matches = 0;
    $mismatches = 0;
    
    foreach (array_slice($commonProperties, 0, 10) as $propertyName) {
        $dbRecord = App\Models\HB837::where("property_name", $propertyName)->first();
        
        if (!$dbRecord) {
            echo "❌ {$propertyName} - Not found in database\n";
            $mismatches++;
            continue;
        }
        
        $expected = $expectedData["expected_progression"][$propertyName]["phases"][$phase] ?? null;
        
        if (!$expected) {
            echo "⚠️  {$propertyName} - No expected data for phase {$phase}\n";
            continue;
        }
        
        // Compare key fields
        $fieldMatches = true;
        $issues = [];
        
        if ($expected["contracting_status"] && $dbRecord->contracting_status !== $expected["contracting_status"]) {
            $issues[] = "contracting_status: expected \"{$expected["contracting_status"]}\", got \"{$dbRecord->contracting_status}\"";
            $fieldMatches = false;
        }
        
        if ($expected["report_status"] && $dbRecord->report_status !== $expected["report_status"]) {
            $issues[] = "report_status: expected \"{$expected["report_status"]}\", got \"{$dbRecord->report_status}\"";
            $fieldMatches = false;
        }
        
        if ($fieldMatches) {
            echo "✅ {$propertyName} - All fields match\n";
            $matches++;
        } else {
            echo "❌ {$propertyName} - Issues: " . implode(", ", $issues) . "\n";
            $mismatches++;
        }
    }
    
    echo "\n=== VALIDATION SUMMARY ===\n";
    echo "Matches: {$matches}\n";
    echo "Mismatches: {$mismatches}\n";
    
    return $mismatches === 0;
}';

file_put_contents('scripts/test/task02_database_validation_function.php', $validationCode);

echo "✅ Created database validation function\n";
echo "✅ Use: php -r 'require \"scripts/test/task02_database_validation_function.php\"; validateImportProgression(\"initial\");'\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Proceed with TEST SHEET 01 import\n";
echo "2. After import, run validation: php scripts/test/task02_database_validation_function.php\n";
echo "3. Compare results with expected progression data\n";
echo "4. Repeat for sheets 2 and 3\n";
echo "==========================================\n";

<?php
/**
 * Task 02 Database vs Expected Data Validator
 * Compares database records with expected progression from test sheets
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

function validateImportProgression($phase = 'initial') {
    $analysisFile = 'scripts/test/task02_expected_data_progression.json';
    
    if (!file_exists($analysisFile)) {
        echo "❌ Analysis file not found. Run test sheet analysis first.\n";
        return false;
    }
    
    $expectedData = json_decode(file_get_contents($analysisFile), true);
    $commonProperties = $expectedData['common_properties'];
    
    echo "==========================================\n";
    echo "  VALIDATING {$phase} PHASE IMPORT       \n";
    echo "==========================================\n\n";
    echo "Checking " . count($commonProperties) . " common properties...\n\n";
    
    $matches = 0;
    $mismatches = 0;
    $missing = 0;
    
    foreach ($commonProperties as $propertyName) {
        echo "Property: {$propertyName}\n";
        
        $dbRecord = App\Models\HB837::where('property_name', $propertyName)->first();
        
        if (!$dbRecord) {
            echo "❌ Not found in database\n\n";
            $missing++;
            continue;
        }
        
        $expected = $expectedData['expected_progression'][$propertyName]['phases'][$phase] ?? null;
        
        if (!$expected) {
            echo "⚠️  No expected data for phase {$phase}\n\n";
            continue;
        }
        
        // Compare key fields
        $fieldMatches = true;
        $fieldResults = [];
        
        // Check contracting status
        if ($expected['contracting_status'] !== null) {
            if ($dbRecord->contracting_status === $expected['contracting_status']) {
                $fieldResults[] = "✅ contracting_status: '{$dbRecord->contracting_status}'";
            } else {
                $fieldResults[] = "❌ contracting_status: expected '{$expected['contracting_status']}', got '{$dbRecord->contracting_status}'";
                $fieldMatches = false;
            }
        } else {
            $fieldResults[] = "ℹ️  contracting_status: '{$dbRecord->contracting_status}' (no expected value)";
        }
        
        // Check report status
        if ($expected['report_status'] !== null) {
            if ($dbRecord->report_status === $expected['report_status']) {
                $fieldResults[] = "✅ report_status: '{$dbRecord->report_status}'";
            } else {
                $fieldResults[] = "❌ report_status: expected '{$expected['report_status']}', got '{$dbRecord->report_status}'";
                $fieldMatches = false;
            }
        } else {
            $fieldResults[] = "ℹ️  report_status: '{$dbRecord->report_status}' (no expected value)";
        }
        
        // Check assigned consultant
        if ($expected['assigned_consultant'] !== null) {
            // Look up consultant name from ID
            $consultant = null;
            if ($dbRecord->assigned_consultant_id) {
                $consultant = Illuminate\Support\Facades\DB::table('consultants')
                    ->where('id', $dbRecord->assigned_consultant_id)
                    ->first();
            }
            
            $consultantName = $consultant ? ($consultant->first_name . ' ' . $consultant->last_name) : 'NULL';
            $expectedName = $expected['assigned_consultant'];
            
            // Check if consultant name matches (allowing for partial matches)
            if ($consultant && (
                stripos($consultantName, $expectedName) !== false || 
                stripos($expectedName, $consultant->first_name) !== false
            )) {
                $fieldResults[] = "✅ assigned_consultant: '{$consultantName}' (matches expected '{$expectedName}')";
            } else {
                $fieldResults[] = "❌ assigned_consultant: expected '{$expectedName}', got '{$consultantName}'";
                $fieldMatches = false;
            }
        } else {
            $consultantId = $dbRecord->assigned_consultant_id ?? 'NULL';
            $fieldResults[] = "ℹ️  assigned_consultant_id: '{$consultantId}' (no expected value)";
        }
        
        // Check address
        if ($expected['address'] !== null) {
            if ($dbRecord->address === $expected['address']) {
                $fieldResults[] = "✅ address: matches";
            } else {
                $fieldResults[] = "❌ address: expected '{$expected['address']}', got '{$dbRecord->address}'";
                $fieldMatches = false;
            }
        }
        
        // Check units
        if ($expected['units'] !== null) {
            if ($dbRecord->units == $expected['units']) {
                $fieldResults[] = "✅ units: {$dbRecord->units}";
            } else {
                $fieldResults[] = "❌ units: expected {$expected['units']}, got {$dbRecord->units}";
                $fieldMatches = false;
            }
        }
        
        // Check quoted price
        if ($expected['quoted_price'] !== null) {
            if ($dbRecord->quoted_price == $expected['quoted_price']) {
                $fieldResults[] = "✅ quoted_price: " . (string)$dbRecord->quoted_price;
            } else {
                $fieldResults[] = "❌ quoted_price: expected " . (string)$expected['quoted_price'] . ", got " . (string)$dbRecord->quoted_price;
                $fieldMatches = false;
            }
        }
        
        // Display results
        foreach ($fieldResults as $result) {
            echo "├─ {$result}\n";
        }
        
        if ($fieldMatches) {
            echo "└─ ✅ Overall: PASS\n\n";
            $matches++;
        } else {
            echo "└─ ❌ Overall: FAIL\n\n";
            $mismatches++;
        }
    }
    
    echo "=== VALIDATION SUMMARY ===\n";
    echo "Properties checked: " . count($commonProperties) . "\n";
    echo "✅ Matches: {$matches}\n";
    echo "❌ Mismatches: {$mismatches}\n";
    echo "⚠️  Missing: {$missing}\n";
    
    $successRate = count($commonProperties) > 0 ? round(($matches / count($commonProperties)) * 100, 1) : 0;
    echo "Success rate: {$successRate}%\n";
    
    if ($mismatches === 0 && $missing === 0) {
        echo "\n🎉 ALL VALIDATIONS PASSED!\n";
        return true;
    } else {
        echo "\n⚠️  Some validations failed - review issues above\n";
        return false;
    }
}

// Allow running from command line
if (isset($argv[1])) {
    $phase = $argv[1];
    validateImportProgression($phase);
} else {
    echo "Usage: php task02_database_validator.php [phase]\n";
    echo "Phases: initial, executed, details_updated\n";
}

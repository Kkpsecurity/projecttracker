<?php
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
}
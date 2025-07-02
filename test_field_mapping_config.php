<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\EnhancedHB837Import;

/**
 * Test the HB837 Field Mapping Configuration
 */

echo "=== HB837 Field Mapping Configuration Test ===\n\n";

try {
    // Test loading the configuration
    $fieldMapping = config('hb837_field_mapping.field_mapping');

    if (empty($fieldMapping)) {
        echo "❌ ERROR: Field mapping configuration is empty or not found!\n";
        exit(1);
    }

    echo "✅ Configuration loaded successfully!\n";
    echo "Total field mappings: " . count($fieldMapping) . "\n\n";

    // Show a summary of mapped fields
    echo "=== Field Mapping Summary ===\n";
    foreach ($fieldMapping as $dbField => $excelColumns) {
        echo sprintf("%-30s => %d possible Excel columns\n", $dbField, count($excelColumns));
    }

    // Test creating the import class
    echo "\n=== Testing Enhanced Import Class ===\n";
    try {
        $import = new EnhancedHB837Import();
        echo "✅ EnhancedHB837Import class instantiated successfully!\n";
    } catch (Exception $e) {
        echo "❌ ERROR creating import class: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Test configuration sections
    echo "\n=== Configuration Sections ===\n";

    $importRules = config('hb837_field_mapping.import_rules');
    echo "Import Rules: " . (empty($importRules) ? "❌ Missing" : "✅ Loaded") . "\n";

    $validationRules = config('hb837_field_mapping.validation_rules');
    echo "Validation Rules: " . (empty($validationRules) ? "❌ Missing" : "✅ Loaded") . "\n";

    $transformations = config('hb837_field_mapping.transformations');
    echo "Transformations: " . (empty($transformations) ? "❌ Missing" : "✅ Loaded") . "\n";

    $statusMaps = config('hb837_field_mapping.status_maps');
    echo "Status Maps: " . (empty($statusMaps) ? "❌ Missing" : "✅ Loaded") . "\n";

    // Show some examples
    echo "\n=== Example Mappings ===\n";
    $examples = ['property_name', 'address', 'report_status', 'quoted_price'];

    foreach ($examples as $field) {
        if (isset($fieldMapping[$field])) {
            echo "{$field}:\n";
            foreach ($fieldMapping[$field] as $excelCol) {
                echo "  - '{$excelCol}'\n";
            }
            echo "\n";
        }
    }

    echo "=== Test Completed Successfully! ===\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

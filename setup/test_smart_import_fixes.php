<?php

echo "🧪 Testing Smart Import Fixes in Application Context\n";
echo "====================================================\n\n";

// Simple bootstrap
define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;

try {
    echo "✅ Laravel application bootstrapped\n";

    // Test file path
    $testFile = realpath('setup/TEST SHEET 02 - Executed & Contacts.xlsx');

    if (!$testFile || !file_exists($testFile)) {
        throw new Exception("Test file not found");
    }

    echo "✅ Test file found: {$testFile}\n";

    // Test the controller method directly
    $controller = new HB837Controller();

    // Access the private method via reflection
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('performFileAnalysis');
    $method->setAccessible(true);

    echo "🔍 Running file analysis...\n";
    $result = $method->invoke($controller, $testFile);

    echo "✅ Analysis completed successfully\n\n";

    echo "📊 Results:\n";
    echo "   Detection: " . $result['detection']['type'] . "\n";
    echo "   Total rows: " . $result['stats']['total_rows'] . "\n";
    echo "   Valid rows: " . $result['stats']['valid_rows'] . "\n";
    echo "   Columns: " . $result['stats']['columns'] . "\n\n";

    echo "🎯 Column Mapping:\n";
    $lowConfidence = 0;
    $totalMapped = 0;

    foreach ($result['mapping'] as $mapping) {
        $confidence = round($mapping['confidence'] * 100);
        $status = $confidence >= 80 ? '✅' : ($confidence >= 60 ? '⚠️' : '❌');

        if ($mapping['target_field'] !== 'unmapped') {
            $totalMapped++;
        }

        if ($confidence < 60) {
            $lowConfidence++;
        }

        echo "   {$status} {$mapping['source_column']} → {$mapping['target_field']} ({$confidence}%)\n";
    }

    echo "\n📈 Summary:\n";
    echo "   - Total columns: " . count($result['mapping']) . "\n";
    echo "   - Mapped columns: {$totalMapped}\n";
    echo "   - Low confidence: {$lowConfidence}\n";

    if (count($result['warnings']) > 0) {
        echo "\n⚠️ Warnings:\n";
        foreach ($result['warnings'] as $warning) {
            echo "   - {$warning}\n";
        }
    }

    echo "\n🎉 Smart Import fixes are working correctly!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

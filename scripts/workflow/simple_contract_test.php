<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use App\Models\User;

echo "=== SIMPLE CONTRACTING STATUS TEST ===\n";

// Get first available user ID
$user = User::first();
if (!$user) {
    echo "No users found in database.\n";
    exit;
}

echo "Using user ID: {$user->id} ({$user->name})\n";

// Find an existing record to test with
$existing = HB837::where('contracting_status', 'quoted')->first();

if (!$existing) {
    echo "No existing 'quoted' records found to test with.\n";
    exit;
}

echo "Testing with existing record ID: {$existing->id}\n";
echo "Property: {$existing->property_name}\n";
echo "Current contracting_status: '{$existing->contracting_status}'\n";

// Test the actual import logic
echo "\n=== TESTING IMPORT FIELD MAPPING ===\n";

$config = config('hb837_field_mapping.field_mapping.contracting_status', []);
echo "Field mapping for contracting_status:\n";
print_r($config);

// Test header mapping
$headers = ['Property Name', 'Contracting Status'];
$import = new App\Imports\EnhancedHB837Import();

// Use reflection to test the createHeaderMapping method
$reflection = new ReflectionClass($import);
$method = $reflection->getMethod('createHeaderMapping');
$method->setAccessible(true);

echo "\nTesting header mapping:\n";
$headerMap = $method->invoke($import, $headers);
print_r($headerMap);

if (isset($headerMap['contracting_status'])) {
    echo "✅ SUCCESS: 'Contracting Status' header maps to contracting_status field\n";
} else {
    echo "❌ FAILED: 'Contracting Status' header does not map properly\n";
}

echo "\n=== TESTING STATUS NORMALIZATION ===\n";
$normalizeMethod = $reflection->getMethod('normalizeContractingStatus');
$normalizeMethod->setAccessible(true);

$testValue = 'executed';
$normalized = $normalizeMethod->invoke($import, $testValue);
echo "normalizeContractingStatus('$testValue') = '$normalized'\n";

if ($normalized === 'executed') {
    echo "✅ SUCCESS: Status normalization works correctly\n";
} else {
    echo "❌ FAILED: Status normalization failed\n";
}

echo "\n=== MANUAL UPDATE TEST ===\n";
// Manually update the record to verify database accepts the value
try {
    $existing->update(['contracting_status' => 'executed']);
    echo "✅ SUCCESS: Manual database update to 'executed' worked\n";
    
    // Revert back
    $existing->update(['contracting_status' => 'quoted']);
    echo "✅ SUCCESS: Manual revert to 'quoted' worked\n";
    
} catch (Exception $e) {
    echo "❌ FAILED: Manual database update failed: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";

<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\EnhancedHB837Import;
use App\Models\HB837;

echo "=== LIVE IMPORT TEST ===\n";

// Find a real record to test with
$testRecord = HB837::where('contracting_status', 'quoted')->first();

if (!$testRecord) {
    echo "No quoted records found to test with.\n";
    exit;
}

echo "Testing with record: {$testRecord->property_name}\n";
echo "Current contracting_status: '{$testRecord->contracting_status}'\n";

// Save original value for restoration
$originalStatus = $testRecord->contracting_status;

// Simulate import data that should update the contracting status
$import = new EnhancedHB837Import();

// Mock the exact data structure that would come from Excel
$headers = ['Property Name', 'Contracting Status'];
$rows = [
    [$testRecord->property_name, 'executed']
];

echo "\nSimulating import with data:\n";
echo "Property Name: '{$testRecord->property_name}'\n";
echo "Contracting Status: 'executed'\n";

try {
    // Process the import
    $import->processImport('test_file.xlsx', $headers, $rows);
    
    echo "\nImport Results:\n";
    echo "Imported: {$import->importedCount}\n";
    echo "Updated: {$import->updatedCount}\n";
    echo "Skipped: {$import->skippedCount}\n";
    
    if (!empty($import->fieldChanges)) {
        echo "\nField Changes:\n";
        foreach ($import->fieldChanges as $recordId => $changes) {
            echo "Record $recordId:\n";
            foreach ($changes as $field => $change) {
                echo "  $field: '{$change['old']}' -> '{$change['new']}' ({$change['type']})\n";
            }
        }
    }
    
    if (!empty($import->errors)) {
        echo "\nErrors:\n";
        foreach ($import->errors as $error) {
            echo "  - $error\n";
        }
    }
    
} catch (Exception $e) {
    echo "Import failed: " . $e->getMessage() . "\n";
}

// Check the final state
$updated = HB837::find($testRecord->id);
echo "\nFinal State:\n";
echo "contracting_status: '{$updated->contracting_status}'\n";

if ($updated->contracting_status === 'executed') {
    echo "✅ SUCCESS: Contracting status import worked!\n";
} else {
    echo "❌ FAILED: Expected 'executed', got '{$updated->contracting_status}'\n";
}

// Restore original value
echo "\nRestoring original value...\n";
$updated->update(['contracting_status' => $originalStatus]);
echo "Restored to: '{$originalStatus}'\n";

echo "\n=== TEST COMPLETE ===\n";

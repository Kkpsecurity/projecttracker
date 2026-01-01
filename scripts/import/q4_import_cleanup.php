<?php
/**
 * Q4 Import Cleanup Script
 * ========================
 * 
 * This script cleans up test data and restores original values.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;

echo "=== Q4 IMPORT TEST CLEANUP ===\n\n";

// Check for backup file
if (!file_exists('q4_test_backup.json')) {
    echo "⚠️  No backup file found (q4_test_backup.json)\n";
    echo "Checking for any remaining test data to clean up...\n\n";
    
    $test_records = HB837::where(function($query) {
        $query->where('property_manager_name', 'LIKE', '%Q4_TEST_%')
              ->orWhere('regional_manager_name', 'LIKE', '%Q4_TEST_%')
              ->orWhere('phone', 'LIKE', '%555-TEST-%')
              ->orWhere('consultant_notes', 'LIKE', '%Q4_TEST_NOTES_%');
    })->get();
    
    if ($test_records->count() > 0) {
        echo "Found {$test_records->count()} records with test data:\n";
        foreach ($test_records as $record) {
            echo "⚠️  {$record->property_name} (ID: {$record->id}) - Manual cleanup needed\n";
        }
    } else {
        echo "✅ No test data found - cleanup not needed\n";
    }
    return;
}

// Load backup data
$backup_data = json_decode(file_get_contents('q4_test_backup.json'), true);

echo "Restoring " . count($backup_data) . " records from backup...\n\n";

$restored_count = 0;

foreach ($backup_data as $record_id => $original_data) {
    $record = HB837::find($record_id);
    
    if ($record) {
        echo "🔄 Restoring: {$record->property_name} (ID: {$record_id})\n";
        
        // Show what's being restored
        foreach ($original_data as $field => $value) {
            $current_value = $record->{$field};
            if ($current_value !== $value) {
                echo "   {$field}: '{$current_value}' → '" . ($value ?: 'NULL') . "'\n";
            }
        }
        
        $record->update($original_data);
        $restored_count++;
        
        echo "   ✅ Restored successfully\n\n";
    } else {
        echo "❌ Record ID {$record_id} not found\n\n";
    }
}

// Clean up files
unlink('q4_test_backup.json');
if (file_exists('pre_import_stats.json')) {
    unlink('pre_import_stats.json');
}

echo "✅ Q4 Import Test Cleanup Complete!\n";
echo "   - Restored {$restored_count} records to original state\n";
echo "   - Removed backup and statistics files\n";
echo "\nTest environment reset.\n";
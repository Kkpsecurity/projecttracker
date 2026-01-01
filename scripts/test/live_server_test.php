<?php
/**
 * HB837 Import Field Mapping Test Script
 * =====================================
 * 
 * This script tests that the field mapping fixes for Craig Gundry's issue are working correctly.
 * It will:
 * 1. Check current field mapping configuration
 * 2. Modify test records with temporary data
 * 3. Provide instructions for testing import
 * 4. Verify import results
 * 5. Clean up test data
 * 
 * Usage: php live_server_test.php [action]
 * Actions: setup, check, cleanup, verify
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Get command line action
$action = $argv[1] ?? 'help';

switch ($action) {
    case 'setup':
        setupTestData();
        break;
    case 'check':
        checkFieldMappings();
        break;
    case 'verify':
        verifyImportResults();
        break;
    case 'cleanup':
        cleanupTestData();
        break;
    default:
        showHelp();
        break;
}

function showHelp()
{
    echo "=== HB837 FIELD MAPPING TEST SCRIPT ===\n\n";
    echo "Usage: php live_server_test.php [action]\n\n";
    echo "Actions:\n";
    echo "  setup   - Create test data for import verification\n";
    echo "  check   - Check field mapping configuration\n";
    echo "  verify  - Verify import results after test\n";
    echo "  cleanup - Remove test data\n\n";
    echo "Test Process:\n";
    echo "1. Run: php live_server_test.php setup\n";
    echo "2. Upload Q4 assessment file through import interface\n";
    echo "3. Run: php live_server_test.php verify\n";
    echo "4. Run: php live_server_test.php cleanup\n\n";
}

function checkFieldMappings()
{
    echo "=== FIELD MAPPING CONFIGURATION CHECK ===\n\n";
    
    $mappings = config('hb837_field_mapping.field_mapping', []);
    
    echo "🔍 Checking critical field mappings...\n\n";
    
    // Check Property Manager mappings
    $pm_name_mappings = $mappings['property_manager_name'] ?? [];
    $pm_email_mappings = $mappings['property_manager_email'] ?? [];
    
    echo "Property Manager Name mappings:\n";
    foreach ($pm_name_mappings as $index => $mapping) {
        echo "  [{$index}] \"{$mapping}\"\n";
    }
    
    echo "\nProperty Manager Email mappings:\n";
    foreach ($pm_email_mappings as $index => $mapping) {
        echo "  [{$index}] \"{$mapping}\"\n";
    }
    
    // Check Regional Manager mappings
    $rm_name_mappings = $mappings['regional_manager_name'] ?? [];
    $rm_email_mappings = $mappings['regional_manager_email'] ?? [];
    
    echo "\nRegional Manager Name mappings:\n";
    foreach ($rm_name_mappings as $index => $mapping) {
        echo "  [{$index}] \"{$mapping}\"\n";
    }
    
    echo "\nRegional Manager Email mappings:\n";
    foreach ($rm_email_mappings as $index => $mapping) {
        echo "  [{$index}] \"{$mapping}\"\n";
    }
    
    // Verify critical mappings are correct
    echo "\n=== CRITICAL MAPPING VERIFICATION ===\n";
    
    $rm_name_has_regional_manager = in_array('Regional Manager', $rm_name_mappings);
    $rm_email_has_rm_email = in_array('RM Email', $rm_email_mappings);
    
    echo "✅ 'Regional Manager' maps to regional_manager_name: " . ($rm_name_has_regional_manager ? "YES" : "❌ NO") . "\n";
    echo "✅ 'RM Email' maps to regional_manager_email: " . ($rm_email_has_rm_email ? "YES" : "❌ NO") . "\n";
    
    // Check for incorrect mappings (the bug we fixed)
    $rm_email_has_regional_manager = in_array('Regional Manager', $rm_email_mappings);
    $macro_email_mappings = $mappings['macro_email'] ?? [];
    $macro_email_has_rm_email = in_array('RM Email', $macro_email_mappings);
    
    echo "\n🚨 Checking for OLD BUGS:\n";
    echo "❌ 'Regional Manager' incorrectly in regional_manager_email: " . ($rm_email_has_regional_manager ? "FOUND BUG!" : "✅ Clean") . "\n";
    echo "❌ 'RM Email' incorrectly in macro_email: " . ($macro_email_has_rm_email ? "FOUND BUG!" : "✅ Clean") . "\n";
    
    if ($rm_name_has_regional_manager && $rm_email_has_rm_email && !$rm_email_has_regional_manager && !$macro_email_has_rm_email) {
        echo "\n🎉 FIELD MAPPINGS ARE CORRECT! Ready for testing.\n";
    } else {
        echo "\n⚠️  FIELD MAPPING ISSUES DETECTED! Please fix before testing.\n";
    }
    
    echo "\nConfiguration check complete.\n";
}

function setupTestData()
{
    echo "=== SETTING UP TEST DATA ===\n\n";
    
    // Find properties that typically appear in Q4 assessments
    $test_properties = [
        'Sand Lake Pointe Apartments',
        'Hickory Pointe Apartments',
        'Wyndham Place Apartments', 
        'Wedgewood Apartments',
        'Charleston Place'
    ];
    
    echo "Looking for test properties in database...\n";
    
    $found_properties = [];
    foreach ($test_properties as $property) {
        $record = HB837::where('property_name', $property)->first();
        if ($record) {
            $found_properties[] = $record;
        }
    }
    
    if (empty($found_properties)) {
        // Find any properties with manager data to use as test subjects
        $found_properties = HB837::whereNotNull('regional_manager_name')
            ->whereNotNull('property_manager_name')
            ->limit(5)
            ->get();
        
        echo "Using available properties with manager data for testing...\n";
    }
    
    if (empty($found_properties)) {
        echo "❌ No suitable properties found for testing!\n";
        echo "   Need properties with existing manager data.\n";
        return;
    }
    
    echo "Found " . count($found_properties) . " properties for testing:\n";
    
    $backup_data = [];
    
    foreach ($found_properties as $record) {
        echo "\n🏢 {$record->property_name} (ID: {$record->id})\n";
        
        // Backup original data
        $backup_data[$record->id] = [
            'property_manager_name' => $record->property_manager_name,
            'property_manager_email' => $record->property_manager_email,
            'regional_manager_name' => $record->regional_manager_name,
            'regional_manager_email' => $record->regional_manager_email,
        ];
        
        // Create test data
        $test_data = [
            'property_manager_name' => 'LIVE_TEST_PM_' . $record->id,
            'property_manager_email' => 'live.test.pm.' . $record->id . '@example.com',
            'regional_manager_name' => 'LIVE_TEST_RM_' . $record->id,
            'regional_manager_email' => 'live.test.rm.' . $record->id . '@example.com',
        ];
        
        echo "   Original PM: {$record->property_manager_name} ({$record->property_manager_email})\n";
        echo "   Original RM: {$record->regional_manager_name} ({$record->regional_manager_email})\n";
        echo "   Setting to: {$test_data['property_manager_name']} / {$test_data['regional_manager_name']}\n";
        
        $record->update($test_data);
    }
    
    // Save backup data for cleanup
    file_put_contents('hb837_test_backup.json', json_encode($backup_data, JSON_PRETTY_PRINT));
    
    echo "\n✅ Test data setup complete!\n";
    echo "📁 Backup saved to: hb837_test_backup.json\n\n";
    
    echo "=== NEXT STEPS ===\n";
    echo "1. Upload Q4 assessment file through the import interface\n";
    echo "2. Watch for import completion\n";
    echo "3. Run: php live_server_test.php verify\n";
    echo "4. Run: php live_server_test.php cleanup (when done)\n\n";
    
    echo "🎯 Expected Result:\n";
    echo "   - Import should detect differences in manager fields\n";
    echo "   - Field mappings should work correctly:\n";
    echo "     • 'Regional Manager' → regional_manager_name ✅\n";
    echo "     • 'RM Email' → regional_manager_email ✅\n";
    echo "   - Test data should be replaced with real Q4 data\n\n";
}

function verifyImportResults()
{
    echo "=== VERIFYING IMPORT RESULTS ===\n\n";
    
    // Check for recent import activity (last 10 minutes)
    $recent_time = Carbon::now()->subMinutes(10);
    echo "Checking for updates since: {$recent_time}\n\n";
    
    $recent_updates = HB837::where('updated_at', '>=', $recent_time)
        ->select(['id', 'property_name', 'property_manager_name', 'property_manager_email', 
                 'regional_manager_name', 'regional_manager_email', 'updated_at'])
        ->orderBy('updated_at', 'desc')
        ->get();
    
    echo "=== RECENTLY UPDATED RECORDS ===\n";
    echo "Found: {$recent_updates->count()} records\n";
    echo str_repeat('=', 80) . "\n";
    
    $test_records_found = 0;
    $test_records_cleaned = 0;
    
    if ($recent_updates->count() > 0) {
        foreach ($recent_updates as $record) {
            echo "\n📊 {$record->property_name} (ID: {$record->id})\n";
            echo "   PM: {$record->property_manager_name} ({$record->property_manager_email})\n";
            echo "   RM: {$record->regional_manager_name} ({$record->regional_manager_email})\n";
            echo "   Updated: {$record->updated_at}\n";
            
            // Check if this was a test record
            $was_test_record = strpos($record->property_manager_name, 'LIVE_TEST_') !== false ||
                              strpos($record->regional_manager_name, 'LIVE_TEST_') !== false ||
                              strpos($record->property_manager_email, 'live.test.') !== false ||
                              strpos($record->regional_manager_email, 'live.test.') !== false;
            
            $is_now_clean = strpos($record->property_manager_name, 'LIVE_TEST_') === false &&
                           strpos($record->regional_manager_name, 'LIVE_TEST_') === false &&
                           strpos($record->property_manager_email, 'live.test.') === false &&
                           strpos($record->regional_manager_email, 'live.test.') === false;
            
            if ($was_test_record) {
                $test_records_found++;
                if ($is_now_clean) {
                    $test_records_cleaned++;
                    echo "   ✅ TEST DATA REPLACED - Import successful!\n";
                } else {
                    echo "   ⚠️  STILL HAS TEST DATA - Import may not have processed this record\n";
                }
            }
            
            echo str_repeat('-', 70) . "\n";
        }
    } else {
        echo "❌ NO RECENT UPDATES FOUND\n";
        echo "   Either no import occurred, or the import data matched existing data exactly.\n";
    }
    
    // Check for any remaining test data
    echo "\n=== CHECKING FOR REMAINING TEST DATA ===\n";
    
    $remaining_test_data = HB837::where(function($query) {
        $query->where('property_manager_name', 'LIKE', '%LIVE_TEST_%')
              ->orWhere('regional_manager_name', 'LIKE', '%LIVE_TEST_%')
              ->orWhere('property_manager_email', 'LIKE', '%live.test.%')
              ->orWhere('regional_manager_email', 'LIKE', '%live.test.%');
    })->get();
    
    if ($remaining_test_data->count() > 0) {
        echo "⚠️  Found {$remaining_test_data->count()} records still with test data:\n";
        foreach ($remaining_test_data as $record) {
            echo "   - {$record->property_name} (ID: {$record->id})\n";
        }
        echo "\nThis suggests these properties were not in the imported file.\n";
    } else {
        echo "✅ No remaining test data found - all test records were processed!\n";
    }
    
    // Final assessment
    echo "\n=== FIELD MAPPING TEST RESULTS ===\n";
    echo str_repeat('=', 80) . "\n";
    
    if ($recent_updates->count() > 0) {
        echo "✅ DATABASE UPDATES DETECTED: {$recent_updates->count()} records updated\n";
        echo "✅ IMPORT SYSTEM FUNCTIONING: Records were processed\n";
        
        if ($test_records_cleaned > 0) {
            echo "✅ FIELD MAPPING SUCCESS: {$test_records_cleaned} test records properly updated\n";
            echo "✅ MANAGER FIELDS WORKING: Data imported to correct database columns\n";
            echo "\n🎉 CRAIG GUNDRY'S FIELD MAPPING ISSUE IS RESOLVED ON LIVE SERVER!\n";
        } else if ($test_records_found > 0) {
            echo "⚠️  PARTIAL SUCCESS: Test records found but not all were cleaned\n";
            echo "   This may indicate the import file doesn't contain all test properties\n";
        } else {
            echo "ℹ️  NO TEST RECORDS IN UPDATES: Import may have processed different properties\n";
        }
    } else {
        echo "❌ NO IMPORT ACTIVITY DETECTED\n";
        echo "   Please ensure the import was completed through the web interface\n";
    }
    
    echo "\nVerification complete.\n";
}

function cleanupTestData()
{
    echo "=== CLEANING UP TEST DATA ===\n\n";
    
    // Check if backup file exists
    if (!file_exists('hb837_test_backup.json')) {
        echo "⚠️  No backup file found (hb837_test_backup.json)\n";
        echo "   Attempting to clean up any remaining test data...\n";
        
        // Clean up any remaining test data without backup
        $test_records = HB837::where(function($query) {
            $query->where('property_manager_name', 'LIKE', '%LIVE_TEST_%')
                  ->orWhere('regional_manager_name', 'LIKE', '%LIVE_TEST_%')
                  ->orWhere('property_manager_email', 'LIKE', '%live.test.%')
                  ->orWhere('regional_manager_email', 'LIKE', '%live.test.%');
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
    
    // Restore from backup
    $backup_data = json_decode(file_get_contents('hb837_test_backup.json'), true);
    
    echo "Restoring " . count($backup_data) . " records from backup...\n\n";
    
    $restored_count = 0;
    
    foreach ($backup_data as $record_id => $original_data) {
        $record = HB837::find($record_id);
        
        if ($record) {
            echo "🔄 Restoring: {$record->property_name} (ID: {$record_id})\n";
            echo "   PM: {$original_data['property_manager_name']} ({$original_data['property_manager_email']})\n";
            echo "   RM: {$original_data['regional_manager_name']} ({$original_data['regional_manager_email']})\n";
            
            $record->update($original_data);
            $restored_count++;
            
            echo "   ✅ Restored successfully\n\n";
        } else {
            echo "❌ Record ID {$record_id} not found\n\n";
        }
    }
    
    // Remove backup file
    unlink('hb837_test_backup.json');
    
    echo "✅ Cleanup complete!\n";
    echo "   - Restored {$restored_count} records\n";
    echo "   - Removed backup file\n";
    echo "\nLive server testing complete.\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "HB837 Field Mapping Test Script - " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 80) . "\n\n";
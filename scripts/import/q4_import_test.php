<?php
/**
 * Q4 File Import Test - All Fields Verification
 * ============================================
 * 
 * This script tests that all fields from the updated Q4 file are being imported correctly.
 * It will check field mappings and verify import results for all columns.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== Q4 FILE IMPORT TEST - ALL FIELDS VERIFICATION ===\n\n";

// Expected Q4 file headers based on previous analysis
$expected_q4_headers = [
    'Property Name',
    'Type', 
    'Units',
    'Address',
    'City',
    'State', 
    'Zip',
    'Scheduled Date of Inspection',
    'Phone',
    'Property Manager',
    'PM Email',
    'Regional Manager', 
    'RM Email',
    'Consultant Notes'
];

echo "Expected Q4 file headers:\n";
foreach ($expected_q4_headers as $index => $header) {
    echo "  [" . ($index + 1) . "] \"{$header}\"\n";
}

echo "\n=== FIELD MAPPING VERIFICATION ===\n";

$field_mappings = config('hb837_field_mapping.field_mapping', []);

// Check how each Q4 header should map
$mapping_results = [];

foreach ($expected_q4_headers as $header) {
    $mapped_field = null;
    $confidence = 0;
    
    // Find which database field this header maps to
    foreach ($field_mappings as $db_field => $possible_headers) {
        foreach ($possible_headers as $possible_header) {
            if (strcasecmp($header, $possible_header) === 0) {
                $mapped_field = $db_field;
                $confidence = 1.0;
                break 2;
            }
        }
    }
    
    $mapping_results[$header] = [
        'db_field' => $mapped_field,
        'confidence' => $confidence
    ];
    
    $status = $mapped_field ? "✅ → {$mapped_field}" : "❌ UNMAPPED";
    echo "\"{$header}\" {$status}\n";
}

echo "\n=== CRITICAL FIELD MAPPING VERIFICATION ===\n";

$critical_mappings = [
    'Property Manager' => 'property_manager_name',
    'PM Email' => 'property_manager_email', 
    'Regional Manager' => 'regional_manager_name',
    'RM Email' => 'regional_manager_email'
];

$all_critical_correct = true;

foreach ($critical_mappings as $header => $expected_field) {
    $actual_field = $mapping_results[$header]['db_field'] ?? null;
    $is_correct = $actual_field === $expected_field;
    
    if (!$is_correct) {
        $all_critical_correct = false;
    }
    
    $status = $is_correct ? "✅ CORRECT" : "❌ WRONG";
    echo "\"{$header}\" → {$expected_field} {$status}\n";
    
    if (!$is_correct) {
        echo "   Expected: {$expected_field}\n";
        echo "   Actual: " . ($actual_field ?: 'UNMAPPED') . "\n";
    }
}

if ($all_critical_correct) {
    echo "\n🎉 ALL CRITICAL FIELD MAPPINGS ARE CORRECT!\n";
} else {
    echo "\n⚠️  FIELD MAPPING ISSUES DETECTED!\n";
}

echo "\n=== PRE-IMPORT DATABASE STATE ===\n";

// Get current counts for key fields
$current_stats = [
    'total_records' => HB837::count(),
    'with_property_manager_name' => HB837::whereNotNull('property_manager_name')->where('property_manager_name', '!=', '')->count(),
    'with_property_manager_email' => HB837::whereNotNull('property_manager_email')->where('property_manager_email', '!=', '')->count(),
    'with_regional_manager_name' => HB837::whereNotNull('regional_manager_name')->where('regional_manager_name', '!=', '')->count(),
    'with_regional_manager_email' => HB837::whereNotNull('regional_manager_email')->where('regional_manager_email', '!=', '')->count(),
    'with_phone' => HB837::whereNotNull('phone')->where('phone', '!=', '')->count(),
    'with_consultant_notes' => HB837::whereNotNull('consultant_notes')->where('consultant_notes', '!=', '')->count(),
    'with_scheduled_date' => HB837::whereNotNull('scheduled_date_of_inspection')->count()
];

echo "Current database statistics:\n";
foreach ($current_stats as $field => $count) {
    echo "  {$field}: {$count}\n";
}

// Save stats for comparison
file_put_contents('pre_import_stats.json', json_encode($current_stats, JSON_PRETTY_PRINT));

echo "\n=== SETUP TEST DATA ===\n";

// Find some records to modify for testing
$test_records = HB837::whereNotNull('property_name')
    ->limit(5)
    ->get();

if ($test_records->count() > 0) {
    echo "Modifying " . $test_records->count() . " records for import testing:\n";
    
    $backup_data = [];
    
    foreach ($test_records as $record) {
        // Backup original data
        $backup_data[$record->id] = [
            'property_manager_name' => $record->property_manager_name,
            'property_manager_email' => $record->property_manager_email,
            'regional_manager_name' => $record->regional_manager_name,
            'regional_manager_email' => $record->regional_manager_email,
            'phone' => $record->phone,
            'consultant_notes' => $record->consultant_notes,
            'scheduled_date_of_inspection' => $record->scheduled_date_of_inspection
        ];
        
        // Create test data  
        $test_data = [
            'property_manager_name' => 'Q4_TEST_PM_' . $record->id,
            'property_manager_email' => 'q4.test.pm.' . $record->id . '@example.com',
            'regional_manager_name' => 'Q4_TEST_RM_' . $record->id,
            'regional_manager_email' => 'q4.test.rm.' . $record->id . '@example.com',
            'phone' => '555-TEST-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
            'consultant_notes' => 'Q4_TEST_NOTES_' . $record->id . '_' . date('Y-m-d'),
            'scheduled_date_of_inspection' => '2025-12-31'
        ];
        
        echo "\n🔄 {$record->property_name} (ID: {$record->id})\n";
        echo "   Setting test PM: {$test_data['property_manager_name']}\n";
        echo "   Setting test RM: {$test_data['regional_manager_name']}\n";
        echo "   Setting test phone: {$test_data['phone']}\n";
        
        $record->update($test_data);
    }
    
    // Save backup
    file_put_contents('q4_test_backup.json', json_encode($backup_data, JSON_PRETTY_PRINT));
    
    echo "\n✅ Test data setup complete!\n";
    echo "📁 Backup saved to: q4_test_backup.json\n";
} else {
    echo "❌ No suitable records found for testing!\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Upload the updated Q4 assessment file through the import interface\n";
echo "2. Wait for import to complete\n";
echo "3. Run: php q4_import_verify.php\n";
echo "4. Run: php q4_import_cleanup.php (when done)\n\n";

echo "🎯 What to expect:\n";
echo "   - All Q4 headers should map to correct database fields\n";
echo "   - Test data should be replaced with real Q4 data\n";
echo "   - Field counts should increase for populated columns\n";
echo "   - Import should show successful updates\n\n";

echo "Ready for Q4 import test!\n";
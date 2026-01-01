<?php
/**
 * Q4 Import Verification Script
 * ============================
 * 
 * This script verifies the results of the Q4 import test.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Carbon\Carbon;

echo "=== Q4 IMPORT VERIFICATION ===\n\n";

// Load pre-import stats for comparison
$pre_import_stats = [];
if (file_exists('pre_import_stats.json')) {
    $pre_import_stats = json_decode(file_get_contents('pre_import_stats.json'), true);
    echo "📊 Loaded pre-import statistics\n";
} else {
    echo "⚠️  No pre-import statistics found\n";
}

// Get current stats
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

echo "\n=== FIELD COUNT COMPARISON ===\n";

if (!empty($pre_import_stats)) {
    foreach ($current_stats as $field => $current_count) {
        $pre_count = $pre_import_stats[$field] ?? 0;
        $change = $current_count - $pre_count;
        $change_str = $change > 0 ? "+{$change}" : ($change < 0 ? "{$change}" : "0");
        $status = $change > 0 ? "📈" : ($change < 0 ? "📉" : "➡️");
        
        echo "  {$field}: {$pre_count} → {$current_count} ({$change_str}) {$status}\n";
    }
} else {
    echo "Current field counts:\n";
    foreach ($current_stats as $field => $count) {
        echo "  {$field}: {$count}\n";
    }
}

// Check for recent updates (last 15 minutes)
$recent_time = Carbon::now()->subMinutes(15);
echo "\n=== RECENT IMPORT ACTIVITY ===\n";
echo "Checking for updates since: {$recent_time}\n\n";

$recent_updates = HB837::where('updated_at', '>=', $recent_time)
    ->select([
        'id', 'property_name', 'property_manager_name', 'property_manager_email',
        'regional_manager_name', 'regional_manager_email', 'phone', 
        'consultant_notes', 'scheduled_date_of_inspection', 'updated_at'
    ])
    ->orderBy('updated_at', 'desc')
    ->limit(20)
    ->get();

echo "Found {$recent_updates->count()} recently updated records:\n";

$test_records_processed = 0;
$test_records_cleaned = 0;

foreach ($recent_updates as $record) {
    echo "\n📊 {$record->property_name} (ID: {$record->id})\n";
    echo "   PM: " . ($record->property_manager_name ?: 'NULL') . " (" . ($record->property_manager_email ?: 'NULL') . ")\n";
    echo "   RM: " . ($record->regional_manager_name ?: 'NULL') . " (" . ($record->regional_manager_email ?: 'NULL') . ")\n";
    echo "   Phone: " . ($record->phone ?: 'NULL') . "\n";
    echo "   Notes: " . (substr($record->consultant_notes ?: 'NULL', 0, 50)) . "\n";
    echo "   Scheduled: " . ($record->scheduled_date_of_inspection ?: 'NULL') . "\n";
    echo "   Updated: {$record->updated_at}\n";
    
    // Check if this was a test record
    $was_test_record = strpos($record->property_manager_name, 'Q4_TEST_') !== false ||
                      strpos($record->regional_manager_name, 'Q4_TEST_') !== false ||
                      strpos($record->phone, '555-TEST-') !== false ||
                      strpos($record->consultant_notes, 'Q4_TEST_NOTES_') !== false;
    
    $is_clean = strpos($record->property_manager_name, 'Q4_TEST_') === false &&
               strpos($record->regional_manager_name, 'Q4_TEST_') === false &&
               strpos($record->phone, '555-TEST-') === false &&
               strpos($record->consultant_notes, 'Q4_TEST_NOTES_') === false;
    
    if ($was_test_record) {
        $test_records_processed++;
        if ($is_clean) {
            $test_records_cleaned++;
            echo "   ✅ TEST DATA REPLACED - Import successful!\n";
        } else {
            echo "   ⚠️  STILL HAS TEST DATA - Import incomplete\n";
        }
    }
    
    echo str_repeat('-', 70) . "\n";
}

// Check for remaining test data
echo "\n=== REMAINING TEST DATA CHECK ===\n";

$remaining_test = HB837::where(function($query) {
    $query->where('property_manager_name', 'LIKE', '%Q4_TEST_%')
          ->orWhere('regional_manager_name', 'LIKE', '%Q4_TEST_%')  
          ->orWhere('phone', 'LIKE', '%555-TEST-%')
          ->orWhere('consultant_notes', 'LIKE', '%Q4_TEST_NOTES_%');
})->count();

echo "Records with remaining test data: {$remaining_test}\n";

if ($remaining_test > 0) {
    $test_samples = HB837::where(function($query) {
        $query->where('property_manager_name', 'LIKE', '%Q4_TEST_%')
              ->orWhere('regional_manager_name', 'LIKE', '%Q4_TEST_%')  
              ->orWhere('phone', 'LIKE', '%555-TEST-%')
              ->orWhere('consultant_notes', 'LIKE', '%Q4_TEST_NOTES_%');
    })->select(['id', 'property_name', 'property_manager_name', 'regional_manager_name'])
      ->limit(5)
      ->get();
    
    echo "\nSample records with test data:\n";
    foreach ($test_samples as $sample) {
        echo "  - {$sample->property_name} (ID: {$sample->id})\n";
    }
}

echo "\n=== IMPORT TEST RESULTS ===\n";
echo str_repeat('=', 80) . "\n";

if ($recent_updates->count() > 0) {
    echo "✅ DATABASE ACTIVITY DETECTED: {$recent_updates->count()} records updated\n";
    
    if ($test_records_processed > 0) {
        echo "✅ TEST RECORDS PROCESSED: {$test_records_processed} records\n";
        
        if ($test_records_cleaned > 0) {
            echo "✅ SUCCESSFUL FIELD UPDATES: {$test_records_cleaned} records cleaned\n";
            echo "🎉 Q4 IMPORT FIELD MAPPING IS WORKING CORRECTLY!\n";
        } else {
            echo "⚠️  TEST DATA NOT REPLACED: Import may have issues\n";
        }
    } else {
        echo "ℹ️  NO TEST RECORDS IN UPDATES: Import processed different records\n";
    }
    
    // Analyze field improvements
    if (!empty($pre_import_stats)) {
        $improvements = [];
        foreach ($current_stats as $field => $current_count) {
            $pre_count = $pre_import_stats[$field] ?? 0;
            $change = $current_count - $pre_count;
            if ($change > 0) {
                $improvements[] = "{$field}: +{$change}";
            }
        }
        
        if (!empty($improvements)) {
            echo "✅ FIELD IMPROVEMENTS: " . implode(', ', $improvements) . "\n";
        }
    }
} else {
    echo "❌ NO RECENT UPDATES: Import may not have occurred or data was identical\n";
}

if ($remaining_test === 0) {
    echo "✅ ALL TEST DATA CLEANED: Import processed all test records\n";
} else {
    echo "⚠️  {$remaining_test} RECORDS WITH TEST DATA: Some records not processed\n";
}

echo "\nVerification complete.\n";
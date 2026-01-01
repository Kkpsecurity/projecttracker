<?php
/**
 * Verify Status Protection
 * ========================
 * 
 * This script verifies that status protection worked after import.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Carbon\Carbon;

echo "=== STATUS PROTECTION VERIFICATION ===\n\n";

// Load test record IDs
if (!file_exists('status_test_records.json')) {
    echo "❌ No test records file found. Run test_status_protection.php first.\n";
    exit;
}

$test_ids = json_decode(file_get_contents('status_test_records.json'), true);
echo "📋 Checking " . count($test_ids) . " test records for status preservation...\n\n";

$all_protected = true;
$status_changes = 0;
$recent_updates = 0;

foreach ($test_ids as $test_id) {
    $record = HB837::find($test_id);
    
    if (!$record) {
        echo "❌ Record ID {$test_id} not found\n";
        continue;
    }
    
    echo "🏢 {$record->property_name} (ID: {$record->id})\n";
    echo "   Contracting Status: {$record->contracting_status}\n";
    echo "   Report Status: {$record->report_status}\n";
    echo "   Last Updated: {$record->updated_at}\n";
    
    // Check if updated recently (last 15 minutes)
    $recently_updated = $record->updated_at >= Carbon::now()->subMinutes(15);
    if ($recently_updated) {
        $recent_updates++;
        echo "   📅 RECENTLY UPDATED ✓\n";
    }
    
    // Check for status downgrades
    $has_good_contracting_status = in_array($record->contracting_status, ['started', 'executed', 'closed']);
    $has_good_report_status = in_array($record->report_status, ['underway', 'in-review', 'completed']);
    
    if ($record->contracting_status === 'quoted' && $recently_updated) {
        echo "   ⚠️  CONTRACTING STATUS MAY HAVE BEEN DOWNGRADED TO 'quoted'\n";
        $all_protected = false;
        $status_changes++;
    } else if ($has_good_contracting_status) {
        echo "   ✅ CONTRACTING STATUS PRESERVED (" . strtoupper($record->contracting_status) . ")\n";
    }
    
    if ($record->report_status === 'not-started' && $recently_updated) {
        echo "   ⚠️  REPORT STATUS MAY HAVE BEEN DOWNGRADED TO 'not-started'\n";
        $all_protected = false;
        $status_changes++;
    } else if ($has_good_report_status) {
        echo "   ✅ REPORT STATUS PRESERVED (" . strtoupper($record->report_status) . ")\n";
    }
    
    echo str_repeat('-', 70) . "\n";
}

echo "\n=== VERIFICATION RESULTS ===\n";
echo str_repeat('=', 80) . "\n";

echo "📊 Test Records: " . count($test_ids) . "\n";
echo "📅 Recently Updated: {$recent_updates}\n";
echo "⚠️  Status Changes Detected: {$status_changes}\n";

if ($all_protected && $recent_updates > 0) {
    echo "\n🎉 STATUS PROTECTION WORKING!\n";
    echo "✅ All advanced status values were preserved during import\n";
    echo "✅ Import updated other fields without downgrading status\n";
    echo "✅ Status hierarchy protection is functioning correctly\n";
} else if ($recent_updates === 0) {
    echo "\n ℹ️ NO RECENT UPDATES DETECTED\n";
    echo "   Either no import occurred, or these records weren't in the import file\n";
    echo "   This is normal if the Q4 file doesn't contain these test properties\n";
} else {
    echo "\n⚠️  STATUS PROTECTION MAY HAVE ISSUES\n";
    echo "   {$status_changes} potential status downgrades detected\n";
    echo "   Please review the Enhanced Import logic\n";
}

// Check for any recent downgrades in the general database
echo "\n=== CHECKING FOR RECENT STATUS DOWNGRADES ===\n";

$recent_time = Carbon::now()->subMinutes(15);
$recent_quoted = HB837::where('contracting_status', 'quoted')
    ->where('updated_at', '>=', $recent_time)
    ->count();

$recent_not_started = HB837::where('report_status', 'not-started')
    ->where('updated_at', '>=', $recent_time)
    ->count();

echo "Records set to 'quoted' in last 15 minutes: {$recent_quoted}\n";
echo "Records set to 'not-started' in last 15 minutes: {$recent_not_started}\n";

if ($recent_quoted > 5 || $recent_not_started > 5) {
    echo "\n⚠️  HIGH NUMBER OF STATUS DOWNGRADES DETECTED!\n";
    echo "   This suggests the import may be applying default status values\n";
    echo "   instead of preserving existing higher status values.\n";
} else {
    echo "\n✅ Status downgrade counts look normal\n";
}

echo "\nVerification complete.\n";
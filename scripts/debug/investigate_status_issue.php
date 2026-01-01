<?php
/**
 * Status Field Investigation Script
 * ================================
 * 
 * This script investigates why status fields are being changed during import
 * when they should potentially be preserved.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Carbon\Carbon;

echo "=== STATUS FIELD INVESTIGATION ===\n\n";

// Check recent status changes (last 30 minutes)
$recent_time = Carbon::now()->subMinutes(30);
echo "Investigating status changes since: {$recent_time}\n\n";

$recent_updates = HB837::where('updated_at', '>=', $recent_time)
    ->select([
        'id', 'property_name', 'report_status', 'contracting_status', 
        'updated_at', 'created_at'
    ])
    ->orderBy('updated_at', 'desc')
    ->get();

echo "=== RECENT STATUS UPDATES ===\n";
echo "Found {$recent_updates->count()} recently updated records:\n\n";

foreach ($recent_updates as $record) {
    echo "🏢 {$record->property_name} (ID: {$record->id})\n";
    echo "   Report Status: {$record->report_status}\n";
    echo "   Contracting Status: {$record->contracting_status}\n";
    echo "   Updated: {$record->updated_at}\n";
    echo "   Created: {$record->created_at}\n";
    
    // Check if this looks like a status that was downgraded
    if ($record->contracting_status === 'quoted' || $record->contracting_status === 'started') {
        echo "   ⚠️  STATUS CONCERN: This may have been downgraded from 'executed'\n";
    }
    
    if ($record->report_status === 'not-started') {
        echo "   ⚠️  REPORT CONCERN: This may have been reset to 'not-started'\n";
    }
    
    echo str_repeat('-', 70) . "\n";
}

echo "\n=== STATUS FIELD MAPPING ANALYSIS ===\n";

// Check what status mappings are configured
$status_maps = config('hb837_field_mapping.status_maps', []);

echo "Report Status Mappings:\n";
if (isset($status_maps['report_status'])) {
    foreach ($status_maps['report_status'] as $input => $output) {
        echo "  \"{$input}\" → \"{$output}\"\n";
    }
} else {
    echo "  ❌ No report status mappings found\n";
}

echo "\nContracting Status Mappings:\n";
if (isset($status_maps['contracting_status'])) {
    foreach ($status_maps['contracting_status'] as $input => $output) {
        echo "  \"{$input}\" → \"{$output}\"\n";
    }
} else {
    echo "  ❌ No contracting status mappings found\n";
}

echo "\n=== DEFAULT VALUES CHECK ===\n";

$default_values = config('hb837_field_mapping.import_rules.default_values', []);

echo "Default values applied during import:\n";
foreach ($default_values as $field => $value) {
    echo "  {$field}: \"{$value}\"\n";
    
    if ($field === 'contracting_status' && $value === 'quoted') {
        echo "    ⚠️  WARNING: This default may override existing 'executed' status!\n";
    }
    
    if ($field === 'report_status' && $value === 'not-started') {
        echo "    ⚠️  WARNING: This default may override existing report progress!\n";
    }
}

echo "\n=== UPDATE RULES ANALYSIS ===\n";

$update_rules = config('hb837_field_mapping.import_rules.update_rules', []);

echo "Update rules:\n";
foreach ($update_rules as $rule => $enabled) {
    echo "  {$rule}: " . ($enabled ? "✅ ENABLED" : "❌ DISABLED") . "\n";
    
    if ($rule === 'empty_to_value' && $enabled) {
        echo "    → Empty fields will be populated with new values\n";
    }
    
    if ($rule === 'value_changed' && $enabled) {
        echo "    → Existing values will be OVERWRITTEN if different\n";
        echo "    ⚠️  This could cause 'executed' → 'quoted' downgrades!\n";
    }
}

echo "\n=== POTENTIAL ISSUE ANALYSIS ===\n";

// Look for records that might have been downgraded
$potentially_downgraded = HB837::where('updated_at', '>=', $recent_time)
    ->where(function($query) {
        $query->where('contracting_status', 'quoted')
              ->orWhere('contracting_status', 'started')
              ->orWhere('report_status', 'not-started');
    })
    ->count();

echo "Records with potentially downgraded status: {$potentially_downgraded}\n";

if ($potentially_downgraded > 0) {
    echo "\n🚨 POTENTIAL STATUS DOWNGRADE ISSUE DETECTED!\n\n";
    
    echo "Possible causes:\n";
    echo "1. Q4 file contains blank/empty status columns\n";
    echo "2. Import applies default values even when DB has better status\n";
    echo "3. Update rules override existing progress with imported blanks\n";
    echo "4. Status mapping not handling empty/missing values correctly\n\n";
    
    echo "Recommended fixes:\n";
    echo "1. Check Q4 file - does it have status columns?\n";
    echo "2. Modify update rules to preserve better existing status\n";
    echo "3. Add logic to not downgrade status values\n";
    echo "4. Consider status hierarchy (executed > started > quoted)\n";
}

echo "\n=== STATUS PRESERVATION RECOMMENDATIONS ===\n";

echo "To prevent status downgrades:\n\n";

echo "1. MODIFY UPDATE RULES:\n";
echo "   - Don't overwrite 'executed' with 'quoted'\n";
echo "   - Don't overwrite 'completed' with 'not-started'\n";
echo "   - Preserve higher status values\n\n";

echo "2. ADD STATUS HIERARCHY LOGIC:\n";
echo "   - contracting_status: executed > started > quoted\n";
echo "   - report_status: completed > in-review > underway > not-started\n\n";

echo "3. CHECK Q4 FILE CONTENT:\n";
echo "   - Does Q4 file have Report Status column?\n";
echo "   - Does Q4 file have Contracting Status column?\n";
echo "   - Are these columns blank/empty?\n\n";

echo "4. IMPLEMENT SMART UPDATE LOGIC:\n";
echo "   - Only update status if import has BETTER status\n";
echo "   - Or only update if DB status is empty/default\n\n";

// Check current status distribution
echo "=== CURRENT STATUS DISTRIBUTION ===\n";

$contracting_distribution = HB837::selectRaw('contracting_status, COUNT(*) as count')
    ->whereNotNull('contracting_status')
    ->groupBy('contracting_status')
    ->orderBy('count', 'desc')
    ->get();

echo "Contracting Status Distribution:\n";
foreach ($contracting_distribution as $status) {
    echo "  {$status->contracting_status}: {$status->count}\n";
}

$report_distribution = HB837::selectRaw('report_status, COUNT(*) as count')
    ->whereNotNull('report_status')
    ->groupBy('report_status')
    ->orderBy('count', 'desc')
    ->get();

echo "\nReport Status Distribution:\n";
foreach ($report_distribution as $status) {
    echo "  {$status->report_status}: {$status->count}\n";
}

echo "\nInvestigation complete.\n";
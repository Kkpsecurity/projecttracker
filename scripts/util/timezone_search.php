<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Carbon\Carbon;

echo "=== TIMEZONE-AWARE SEARCH FOR UPDATED RECORDS ===\n\n";

// The logs show these times (likely local time):
// [2025-10-06 16:46:17] and [2025-10-06 17:20:20]

// Try different timezone interpretations
$timezones = [
    'UTC',
    'America/New_York',    // Eastern
    'America/Chicago',     // Central
    'America/Denver',      // Mountain  
    'America/Los_Angeles', // Pacific
    'America/Phoenix'      // Arizona
];

$log_times = [
    '2025-10-06 16:46:17',
    '2025-10-06 17:20:20'
];

foreach ($timezones as $tz) {
    echo "=== CHECKING TIMEZONE: {$tz} ===\n";
    
    foreach ($log_times as $log_time) {
        // Parse log time as if it's in this timezone, then convert to UTC for database query
        $local_time = Carbon::createFromFormat('Y-m-d H:i:s', $log_time, $tz);
        $utc_time = $local_time->utc();
        
        echo "\nLog time: {$log_time} ({$tz}) = {$utc_time} (UTC)\n";
        
        // Search for records updated within 2 minutes
        $records = HB837::whereBetween('updated_at', [
            $utc_time->copy()->subMinutes(2),
            $utc_time->copy()->addMinutes(2)
        ])->select([
            'id', 'property_name', 'property_manager_name', 'regional_manager_name', 'updated_at'
        ])->get();
        
        if ($records->count() > 0) {
            echo "✅ FOUND {$records->count()} RECORDS!\n";
            foreach ($records as $record) {
                echo "  ID: {$record->id} - {$record->property_name}\n";
                echo "  PM: " . ($record->property_manager_name ?: 'NULL') . "\n";
                echo "  RM: " . ($record->regional_manager_name ?: 'NULL') . "\n";
                echo "  Updated: {$record->updated_at} UTC\n";
                echo "  ---\n";
            }
        } else {
            echo "No records found for this time.\n";
        }
    }
    echo "\n" . str_repeat('-', 80) . "\n";
}

// Also try a broader search - all records updated in the last 4 hours
echo "\n=== BROADER SEARCH: LAST 4 HOURS ===\n";
$four_hours_ago = Carbon::now()->subHours(4);
echo "Searching since: {$four_hours_ago}\n";

$recent_records = HB837::where('updated_at', '>=', $four_hours_ago)
    ->select([
        'id', 'property_name', 'property_manager_name', 'regional_manager_name', 'updated_at'
    ])
    ->orderBy('updated_at', 'desc')
    ->limit(20)
    ->get();

echo "Found {$recent_records->count()} records updated in last 4 hours:\n";

foreach ($recent_records as $record) {
    echo "\nID: {$record->id} - {$record->property_name}\n";
    echo "PM: " . ($record->property_manager_name ?: 'NULL') . "\n";
    echo "RM: " . ($record->regional_manager_name ?: 'NULL') . "\n";
    echo "Updated: {$record->updated_at}\n";
}

echo "\nAnalysis complete.\n";
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Illuminate\Support\Facades\DB;

echo "=== FINDING THE 17 UPDATED RECORDS ===\n\n";

// The logs show updates occurred at 4:46 PM and 5:20 PM
// Let's search for records updated around those times

$search_times = [
    '2025-10-06 16:46:00',  // 4:46 PM
    '2025-10-06 17:20:00'   // 5:20 PM
];

foreach ($search_times as $search_time) {
    $time = \Carbon\Carbon::parse($search_time);
    echo "Searching around: {$time}\n";
    
    // Search within 2 minutes of import time
    $updated_records = HB837::whereBetween('updated_at', [
        $time->copy()->subMinutes(2),
        $time->copy()->addMinutes(2)
    ])->select([
        'id',
        'property_name',
        'property_manager_name',
        'property_manager_email', 
        'regional_manager_name',
        'regional_manager_email',
        'updated_at'
    ])->orderBy('updated_at')->get();
    
    echo "Found {$updated_records->count()} records updated around this time:\n";
    
    foreach ($updated_records as $record) {
        echo "\nID: {$record->id} - {$record->property_name}\n";
        echo "PM: " . ($record->property_manager_name ?: 'NULL') . " (" . ($record->property_manager_email ?: 'NULL') . ")\n";
        echo "RM: " . ($record->regional_manager_name ?: 'NULL') . " (" . ($record->regional_manager_email ?: 'NULL') . ")\n";
        echo "Updated: {$record->updated_at}\n";
        echo str_repeat('-', 50) . "\n";
    }
    
    echo "\n" . str_repeat('=', 80) . "\n\n";
}

// Let's also check for any records with Q4 specific managers
echo "=== SEARCHING FOR Q4 MANAGERS IN DATABASE ===\n";

$q4_managers = [
    'Beverly Duda',
    'Renae Rodriguez', 
    'Kevin Wright',
    'Brian Santos',
    'Beverly',
    'Renae',
    'Kevin',
    'Brian'
];

foreach ($q4_managers as $manager) {
    echo "\nSearching for '{$manager}':\n";
    
    $pm_matches = HB837::where('property_manager_name', 'ILIKE', "%{$manager}%")
        ->select(['id', 'property_name', 'property_manager_name', 'updated_at'])
        ->orderBy('updated_at', 'desc')
        ->limit(3)
        ->get();
    
    $rm_matches = HB837::where('regional_manager_name', 'ILIKE', "%{$manager}%")
        ->select(['id', 'property_name', 'regional_manager_name', 'updated_at'])
        ->orderBy('updated_at', 'desc')
        ->limit(3)
        ->get();
    
    if ($pm_matches->count() > 0) {
        echo "  Property Manager matches ({$pm_matches->count()}):\n";
        foreach ($pm_matches as $match) {
            echo "    ID: {$match->id} - {$match->property_name} - {$match->property_manager_name} - {$match->updated_at}\n";
        }
    }
    
    if ($rm_matches->count() > 0) {
        echo "  Regional Manager matches ({$rm_matches->count()}):\n";
        foreach ($rm_matches as $match) {
            echo "    ID: {$match->id} - {$match->property_name} - {$match->regional_manager_name} - {$match->updated_at}\n";
        }
    }
    
    if ($pm_matches->count() == 0 && $rm_matches->count() == 0) {
        echo "  No matches found for '{$manager}'\n";
    }
}

echo "\nSearch complete.\n";
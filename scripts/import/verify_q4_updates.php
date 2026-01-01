<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Illuminate\Support\Facades\DB;

echo "=== Q4 ASSESSMENT UPDATE VERIFICATION ===\n\n";

// Get records updated in the last hour
$recent_updates = HB837::where('updated_at', '>=', now()->subHour())
    ->whereNotNull('regional_manager_name')
    ->whereNotNull('regional_manager_email')
    ->select([
        'property_name', 
        'property_manager_name', 
        'property_manager_email',
        'regional_manager_name', 
        'regional_manager_email',
        'updated_at'
    ])
    ->orderBy('updated_at', 'desc')
    ->limit(20)
    ->get();

echo "RECENTLY UPDATED RECORDS WITH MANAGER INFO:\n";
echo "Count: " . $recent_updates->count() . "\n\n";

foreach ($recent_updates as $record) {
    echo "Property: {$record->property_name}\n";
    echo "PM: {$record->property_manager_name} ({$record->property_manager_email})\n";
    echo "RM: {$record->regional_manager_name} ({$record->regional_manager_email})\n";
    echo "Updated: {$record->updated_at}\n";
    echo str_repeat('-', 80) . "\n";
}

// Check for Q4 specific managers
echo "\nQ4 MANAGER VERIFICATION:\n";
$q4_managers = ['Beverly Duda', 'Renae Rodriguez', 'Kevin Wright', 'Brian Santos'];

foreach ($q4_managers as $manager) {
    $pm_count = HB837::where('property_manager_name', 'LIKE', "%{$manager}%")->count();
    $rm_count = HB837::where('regional_manager_name', 'LIKE', "%{$manager}%")->count();
    
    echo "{$manager}:\n";
    echo "  - As Property Manager: {$pm_count} records\n";
    echo "  - As Regional Manager: {$rm_count} records\n";
}

echo "\nVerification complete.\n";
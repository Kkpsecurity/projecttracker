<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== IMPORT RESULTS ANALYSIS ===\n\n";

// Get final counts
$hb837Count = DB::table('hb837')->count();
echo "Final HB837 records: {$hb837Count}\n\n";

if ($hb837Count > 0) {
    echo "RECORD DETAILS:\n";
    echo str_repeat("-", 50) . "\n";
    
    $records = DB::table('hb837')->get();
    
    foreach ($records as $record) {
        echo "ID: {$record->id}\n";
        echo "  Property: {$record->property_name}\n";
        echo "  Address: {$record->address}\n";
        echo "  City: {$record->city}, {$record->state} {$record->zip}\n";
        echo "  Report Status: {$record->report_status}\n";
        echo "  Contracting Status: {$record->contracting_status}\n";
        echo "  Assigned Consultant: " . ($record->assigned_consultant_id ?: 'None') . "\n";
        echo "  Property Manager: {$record->property_manager_name}\n";
        echo "  Regional Manager: {$record->regional_manager_name}\n";
        echo "  Quoted Price: $" . number_format($record->quoted_price ?? 0, 2) . "\n";
        echo "  Created: {$record->created_at}\n";
        echo "  Updated: {$record->updated_at}\n";
        echo str_repeat("-", 50) . "\n";
    }
}

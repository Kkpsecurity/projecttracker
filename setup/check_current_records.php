<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Current HB837 Records\n";
echo "=====================\n\n";

$records = HB837::orderBy('id', 'desc')->get();

echo "Total records: " . $records->count() . "\n\n";

foreach ($records as $record) {
    echo "ID: {$record->id}\n";
    echo "Property: {$record->property_name}\n";
    echo "Address: {$record->address}\n";
    echo "Status: {$record->report_status} / {$record->contracting_status}\n";
    echo "Security Risk: {$record->securitygauge_crime_risk}\n";
    echo "Macro Client: {$record->macro_client}\n";
    echo "Property Manager: {$record->property_manager_name}\n";
    echo "Created: {$record->created_at}\n";
    echo "----------------------------------------\n";
}

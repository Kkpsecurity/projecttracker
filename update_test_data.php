<?php

require_once 'vendor/autoload.php';

use App\Models\HB837;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== UPDATING HB837 RECORDS WITH TEST DATA ===\n";

    // Update all records with test data for the missing fields
    $records = HB837::all();

    foreach ($records as $record) {
        $updated = false;

        // Add securitygauge_crime_risk if missing
        if (empty($record->securitygauge_crime_risk)) {
            $riskLevels = ['Low', 'Moderate', 'Elevated', 'High', 'Severe'];
            $record->securitygauge_crime_risk = $riskLevels[array_rand($riskLevels)];
            $updated = true;
        }

        // Add macro_client if missing
        if (empty($record->macro_client)) {
            $clients = [
                'ABC Property Management',
                'Premier Real Estate Group',
                'Metro Property Solutions',
                'Sunrise Management Company',
                'Elite Property Services'
            ];
            $record->macro_client = $clients[array_rand($clients)];
            $updated = true;
        }

        // Ensure contracting_status is set for proper tab filtering
        if (empty($record->contracting_status)) {
            $record->contracting_status = 'quoted'; // This will make records show up in quoted tab
            $updated = true;
        }

        // Ensure report_status is set
        if (empty($record->report_status)) {
            $record->report_status = 'not-started';
            $updated = true;
        }

        if ($updated) {
            $record->save();
            echo "Updated Record ID {$record->id}:\n";
            echo "  - Crime Risk: {$record->securitygauge_crime_risk}\n";
            echo "  - Macro Client: {$record->macro_client}\n";
            echo "  - Contract Status: {$record->contracting_status}\n";
            echo "  - Report Status: {$record->report_status}\n\n";
        }
    }

    echo "=== UPDATE COMPLETE ===\n";
    echo "Total records updated: " . $records->count() . "\n";

    // Show summary of quoted tab records
    $quotedRecords = HB837::whereIn('contracting_status', ['quoted', 'started'])->count();
    echo "Records that should appear in 'quoted' tab: $quotedRecords\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

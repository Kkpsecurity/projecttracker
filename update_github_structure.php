<?php

require_once 'vendor/autoload.php';

use App\Models\HB837;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== UPDATING HB837 RECORDS WITH COMPLETE STRUCTURE ===\n";

    // Update all records with complete data for the new GitHub structure
    $records = HB837::all();

    $sampleData = [
        [
            'management_company' => 'Premier Property Management',
            'property_type' => 'midrise',
            'units' => 120,
            'city' => 'Miami',
            'state' => 'FL',
            'zip' => '33101',
            'phone' => '(305) 555-0123',
            'property_manager_name' => 'Sarah Johnson',
            'property_manager_email' => 'sarah.johnson@premier.com',
            'regional_manager_name' => 'Mike Chen',
            'regional_manager_email' => 'mike.chen@premier.com'
        ],
        [
            'management_company' => 'Sunrise Management Company',
            'property_type' => 'garden',
            'units' => 85,
            'city' => 'Orlando',
            'state' => 'FL',
            'zip' => '32801',
            'phone' => '(407) 555-0456',
            'property_manager_name' => 'David Rodriguez',
            'property_manager_email' => 'david.rodriguez@sunrise.com',
            'regional_manager_name' => 'Lisa Park',
            'regional_manager_email' => 'lisa.park@sunrise.com'
        ],
        [
            'management_company' => 'Elite Property Services',
            'property_type' => 'highrise',
            'units' => 200,
            'city' => 'Tampa',
            'state' => 'FL',
            'zip' => '33602',
            'phone' => '(813) 555-0789',
            'property_manager_name' => 'Jennifer Wilson',
            'property_manager_email' => 'jennifer.wilson@elite.com',
            'regional_manager_name' => 'Robert Kim',
            'regional_manager_email' => 'robert.kim@elite.com'
        ]
    ];

    foreach ($records as $index => $record) {
        $sampleIndex = $index % count($sampleData);
        $sample = $sampleData[$sampleIndex];

        // Update all the missing fields
        $record->management_company = $sample['management_company'];
        $record->property_type = $sample['property_type'];
        $record->units = $sample['units'];
        $record->city = $sample['city'];
        $record->state = $sample['state'];
        $record->zip = $sample['zip'];
        $record->phone = $sample['phone'];
        $record->property_manager_name = $sample['property_manager_name'];
        $record->property_manager_email = $sample['property_manager_email'];
        $record->regional_manager_name = $sample['regional_manager_name'];
        $record->regional_manager_email = $sample['regional_manager_email'];

        // Set some dates for testing
        if (empty($record->report_submitted) && $record->report_status === 'completed') {
            $record->report_submitted = Carbon::now()->subDays(rand(1, 30));
        }

        if (empty($record->billing_req_sent) && $record->report_submitted) {
            $record->billing_req_sent = Carbon::now()->subDays(rand(1, 20));
        }

        if (empty($record->agreement_submitted) && $record->contracting_status === 'executed') {
            $record->agreement_submitted = Carbon::now()->subDays(rand(30, 90));
        }

        $record->save();

        echo "Updated Record ID {$record->id}:\n";
        echo "  - Management Company: {$record->management_company}\n";
        echo "  - Property Type: {$record->property_type}\n";
        echo "  - Units: {$record->units}\n";
        echo "  - City: {$record->city}\n";
        echo "  - Contact: {$record->property_manager_name} ({$record->property_manager_email})\n\n";
    }

    echo "=== UPDATE COMPLETE ===\n";
    echo "All records now have complete GitHub structure data\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

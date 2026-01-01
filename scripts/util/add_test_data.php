<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Adding test data to hb837 table...\n";
    
    // Check if table is empty
    $count = DB::table('hb837')->count();
    echo "Current records: {$count}\n";
    
    if ($count > 0) {
        echo "Table already has data. Skipping insert.\n";
        exit;
    }
    
    // Insert test records
    $testData = [
        [
            'property_name' => 'Sunset Gardens Apartments',
            'property_type' => 'garden',
            'units' => 150,
            'address' => '123 Main Street',
            'city' => 'Orlando',
            'state' => 'FL',
            'zip' => '32801',
            'county' => 'Orange',
            'phone' => '(407) 555-0123',
            'owner_name' => 'Smith Property Management',
            'management_company' => 'ABC Management Corp',
            'property_manager_name' => 'John Smith',
            'property_manager_email' => 'john.smith@abcmanagement.com',
            'regional_manager_name' => 'Mary Johnson',
            'regional_manager_email' => 'mary.johnson@abcmanagement.com',
            'macro_client' => 'ABC Holdings',
            'macro_contact' => 'David Brown',
            'macro_email' => 'david.brown@abcholdings.com',
            'report_status' => 'in_progress',
            'contracting_status' => 'executed',
            'securitygauge_crime_risk' => 'Moderate',
            'quoted_price' => 2500.00,
            'scheduled_date_of_inspection' => '2025-07-20',
            'consultant_notes' => 'Property requires additional security assessment for parking areas. Previous inspection noted some lighting issues.',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'property_name' => 'Oakwood Residential Complex',
            'property_type' => 'midrise',
            'units' => 75,
            'address' => '456 Oak Avenue',
            'city' => 'Tampa',
            'state' => 'FL',
            'zip' => '33601',
            'county' => 'Hillsborough',
            'phone' => '(813) 555-0456',
            'owner_name' => 'Oakwood Development LLC',
            'management_company' => 'Premier Property Solutions',
            'property_manager_name' => 'Sarah Wilson',
            'property_manager_email' => 'sarah.wilson@premierprop.com',
            'regional_manager_name' => 'Robert Taylor',
            'regional_manager_email' => 'robert.taylor@premierprop.com',
            'macro_client' => 'XYZ Investments',
            'macro_contact' => 'Lisa Chen',
            'macro_email' => 'lisa.chen@xyzinv.com',
            'report_status' => 'not_started',
            'contracting_status' => 'executed',
            'securitygauge_crime_risk' => 'Low',
            'quoted_price' => 1800.00,
            'scheduled_date_of_inspection' => '2025-07-25',
            'consultant_notes' => 'New property with modern security systems already in place.',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];
    
    foreach ($testData as $data) {
        DB::table('hb837')->insert($data);
        echo "✅ Inserted: {$data['property_name']}\n";
    }
    
    $finalCount = DB::table('hb837')->count();
    echo "\n✅ Successfully added test data!\n";
    echo "Total records now: {$finalCount}\n";
    echo "\nYou can now test the PDF generation functionality.\n";
    
} catch (Exception $e) {
    echo "❌ Error adding test data: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

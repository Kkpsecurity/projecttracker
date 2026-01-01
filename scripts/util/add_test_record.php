<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Adding test HB837 record...\n";
    
    $recordId = DB::table('hb837')->insertGetId([
        'property_name' => 'Sunset Gardens Apartments',
        'property_type' => 'garden',
        'units' => 150,
        'securitygauge_crime_risk' => 'Medium',
        'address' => '123 Main Street',
        'city' => 'Miami',
        'state' => 'FL',
        'zip' => '33101',
        'phone' => '(305) 555-0123',
        'owner_name' => 'ABC Property Management',
        'management_company' => 'Premier Properties LLC',
        'property_manager_name' => 'John Smith',
        'property_manager_email' => 'john.smith@example.com',
        'regional_manager_name' => 'Sarah Johnson',
        'regional_manager_email' => 'sarah.johnson@example.com',
        'report_status' => 'not-started',
        'contracting_status' => 'executed',
        'quoted_price' => 25000.00,
        'scheduled_date_of_inspection' => now()->addDays(7),
        'macro_client' => 'ABC Corp',
        'macro_contact' => 'Mike Davis',
        'macro_email' => 'mike.davis@abccorp.com',
        'consultant_notes' => 'Property requires security assessment for gate system and perimeter lighting.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ Test record created successfully!\n";
    echo "Record ID: {$recordId}\n";
    echo "Property Name: Sunset Gardens Apartments\n";
    echo "Address: 123 Main Street, Miami, FL 33101\n";
    echo "\nYou can now test the PDF generation with this record.\n";
    
} catch (Exception $e) {
    echo "❌ Error creating test record: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

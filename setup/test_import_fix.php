<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing HB837 Import Fix\n";
echo "======================\n\n";

// Check current record count
$beforeCount = HB837::count();
echo "Records before test: $beforeCount\n";

// Test the fillable fields
echo "\nTesting fillable fields...\n";
$fillable = (new HB837())->getFillable();
echo "Fillable fields: " . implode(', ', $fillable) . "\n";

// Check if the missing fields are now included
$requiredFields = [
    'securitygauge_crime_risk',
    'property_manager_name',
    'property_manager_email',
    'regional_manager_name',
    'regional_manager_email',
    'macro_client',
    'macro_contact',
    'macro_email',
    'financial_notes'
];

echo "\nChecking required fields:\n";
foreach ($requiredFields as $field) {
    $exists = in_array($field, $fillable);
    echo "- $field: " . ($exists ? "✓ Present" : "✗ Missing") . "\n";
}

// Test creating a record with all the fields
echo "\nTesting record creation with new fields...\n";
try {
    $testData = [
        'user_id' => 1, // Assuming user ID 1 exists
        'property_name' => 'Test Import Property - ' . time(),
        'address' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'TX',
        'zip' => '12345',
        'management_company' => 'Test Management',
        'owner_name' => 'Test Owner',
        'property_type' => 'garden',
        'units' => 100,
        'report_status' => 'not-started',
        'contracting_status' => 'quoted',
        'quoted_price' => 15000.00,
        'sub_fees_estimated_expenses' => 2000.00,
        'project_net_profit' => 13000.00,
        'securitygauge_crime_risk' => 'Medium',
        'property_manager_name' => 'John Property Manager',
        'property_manager_email' => 'pm@test.com',
        'regional_manager_name' => 'Jane Regional Manager',
        'regional_manager_email' => 'rm@test.com',
        'macro_client' => 'Test Macro Client',
        'macro_contact' => 'Test Contact',
        'macro_email' => 'contact@test.com',
        'notes' => 'Test import record',
        'financial_notes' => 'Test financial notes'
    ];

    $record = HB837::create($testData);
    echo "✓ Successfully created test record with ID: " . $record->id . "\n";

    // Verify the record was saved with all fields
    $savedRecord = HB837::find($record->id);
    echo "✓ Verified record exists in database\n";
    echo "  - Property Name: " . $savedRecord->property_name . "\n";
    echo "  - Security Risk: " . $savedRecord->securitygauge_crime_risk . "\n";
    echo "  - Macro Client: " . $savedRecord->macro_client . "\n";
    echo "  - Property Manager: " . $savedRecord->property_manager_name . "\n";

} catch (\Exception $e) {
    echo "✗ Error creating test record: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

$afterCount = HB837::count();
echo "\nRecords after test: $afterCount\n";
echo "Records added: " . ($afterCount - $beforeCount) . "\n";

echo "\n======================\n";
echo "Test completed!\n";

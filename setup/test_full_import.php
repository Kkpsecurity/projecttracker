<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Complete Import Workflow\n";
echo "================================\n\n";

// Simulate authentication (using user ID 1)
Auth::loginUsingId(1);
echo "Authenticated as user ID: " . Auth::id() . "\n\n";

// Check current record count
$beforeCount = HB837::count();
echo "Records before import test: $beforeCount\n\n";

// Simulate the import data (what would come from an Excel file)
$importData = [
    [
        'property_name' => 'Sunset Apartments - Import Test',
        'address' => '456 Import Street',
        'city' => 'Import City',
        'state' => 'TX',
        'zip' => '75001',
        'management_company' => 'Import Management Co',
        'owner_name' => 'Import Property Owner',
        'property_type' => 'garden',
        'units' => 150,
        'report_status' => 'not-started',
        'contracting_status' => 'quoted',
        'quoted_price' => 18000.00,
        'sub_fees_estimated_expenses' => 3000.00,
        'securitygauge_crime_risk' => 'High',
        'property_manager_name' => 'Susan Import Manager',
        'property_manager_email' => 'susan@import.com',
        'macro_client' => 'Import Macro Corp',
        'macro_contact' => 'Import Contact Person',
        'macro_email' => 'contact@import.com',
        'notes' => 'Imported via smart import test'
    ],
    [
        'property_name' => 'Riverside Complex - Import Test',
        'address' => '789 River Road',
        'city' => 'River City',
        'state' => 'CA',
        'zip' => '90210',
        'management_company' => 'River Management',
        'owner_name' => 'River Property LLC',
        'property_type' => 'midrise',
        'units' => 200,
        'report_status' => 'in-progress',
        'contracting_status' => 'executed',
        'quoted_price' => 25000.00,
        'sub_fees_estimated_expenses' => 4000.00,
        'securitygauge_crime_risk' => 'Medium',
        'regional_manager_name' => 'Tom Regional',
        'regional_manager_email' => 'tom@river.com',
        'notes' => 'Second import test record'
    ]
];

echo "Testing import of " . count($importData) . " records...\n\n";

$imported = 0;
$errors = [];

foreach ($importData as $index => $recordData) {
    try {
        // Add user_id like the import function does
        $recordData['user_id'] = Auth::id();

        // Calculate net profit if both price and expenses are present
        if (isset($recordData['quoted_price']) && isset($recordData['sub_fees_estimated_expenses'])) {
            $recordData['project_net_profit'] = $recordData['quoted_price'] - $recordData['sub_fees_estimated_expenses'];
        }

        echo "Importing record " . ($index + 1) . ": " . $recordData['property_name'] . "\n";

        // Check if record already exists (like the import function does)
        $existing = HB837::where('property_name', 'like', "%{$recordData['property_name']}%")->first();

        if ($existing) {
            echo "  - Updating existing record (ID: {$existing->id})\n";
            $existing->update($recordData);
        } else {
            echo "  - Creating new record\n";
            $created = HB837::create($recordData);
            echo "  - Created with ID: {$created->id}\n";
            $imported++;
        }

        echo "  ✓ Success\n\n";

    } catch (\Exception $e) {
        $errors[] = "Record " . ($index + 1) . ": " . $e->getMessage();
        echo "  ✗ Error: " . $e->getMessage() . "\n\n";
    }
}

$afterCount = HB837::count();
echo "=================================\n";
echo "Import Results:\n";
echo "- Records before: $beforeCount\n";
echo "- Records after: $afterCount\n";
echo "- Records imported: $imported\n";
echo "- Records added: " . ($afterCount - $beforeCount) . "\n";
echo "- Errors: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\nErrors encountered:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}

// Verify the records were created with all fields
echo "\nVerifying imported records:\n";
$importedRecords = HB837::where('notes', 'like', '%import test%')->get();
foreach ($importedRecords as $record) {
    echo "- ID {$record->id}: {$record->property_name}\n";
    echo "  Security Risk: {$record->securitygauge_crime_risk}\n";
    echo "  Property Manager: {$record->property_manager_name}\n";
    echo "  Macro Client: {$record->macro_client}\n";
    echo "  Net Profit: $" . number_format($record->project_net_profit ?? 0, 2) . "\n\n";
}

echo "Test completed!\n";

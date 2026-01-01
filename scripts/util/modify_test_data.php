<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Illuminate\Support\Facades\DB;

echo "=== MODIFYING MANAGER DATA FOR IMPORT TEST ===\n\n";

// Find the properties that would be affected by Q4 import
$properties_to_modify = [
    'Sand Lake Pointe Apartments',
    'Hickory Pointe Apartments', 
    'Wyndham Place Apartments',
    'Wedgewood Apartments',
    'Charleston Place'
];

echo "Properties to modify for testing:\n";
foreach ($properties_to_modify as $prop) {
    echo "- {$prop}\n";
}

echo "\n=== BEFORE MODIFICATION ===\n";

$records = HB837::whereIn('property_name', $properties_to_modify)
    ->select(['id', 'property_name', 'property_manager_name', 'property_manager_email', 'regional_manager_name', 'regional_manager_email'])
    ->orderBy('property_name')
    ->get();

foreach ($records as $record) {
    echo "\nID: {$record->id} - {$record->property_name}\n";
    echo "  PM: " . ($record->property_manager_name ?: 'NULL') . " (" . ($record->property_manager_email ?: 'NULL') . ")\n";
    echo "  RM: " . ($record->regional_manager_name ?: 'NULL') . " (" . ($record->regional_manager_email ?: 'NULL') . ")\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "MODIFYING DATA...\n";
echo str_repeat('=', 80) . "\n";

// Modify each record with different manager data
$modifications = [
    'Sand Lake Pointe Apartments' => [
        'property_manager_name' => 'TEST John Smith',
        'property_manager_email' => 'john.smith.test@example.com',
        'regional_manager_name' => 'TEST Beverly Duda Modified',
        'regional_manager_email' => 'beverly.duda.test@example.com'
    ],
    'Hickory Pointe Apartments' => [
        'property_manager_name' => 'TEST Jane Wilson',
        'property_manager_email' => 'jane.wilson.test@example.com',
        'regional_manager_name' => 'TEST Beverly Duda Alt',
        'regional_manager_email' => 'b.duda.test@example.com'
    ],
    'Wyndham Place Apartments' => [
        'property_manager_name' => 'TEST Mike Johnson',
        'property_manager_email' => 'mike.johnson.test@example.com',
        'regional_manager_name' => 'TEST Renae Rodriguez Modified',
        'regional_manager_email' => 'renae.rodriguez.test@example.com'
    ],
    'Wedgewood Apartments' => [
        'property_manager_name' => 'TEST Sarah Davis',
        'property_manager_email' => 'sarah.davis.test@example.com',
        'regional_manager_name' => 'TEST Renae Rodriguez Alt',
        'regional_manager_email' => 'r.rodriguez.test@example.com'
    ],
    'Charleston Place' => [
        'property_manager_name' => 'TEST Robert Chen',
        'property_manager_email' => 'robert.chen.test@example.com',
        'regional_manager_name' => 'TEST Kevin Wright New',
        'regional_manager_email' => 'kevin.wright.test@example.com'
    ]
];

$updated_count = 0;

foreach ($modifications as $property_name => $changes) {
    $record = HB837::where('property_name', $property_name)->first();
    
    if ($record) {
        echo "\nUpdating: {$property_name} (ID: {$record->id})\n";
        
        foreach ($changes as $field => $new_value) {
            $old_value = $record->{$field};
            echo "  {$field}: '{$old_value}' → '{$new_value}'\n";
        }
        
        $record->update($changes);
        $updated_count++;
        
        echo "  ✅ Updated successfully\n";
    } else {
        echo "\n❌ Property not found: {$property_name}\n";
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "MODIFICATION COMPLETE\n";
echo str_repeat('=', 80) . "\n";

echo "\n=== AFTER MODIFICATION ===\n";

$updated_records = HB837::whereIn('property_name', $properties_to_modify)
    ->select(['id', 'property_name', 'property_manager_name', 'property_manager_email', 'regional_manager_name', 'regional_manager_email', 'updated_at'])
    ->orderBy('property_name')
    ->get();

foreach ($updated_records as $record) {
    echo "\nID: {$record->id} - {$record->property_name}\n";
    echo "  PM: " . ($record->property_manager_name ?: 'NULL') . " (" . ($record->property_manager_email ?: 'NULL') . ")\n";
    echo "  RM: " . ($record->regional_manager_name ?: 'NULL') . " (" . ($record->regional_manager_email ?: 'NULL') . ")\n";
    echo "  Updated: {$record->updated_at}\n";
}

echo "\n=== TEST SETUP COMPLETE ===\n";
echo "Modified {$updated_count} records with TEST manager data.\n";
echo "\nNow when you re-import the Q4 Assessment file, the import should:\n";
echo "1. ✅ Find these records by property name\n";
echo "2. ✅ Detect differences in manager fields\n"; 
echo "3. ✅ Update the records with Q4 data (removing TEST prefixes)\n";
echo "4. ✅ Show actual database changes in updated_at timestamps\n";
echo "5. ✅ Prove that field mapping fixes are working correctly\n";

echo "\nReady for Q4 import test!\n";
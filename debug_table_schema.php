<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the actual table structure
    $columns = Schema::getColumnListing('hb837');

    echo "=== ACTUAL HB837 TABLE COLUMNS ===\n";
    foreach ($columns as $column) {
        echo "✓ $column\n";
    }

    echo "\n=== CHECKING FOR SPECIFIC FIELDS ===\n";
    $requiredFields = ['securitygauge_crime_risk', 'macro_client'];
    foreach ($requiredFields as $field) {
        $exists = in_array($field, $columns);
        $status = $exists ? '✓' : '✗';
        echo "$status $field\n";
    }

    // Check if we have any records and what fields they have
    $recordCount = DB::table('hb837')->count();
    echo "\n=== DATABASE RECORDS ===\n";
    echo "Total HB837 records: $recordCount\n";

    if ($recordCount > 0) {
        $sampleRecord = DB::table('hb837')->first();
        echo "\nSample record has these fields:\n";
        foreach ((array)$sampleRecord as $field => $value) {
            $hasValue = !is_null($value) && $value !== '';
            $status = $hasValue ? '✓' : '○';
            echo "$status $field: " . (is_string($value) ? substr($value, 0, 30) : $value) . "\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

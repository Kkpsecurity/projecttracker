<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\HB837;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing DataTables Column Configuration\n";
echo "=====================================\n\n";

// Simulate authentication
Auth::loginUsingId(1);

// Test what columns are being returned by the controller
$controller = new \App\Http\Controllers\Admin\HB837\HB837Controller();

// Get one record to test the column structure
$testRecord = HB837::first();

if ($testRecord) {
    echo "Testing with record: {$testRecord->property_name}\n\n";

    // Test the column configurations
    $columns = [
        'checkbox' => '<input type="checkbox" class="bulk-checkbox" value="' . $testRecord->id . '">',
        'property_name' => '<strong>' . e($testRecord->property_name) . '</strong><br><small class="text-muted">' . e($testRecord->address) . ', ' . e($testRecord->city) . ', ' . e($testRecord->state) . '</small>',
        'county' => $testRecord->county ?: '<span class="text-muted">Not specified</span>',
        'macro_client' => $testRecord->macro_client ?: '<span class="text-muted">Not assigned</span>',
        'assigned_consultant_id' => $testRecord->consultant ? $testRecord->consultant->first_name . ' ' . $testRecord->consultant->last_name : '<span class="text-muted">Unassigned</span>',
        'scheduled_date_of_inspection' => $testRecord->scheduled_date_of_inspection ? (string)$testRecord->scheduled_date_of_inspection : '<span class="text-muted">Not scheduled</span>',
        'report_status' => $testRecord->report_status ?: '<span class="text-muted">No status</span>',
        'action' => 'Action buttons here'
    ];

    echo "Expected DataTables columns (index => data):\n";
    $index = 0;
    foreach ($columns as $key => $value) {
        echo "  [{$index}] {$key}: " . strip_tags($value) . "\n";
        $index++;
    }

    echo "\nTotal columns: " . count($columns) . "\n";
    echo "Expected column indices: 0-" . (count($columns) - 1) . "\n\n";

    // Test the actual record data structure
    echo "Record data available:\n";
    $fillable = $testRecord->getFillable();
    foreach ($fillable as $field) {
        if (in_array($field, ['checkbox', 'property_name', 'county', 'macro_client', 'assigned_consultant_id', 'scheduled_date_of_inspection', 'report_status', 'action'])) {
            echo "  ✓ {$field}: " . ($testRecord->$field ?? 'null') . "\n";
        }
    }

} else {
    echo "No records found in database to test with.\n";
}

echo "\nColumn mapping verification complete!\n";

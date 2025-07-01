<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;
use App\Models\HB837;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get sample HB837 record to check field structure
    $sampleRecord = HB837::with(['consultant', 'user'])->first();

    if ($sampleRecord) {
        echo "=== SAMPLE HB837 RECORD STRUCTURE ===\n";
        echo "Database fields available:\n";
        foreach ($sampleRecord->getAttributes() as $field => $value) {
            $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
            echo "  $field: " . $displayValue . "\n";
        }

        echo "\n=== EXPECTED DATATABLE COLUMNS ===\n";
        $expectedColumns = [
            'checkbox',
            'property_name',
            'county',
            'securitygauge_crime_risk',
            'macro_client',
            'assigned_consultant_id',
            'scheduled_date_of_inspection',
            'report_status',
            'contracting_status',
            'quoted_price',
            'priority',
            'created_at',
            'action'
        ];

        echo "Columns DataTable expects:\n";
        foreach ($expectedColumns as $column) {
            $hasField = $sampleRecord->offsetExists($column) || $column === 'checkbox' || $column === 'action' || $column === 'priority';
            $status = $hasField ? '✓' : '✗';
            echo "  $status $column\n";
        }

        echo "\n=== MISSING/COMPUTED FIELDS ===\n";
        echo "  priority: Computed by calculatePriorityScore()\n";
        echo "  checkbox: Generated in addColumn()\n";
        echo "  action: Generated in addColumn()\n";

        // Test the controller data transformation
        echo "\n=== CONTROLLER DATA TEST ===\n";
        $request = new Request();
        $request->merge([
            'draw' => 1,
            'start' => 0,
            'length' => 1,
            'search' => ['value' => ''],
            'columns' => []
        ]);

        $controller = new HB837Controller();
        $response = $controller->getTabData($request, 'quoted');
        $jsonContent = $response->getContent();
        $data = json_decode($jsonContent, true);

        if (isset($data['data']) && count($data['data']) > 0) {
            echo "Controller returns these fields:\n";
            foreach (array_keys($data['data'][0]) as $field) {
                echo "  • $field\n";
            }
        }

    } else {
        echo "No HB837 records found in database\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

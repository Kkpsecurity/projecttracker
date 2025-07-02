<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Mock DataTables Response\n";
echo "===============================\n\n";

// Create a mock request for the AJAX endpoint
$request = Request::create('/admin/plots/datatable', 'GET', [
    'start' => 0,
    'length' => 10,
    'draw' => 1,
    'search' => ['value' => '', 'regex' => false],
    'columns' => [
        ['data' => 'id'],
        ['data' => 'plot_name'],
        ['data' => 'address'],
        ['data' => 'coordinates'],
        ['data' => 'hb837'],
        ['data' => 'actions']
    ]
]);
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Test the mock controller method
$controller = new \App\Http\Controllers\Admin\MockPlotsController();

try {
    $response = $controller->datatable($request);

    // Get the JSON content
    $jsonContent = $response->getContent();
    $data = json_decode($jsonContent, true);

    echo "✓ Mock DataTables Response Structure:\n";
    echo "------------------------------------\n";
    echo "Draw: " . $data['draw'] . "\n";
    echo "Records Total: " . $data['recordsTotal'] . "\n";
    echo "Records Filtered: " . $data['recordsFiltered'] . "\n";
    echo "Data Array Length: " . count($data['data']) . "\n\n";

    if (!empty($data['data'])) {
        echo "✓ Sample Record:\n";
        echo "----------------\n";
        $firstRecord = $data['data'][0];

        foreach ($firstRecord as $column => $value) {
            $displayValue = is_string($value) ? substr(strip_tags($value), 0, 50) : $value;
            echo "  {$column}: " . $displayValue . "\n";
        }

        echo "\n✓ Mock data is working correctly!\n";
        echo "\nTo use this temporarily:\n";
        echo "1. Backup your routes/admin.php\n";
        echo "2. Replace PlotsController with MockPlotsController in the datatable route\n";
        echo "3. Test your DataTables frontend\n";
        echo "4. Restore original controller when database is ready\n";

    } else {
        echo "✗ No data in response\n";
    }

} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== MOCK TEST COMPLETED ===\n";

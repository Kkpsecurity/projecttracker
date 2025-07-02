<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Plots DataTables AJAX Response\n";
echo "=====================================\n\n";

// Simulate authentication
Auth::loginUsingId(1);

// Create a mock request for the AJAX endpoint
$request = Request::create('/admin/plots/datatable', 'GET', [
    'start' => 0,
    'length' => 10,
    'draw' => 1
]);
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Test the controller method
$controller = new \App\Http\Controllers\Admin\PlotsController();

try {
    $response = $controller->datatable($request);

    // Get the JSON content
    $jsonContent = $response->getContent();
    $data = json_decode($jsonContent, true);

    echo "AJAX Response Structure:\n";
    echo "------------------------\n";
    echo "Draw: " . ($data['draw'] ?? 'missing') . "\n";
    echo "Records Total: " . ($data['recordsTotal'] ?? 'missing') . "\n";
    echo "Records Filtered: " . ($data['recordsFiltered'] ?? 'missing') . "\n";
    echo "Data Array Length: " . (isset($data['data']) ? count($data['data']) : 'missing') . "\n\n";

    if (isset($data['data']) && !empty($data['data'])) {
        echo "First Record Structure:\n";
        echo "-----------------------\n";
        $firstRecord = $data['data'][0];

        echo "Actual columns: " . implode(', ', array_keys($firstRecord)) . "\n\n";

        foreach ($firstRecord as $column => $value) {
            $displayValue = is_string($value) ? substr(strip_tags($value), 0, 50) : $value;
            echo "  {$column}: " . $displayValue . "\n";
        }

    } else {
        echo "No data returned or empty data array.\n";
        if (isset($data['error'])) {
            echo "Error: " . $data['error'] . "\n";
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";

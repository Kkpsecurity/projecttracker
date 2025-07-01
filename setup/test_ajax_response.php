<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing HB837 DataTables AJAX Response\n";
echo "=====================================\n\n";

// Simulate authentication
Auth::loginUsingId(1);

// Create a mock request for the AJAX endpoint
$request = Request::create('/admin/hb837/data/all', 'GET', [
    'start' => 0,
    'length' => 10,
    'draw' => 1
]);
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Test the controller method
$controller = new \App\Http\Controllers\Admin\HB837\HB837Controller();

try {
    $response = $controller->getTabData($request, 'all');
    $data = $response->getData(true);
    
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
        
        $expectedColumns = [
            'checkbox',
            'property_name', 
            'county',
            'macro_client',
            'assigned_consultant_id',
            'scheduled_date_of_inspection',
            'report_status',
            'action'
        ];
        
        echo "Expected columns: " . implode(', ', $expectedColumns) . "\n";
        echo "Actual columns:   " . implode(', ', array_keys($firstRecord)) . "\n\n";
        
        foreach ($expectedColumns as $index => $column) {
            $exists = array_key_exists($column, $firstRecord);
            echo "  [{$index}] {$column}: " . ($exists ? "✓ Present" : "✗ Missing") . "\n";
            if ($exists) {
                $value = strip_tags($firstRecord[$column]);
                echo "      Value: " . (strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value) . "\n";
            }
        }
        
        // Check for any extra columns
        $extraColumns = array_diff(array_keys($firstRecord), $expectedColumns);
        if (!empty($extraColumns)) {
            echo "\nExtra columns found: " . implode(', ', $extraColumns) . "\n";
        }
        
    } else {
        echo "No data returned or empty data array.\n";
    }
    
} catch (\Exception $e) {
    echo "Error testing AJAX endpoint: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nAJAX endpoint test completed!\n";

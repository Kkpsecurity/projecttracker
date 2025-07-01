<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Create a fake request for the quoted tab
    $request = new Request();
    $request->merge([
        'draw' => 1,
        'start' => 0,
        'length' => 10,
        'search' => ['value' => ''],
        'columns' => []
    ]);

    // Create controller instance
    $controller = new HB837Controller();

    // Call the getTabData method for quoted tab
    $response = $controller->getTabData($request, 'quoted');

    // Get the JSON content
    $jsonContent = $response->getContent();
    $data = json_decode($jsonContent, true);

    echo "=== QUOTED TAB DEBUG ===\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Records Total: " . ($data['recordsTotal'] ?? 'undefined') . "\n";
    echo "Records Filtered: " . ($data['recordsFiltered'] ?? 'undefined') . "\n";
    echo "Data Count: " . (isset($data['data']) ? count($data['data']) : 'undefined') . "\n";
    echo "\n";

    if (isset($data['data']) && count($data['data']) > 0) {
        echo "First Record Keys: " . implode(', ', array_keys($data['data'][0])) . "\n";
        echo "\n";
        echo "First Record Data:\n";
        foreach ($data['data'][0] as $key => $value) {
            $displayValue = is_string($value) ? substr($value, 0, 100) : $value;
            echo "  $key: " . $displayValue . "\n";
        }
    } else {
        echo "No data returned\n";
        echo "Full response:\n";
        echo $jsonContent . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

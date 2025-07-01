<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test all tabs
    $tabs = ['all', 'active', 'quoted', 'completed', 'closed'];

    foreach ($tabs as $tab) {
        echo "\n=== TESTING {$tab} TAB ===\n";

        // Create a fake request for the tab
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

        // Call the getTabData method
        $response = $controller->getTabData($request, $tab);

        // Get the JSON content
        $jsonContent = $response->getContent();
        $data = json_decode($jsonContent, true);

        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Records Total: " . ($data['recordsTotal'] ?? 'undefined') . "\n";
        echo "Records Filtered: " . ($data['recordsFiltered'] ?? 'undefined') . "\n";
        echo "Data Count: " . (isset($data['data']) ? count($data['data']) : 'undefined') . "\n";

        if (isset($data['data']) && count($data['data']) > 0) {
            echo "✓ Tab working - has data\n";
        } else {
            echo "⚠ Tab empty or not working\n";
            if (isset($data['error'])) {
                echo "Error: " . $data['error'] . "\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

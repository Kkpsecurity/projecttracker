<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing HB837 Index Method Stats\n";
echo "================================\n\n";

// Simulate authentication
Auth::loginUsingId(1);

// Create a mock request
$request = Request::create('/admin/hb837', 'GET');

// Test the controller index method
$controller = new HB837Controller();

try {
    // Call the index method
    $response = $controller->index($request, 'all');

    if ($response instanceof \Illuminate\View\View) {
        $viewData = $response->getData();

        echo "View Data Passed to Template:\n";
        echo "-----------------------------\n";

        if (isset($viewData['stats'])) {
            echo "Stats array found:\n";
            foreach ($viewData['stats'] as $key => $value) {
                echo "  {$key}: {$value}\n";
            }
        } else {
            echo "❌ No 'stats' variable found in view data!\n";
        }

        if (isset($viewData['tab'])) {
            echo "\nCurrent tab: {$viewData['tab']}\n";
        }

        echo "\nAll view variables:\n";
        echo "-------------------\n";
        foreach ($viewData as $key => $value) {
            $type = is_array($value) ? 'array[' . count($value) . ']' : gettype($value);
            echo "  {$key}: {$type}\n";
        }

    } else {
        echo "❌ Expected View response, got: " . get_class($response) . "\n";
    }

} catch (\Exception $e) {
    echo "❌ Error testing index method: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nIndex method test completed!\n";

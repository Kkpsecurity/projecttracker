<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing HB837 Dashboard Response\n";
echo "================================\n\n";

try {
    // Simulate authentication
    Auth::loginUsingId(1);

    // Create a request to the HB837 index
    $request = Request::create('/admin/hb837', 'GET');

    // Instantiate controller and call index method
    $controller = new HB837Controller();
    $response = $controller->index($request);

    // Extract the view data
    $viewData = $response->getData();
    $stats = $viewData['stats'];

    echo "✓ Dashboard Stats Retrieved Successfully:\n";
    echo "-----------------------------------------\n";
    echo "Active Projects: {$stats['active']}\n";
    echo "Quoted Projects: {$stats['quoted']}\n";
    echo "Completed Projects: {$stats['completed']}\n";
    echo "Closed Projects: {$stats['closed']}\n";
    echo "Total Projects: {$stats['total']}\n\n";

    echo "✓ These numbers should match your dashboard cards!\n";
    echo "\nIf your dashboard still shows different numbers:\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Check if you're looking at the correct page\n";
    echo "3. Verify the page URL is /admin/hb837\n";

    // Log the results for monitoring
    \Illuminate\Support\Facades\Log::info('HB837 Dashboard Stats Test', [
        'timestamp' => now()->toDateTimeString(),
        'stats' => $stats,
        'test_result' => 'SUCCESS'
    ]);

    echo "\n📊 Results logged to storage/logs/laravel.log for monitoring\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    // Log the error
    \Illuminate\Support\Facades\Log::error('HB837 Dashboard Stats Test Failed', [
        'timestamp' => now()->toDateTimeString(),
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

echo "\n=== TEST COMPLETED ===\n";

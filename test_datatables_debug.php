<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "DataTables Ajax Error Diagnostic\n";
echo "===============================\n\n";

// Test 1: Check if DataTables package is installed
echo "1. Checking DataTables Package...\n";
try {
    if (function_exists('datatables')) {
        echo "✓ DataTables helper function exists\n";
    } else {
        echo "✗ DataTables helper function NOT found\n";
        echo "   Run: composer require yajra/laravel-datatables-oracle\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking DataTables: " . $e->getMessage() . "\n";
}

// Test 2: Check if route exists
echo "\n2. Checking Routes...\n";
try {
    $routeExists = Route::has('admin.plots.datatable');
    echo $routeExists ? "✓ admin.plots.datatable route exists\n" : "✗ admin.plots.datatable route NOT found\n";
} catch (Exception $e) {
    echo "✗ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 3: Check database connection
echo "\n3. Checking Database Connection...\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✓ Database connection successful\n";

    // Check if plots table exists
    $tablesExist = \Illuminate\Support\Facades\Schema::hasTable('plots');
    echo $tablesExist ? "✓ Plots table exists\n" : "✗ Plots table NOT found\n";

    if ($tablesExist) {
        $plotCount = \App\Models\Plot::count();
        echo "✓ Plot records: {$plotCount}\n";
    }

} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "   This is likely the main cause of the DataTables error\n";
}

// Test 4: Try to instantiate controller
echo "\n4. Checking Controller...\n";
try {
    $controller = new \App\Http\Controllers\Admin\PlotsController();
    echo "✓ PlotsController instantiated successfully\n";
} catch (Exception $e) {
    echo "✗ Controller instantiation failed: " . $e->getMessage() . "\n";
}

// Test 5: Check views
echo "\n5. Checking Views...\n";
$viewPath = resource_path('views/admin/plots/partials/actions.blade.php');
if (file_exists($viewPath)) {
    echo "✓ Actions partial view exists\n";
} else {
    echo "✗ Actions partial view NOT found at: {$viewPath}\n";
    echo "   This will cause the DataTables rendering to fail\n";
}

// Test 6: Simulate the exact DataTables request
echo "\n6. Simulating DataTables Request...\n";
if (function_exists('datatables')) {
    try {
        // Mock authentication
        Auth::loginUsingId(1);

        $request = Request::create('/admin/plots/datatable', 'GET', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'columns' => [
                ['data' => 'id', 'searchable' => false],
                ['data' => 'plot_name', 'searchable' => true],
                ['data' => 'address', 'searchable' => true],
                ['data' => 'coordinates', 'searchable' => false],
                ['data' => 'hb837', 'searchable' => true],
                ['data' => 'actions', 'searchable' => false, 'orderable' => false]
            ],
            'order' => [['column' => 0, 'dir' => 'asc']],
            'search' => ['value' => '', 'regex' => false]
        ]);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $controller = new \App\Http\Controllers\Admin\PlotsController();
        $response = $controller->datatable($request);

        echo "✓ DataTables method executed successfully\n";
        echo "Response status: " . $response->getStatusCode() . "\n";

        $content = $response->getContent();
        $data = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✓ Valid JSON response\n";
            echo "Draw: " . ($data['draw'] ?? 'missing') . "\n";
            echo "Records Total: " . ($data['recordsTotal'] ?? 'missing') . "\n";
            echo "Records Filtered: " . ($data['recordsFiltered'] ?? 'missing') . "\n";
        } else {
            echo "✗ Invalid JSON response\n";
            echo "JSON Error: " . json_last_error_msg() . "\n";
            echo "Raw response: " . substr($content, 0, 200) . "...\n";
        }

    } catch (Exception $e) {
        echo "✗ DataTables request failed: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
} else {
    echo "✗ Skipping - DataTables not available\n";
}

echo "\n=== DIAGNOSTIC COMPLETED ===\n";
echo "\nLikely Solutions:\n";
echo "1. Start your database server (PostgreSQL)\n";
echo "2. Run: composer require yajra/laravel-datatables-oracle (if not installed)\n";
echo "3. Create missing view files\n";
echo "4. Run: php artisan migrate (if tables don't exist)\n";

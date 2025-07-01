<?php
// Quick route test for HB837 DataTables

echo "=== HB837 Route Diagnostics ===\n";

try {
    // Test if Laravel is working
    require_once __DIR__ . '/../../../vendor/autoload.php';
    $app = require_once __DIR__ . '/../../../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "✓ Laravel bootstrapped successfully\n";

    // Test route generation
    $routes = [
        'admin.hb837.index',
        'admin.hb837.data.tab',
        'admin.hb837.smart-import.show'
    ];

    foreach ($routes as $route) {
        try {
            if ($route === 'admin.hb837.data.tab') {
                $url = route($route, 'active');
            } else {
                $url = route($route);
            }
            echo "✓ Route '{$route}': {$url}\n";
        } catch (Exception $e) {
            echo "✗ Route '{$route}': ERROR - {$e->getMessage()}\n";
        }
    }

    // Test controller method
    try {
        $controller = new App\Http\Controllers\Admin\HB837\HB837Controller();
        echo "✓ Controller instantiated successfully\n";

        // Test if method exists
        if (method_exists($controller, 'getTabData')) {
            echo "✓ getTabData method exists\n";
        } else {
            echo "✗ getTabData method missing\n";
        }
    } catch (Exception $e) {
        echo "✗ Controller error: {$e->getMessage()}\n";
    }

} catch (Exception $e) {
    echo "✗ Bootstrap error: {$e->getMessage()}\n";
}

echo "\n=== Test Complete ===\n";

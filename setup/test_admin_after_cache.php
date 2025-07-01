<?php
// Test Admin Center after cache clear
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "🔍 Testing Admin Center with pt_test database...\n\n";

try {
    // Test database connection
    $userCount = \Illuminate\Support\Facades\DB::table('users')->count();
    echo "✅ Database connection: $userCount users found\n";

    // Test if routes are registered
    $router = app('router');
    $routes = $router->getRoutes();

    $adminRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->getName() ?? '', 'admin.') === 0) {
            $adminRoutes[] = $route->getName();
        }
    }

    echo "🔍 Found " . count($adminRoutes) . " admin routes:\n";
    foreach ($adminRoutes as $route) {
        echo "   - $route\n";
    }

    if (empty($adminRoutes)) {
        echo "❌ No admin routes found. Checking route files...\n";

        // Check if admin.php exists
        $adminRouteFile = __DIR__ . '/routes/admin.php';
        if (file_exists($adminRouteFile)) {
            echo "✅ routes/admin.php exists\n";
        } else {
            echo "❌ routes/admin.php missing\n";
        }

        // Check web.php for include
        $webRouteFile = __DIR__ . '/routes/web.php';
        $webContent = file_get_contents($webRouteFile);
        if (strpos($webContent, 'admin.php') !== false) {
            echo "✅ routes/web.php includes admin.php\n";
        } else {
            echo "❌ routes/web.php does not include admin.php\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test completed!\n";

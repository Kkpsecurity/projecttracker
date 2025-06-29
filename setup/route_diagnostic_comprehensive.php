<?php
/**
 * Comprehensive Route Diagnostic Tool
 * Tests all menu routes to ensure they point to correct controllers and views
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== COMPREHENSIVE ROUTE DIAGNOSTIC ===\n";
echo "Testing all menu routes for controller and view integrity\n";
echo "=========================================\n\n";

// Get all routes
$routes = Route::getRoutes();
$routeCollection = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name) {
        $routeCollection[$name] = [
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName(),
            'middleware' => implode(',', $route->gatherMiddleware())
        ];
    }
}

// Define menu routes from AdminLTE config
$menuRoutes = [
    'dashboard' => 'Dashboard',
    'admin.users.index' => 'User Management',
    'admin.settings.index' => 'System Settings',
    'admin.logs.index' => 'Activity Logs',
    'hb837.index' => 'HB837 Projects',
    'consultants.index' => 'Consultants',
    'dashboard.analytics' => 'Analytics',
    'account.dashboard' => 'Account Dashboard',
    'account.settings' => 'Profile Settings',
    'account.security' => 'Security Settings'
];

$results = [];
$totalTests = 0;
$passedTests = 0;

echo "🔍 TESTING MENU ROUTES\n";
echo "=====================\n\n";

foreach ($menuRoutes as $routeName => $menuLabel) {
    $totalTests++;
    echo "Testing: $menuLabel ($routeName)\n";
    echo str_repeat('-', 50) . "\n";
    
    $testResult = [
        'route_name' => $routeName,
        'menu_label' => $menuLabel,
        'route_exists' => false,
        'controller_exists' => false,
        'method_exists' => false,
        'view_exists' => false,
        'route_url' => null,
        'controller_class' => null,
        'controller_method' => null,
        'issues' => []
    ];
    
    // Test 1: Route exists
    if (isset($routeCollection[$routeName])) {
        $testResult['route_exists'] = true;
        $testResult['route_url'] = $routeCollection[$routeName]['uri'];
        echo "✅ Route exists: /{$routeCollection[$routeName]['uri']}\n";
        
        // Parse controller and method
        $action = $routeCollection[$routeName]['action'];
        if (strpos($action, '@') !== false) {
            list($controller, $method) = explode('@', $action);
            $testResult['controller_class'] = $controller;
            $testResult['controller_method'] = $method;
            
            // Test 2: Controller file exists
            $controllerFile = app_path() . '/' . str_replace(['App\\', '\\'], ['', '/'], $controller) . '.php';
            if (file_exists($controllerFile)) {
                $testResult['controller_exists'] = true;
                echo "✅ Controller exists: $controller\n";
                
                // Test 3: Controller method exists
                if (class_exists($controller)) {
                    if (method_exists($controller, $method)) {
                        $testResult['method_exists'] = true;
                        echo "✅ Method exists: $method\n";
                        
                        // Test 4: Try to determine view
                        $viewPath = guessViewPath($controller, $method);
                        if ($viewPath && view()->exists($viewPath)) {
                            $testResult['view_exists'] = true;
                            echo "✅ View exists: $viewPath\n";
                        } else {
                            $testResult['issues'][] = "View not found or cannot be determined";
                            echo "⚠️  View status unclear\n";
                        }
                    } else {
                        $testResult['issues'][] = "Controller method '$method' not found";
                        echo "❌ Method not found: $method\n";
                    }
                } else {
                    $testResult['issues'][] = "Controller class '$controller' not found";
                    echo "❌ Controller class not found: $controller\n";
                }
            } else {
                $testResult['issues'][] = "Controller file not found: $controllerFile";
                echo "❌ Controller file not found: $controllerFile\n";
            }
        } else {
            $testResult['issues'][] = "Invalid action format: $action";
            echo "❌ Invalid action format: $action\n";
        }
    } else {
        $testResult['issues'][] = "Route '$routeName' not found";
        echo "❌ Route not found: $routeName\n";
    }
    
    // Test URL generation
    try {
        $url = route($routeName);
        echo "🔗 Generated URL: $url\n";
    } catch (Exception $e) {
        $testResult['issues'][] = "URL generation failed: " . $e->getMessage();
        echo "❌ URL generation failed: " . $e->getMessage() . "\n";
    }
    
    // Calculate overall status
    $isWorking = $testResult['route_exists'] && 
                 $testResult['controller_exists'] && 
                 $testResult['method_exists'];
    
    if ($isWorking) {
        $passedTests++;
        echo "✅ OVERALL STATUS: WORKING\n";
    } else {
        echo "❌ OVERALL STATUS: ISSUES FOUND\n";
    }
    
    $results[] = $testResult;
    echo "\n";
}

// Summary Report
echo "\n=== SUMMARY REPORT ===\n";
echo "Total routes tested: $totalTests\n";
echo "Working routes: $passedTests\n";
echo "Problematic routes: " . ($totalTests - $passedTests) . "\n\n";

echo "🔍 DETAILED ISSUES:\n";
echo "==================\n";
foreach ($results as $result) {
    if (!empty($result['issues'])) {
        echo "❌ {$result['menu_label']} ({$result['route_name']}):\n";
        foreach ($result['issues'] as $issue) {
            echo "   - $issue\n";
        }
        echo "\n";
    }
}

echo "\n🔍 ROUTE AVAILABILITY CHECK:\n";
echo "============================\n";
echo "Available routes in the system:\n";
$availableRoutes = array_keys($routeCollection);
sort($availableRoutes);
foreach ($availableRoutes as $route) {
    if (strpos($route, 'admin') !== false || 
        strpos($route, 'dashboard') !== false || 
        strpos($route, 'hb837') !== false || 
        strpos($route, 'consultant') !== false ||
        strpos($route, 'account') !== false) {
        echo "✅ $route\n";
    }
}

echo "\n🎯 RECOMMENDATIONS:\n";
echo "==================\n";

// Recommendations for missing routes
$missingRoutes = [];
foreach ($menuRoutes as $routeName => $menuLabel) {
    if (!isset($routeCollection[$routeName])) {
        $missingRoutes[] = $routeName;
    }
}

if (!empty($missingRoutes)) {
    echo "Missing routes to create:\n";
    foreach ($missingRoutes as $route) {
        echo "- $route\n";
    }
    echo "\n";
}

// Check for alternative routes
echo "Alternative routes that might work:\n";
foreach ($availableRoutes as $route) {
    if (strpos($route, 'home') !== false || strpos($route, 'dashboard') !== false) {
        echo "- $route (could be used for Dashboard)\n";
    }
}

echo "\n=== ROUTE DIAGNOSTIC COMPLETE ===\n";

/**
 * Guess the view path based on controller and method
 */
function guessViewPath($controller, $method) {
    // Remove App\Http\Controllers\ prefix and convert to view path
    $path = str_replace(['App\\Http\\Controllers\\', '\\'], ['', '.'], $controller);
    $path = strtolower($path);
    
    // Convert method to view name
    $viewMethod = $method;
    if ($method === 'index') {
        $viewMethod = 'index';
    } elseif ($method === 'show') {
        $viewMethod = 'show';
    } elseif ($method === 'create') {
        $viewMethod = 'create';
    } elseif ($method === 'edit') {
        $viewMethod = 'edit';
    }
    
    return $path . '.' . $viewMethod;
}

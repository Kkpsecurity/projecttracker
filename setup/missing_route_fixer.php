<?php
/**
 * Missing Route Fixer
 * Identifies and suggests fixes for missing menu routes
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== MISSING ROUTE ANALYSIS ===\n";
echo "Checking for missing routes and suggesting fixes\n";
echo "===============================================\n\n";

// Get all routes
$routes = Route::getRoutes();
$routeCollection = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name) {
        $routeCollection[$name] = [
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName()
        ];
    }
}

// Define required menu routes
$requiredRoutes = [
    'dashboard' => [
        'label' => 'Dashboard',
        'expected_uri' => 'dashboard',
        'expected_controller' => 'DashboardController@index'
    ],
    'admin.settings.index' => [
        'label' => 'System Settings',
        'expected_uri' => 'admin/settings',
        'expected_controller' => 'Admin\\SettingsController@index'
    ],
    'admin.users.index' => [
        'label' => 'User Management',
        'expected_uri' => 'admin/users',
        'expected_controller' => 'Admin\\UserController@index'
    ],
    'admin.logs.index' => [
        'label' => 'Activity Logs',
        'expected_uri' => 'admin/logs',
        'expected_controller' => 'Admin\\LogController@index'
    ],
    'hb837.index' => [
        'label' => 'HB837 Projects',
        'expected_uri' => 'hb837',
        'expected_controller' => 'HB837Controller@index'
    ],
    'consultants.index' => [
        'label' => 'Consultants',
        'expected_uri' => 'consultants',
        'expected_controller' => 'ConsultantController@index'
    ]
];

echo "🔍 CHECKING REQUIRED ROUTES:\n";
echo "============================\n\n";

$missingRoutes = [];
$workingRoutes = [];

foreach ($requiredRoutes as $routeName => $config) {
    echo "Checking: {$config['label']} ($routeName)\n";
    
    if (isset($routeCollection[$routeName])) {
        echo "✅ Route exists: /{$routeCollection[$routeName]['uri']}\n";
        echo "   Action: {$routeCollection[$routeName]['action']}\n";
        $workingRoutes[] = $routeName;
    } else {
        echo "❌ Route missing: $routeName\n";
        echo "   Expected URI: /{$config['expected_uri']}\n";
        echo "   Expected Controller: {$config['expected_controller']}\n";
        $missingRoutes[] = $routeName;
        
        // Check for similar routes
        $similarRoutes = [];
        foreach ($routeCollection as $existingRoute => $details) {
            if (strpos($existingRoute, basename($routeName)) !== false) {
                $similarRoutes[] = $existingRoute;
            }
        }
        
        if (!empty($similarRoutes)) {
            echo "   🔍 Similar routes found:\n";
            foreach ($similarRoutes as $similar) {
                echo "     - $similar ({$routeCollection[$similar]['uri']})\n";
            }
        }
    }
    echo "\n";
}

echo "📊 SUMMARY:\n";
echo "===========\n";
echo "Working routes: " . count($workingRoutes) . "/" . count($requiredRoutes) . "\n";
echo "Missing routes: " . count($missingRoutes) . "\n\n";

if (!empty($workingRoutes)) {
    echo "✅ WORKING ROUTES:\n";
    foreach ($workingRoutes as $route) {
        echo "- $route ({$requiredRoutes[$route]['label']})\n";
    }
    echo "\n";
}

if (!empty($missingRoutes)) {
    echo "❌ MISSING ROUTES:\n";
    foreach ($missingRoutes as $route) {
        echo "- $route ({$requiredRoutes[$route]['label']})\n";
    }
    echo "\n";
    
    echo "🔧 SUGGESTED ROUTE ADDITIONS FOR web.php:\n";
    echo "=========================================\n\n";
    
    foreach ($missingRoutes as $route) {
        $config = $requiredRoutes[$route];
        echo "// {$config['label']}\n";
        
        if ($route === 'dashboard') {
            echo "Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');\n";
        } elseif ($route === 'hb837.index') {
            echo "Route::get('/hb837', [HB837Controller::class, 'index'])->name('hb837.index');\n";
        } elseif ($route === 'consultants.index') {
            echo "Route::get('/consultants', [ConsultantController::class, 'index'])->name('consultants.index');\n";
        } elseif ($route === 'admin.logs.index') {
            echo "Route::get('/admin/logs', [Admin\\LogController::class, 'index'])->name('admin.logs.index');\n";
        }
        echo "\n";
    }
}

// Check existing alternative routes
echo "🔍 ALTERNATIVE ROUTES AVAILABLE:\n";
echo "================================\n";
$dashboardAlternatives = [];
$hb837Alternatives = [];
$consultantAlternatives = [];

foreach ($routeCollection as $routeName => $details) {
    if (strpos($routeName, 'dashboard') !== false || strpos($routeName, 'home') !== false) {
        $dashboardAlternatives[] = "$routeName ({$details['uri']})";
    }
    if (strpos($routeName, 'hb837') !== false) {
        $hb837Alternatives[] = "$routeName ({$details['uri']})";
    }
    if (strpos($routeName, 'consultant') !== false) {
        $consultantAlternatives[] = "$routeName ({$details['uri']})";
    }
}

if (!empty($dashboardAlternatives)) {
    echo "Dashboard alternatives:\n";
    foreach (array_slice($dashboardAlternatives, 0, 5) as $alt) {
        echo "- $alt\n";
    }
    echo "\n";
}

if (!empty($hb837Alternatives)) {
    echo "HB837 alternatives:\n";
    foreach (array_slice($hb837Alternatives, 0, 5) as $alt) {
        echo "- $alt\n";
    }
    echo "\n";
}

if (!empty($consultantAlternatives)) {
    echo "Consultant alternatives:\n";
    foreach (array_slice($consultantAlternatives, 0, 5) as $alt) {
        echo "- $alt\n";
    }
    echo "\n";
}

echo "=== MISSING ROUTE ANALYSIS COMPLETE ===\n";

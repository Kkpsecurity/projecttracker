<?php
/**
 * AI-Powered Menu System Route Diagnostic
 * Tests all menu routes to ensure they're working correctly
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "🤖 AI-POWERED MENU SYSTEM DIAGNOSTIC\n";
echo "=====================================\n\n";

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "🔍 TESTING ADMIN MENU ROUTES\n";
    echo "============================\n\n";

    // Define all admin menu routes to test
    $menuRoutes = [
        'Dashboard' => [
            'route' => 'admin',
            'url' => 'admin',
            'controller' => 'HomeController@index'
        ],
        'Home/Dashboard' => [
            'route' => 'admin.home.index',
            'url' => 'admin/home',
            'controller' => 'HomeController@index'
        ],
        'HB837 Module' => [
            'route' => 'admin.hb837.index',
            'url' => 'admin/hb837',
            'controller' => 'Admin\HB837\HB837Controller@index'
        ],
        'HB837 Backup' => [
            'route' => 'admin.hb837.backup.dashboard',
            'url' => 'admin/hb837/backup',
            'controller' => 'Admin\Services\BackupController@dashboard'
        ],
        'Consultants' => [
            'route' => 'admin.consultants.index',
            'url' => 'admin/consultants',
            'controller' => 'Admin\Consultants\ConsultantController@index'
        ],
        'Map/Plots' => [
            'route' => 'admin.mapplots.index',
            'url' => 'admin/mapplots',
            'controller' => 'Admin\HB837\GoogleMapsController@index'
        ],
        'System Settings' => [
            'route' => 'admin.settings.index',
            'url' => 'admin/settings',
            'controller' => 'Admin\SettingsController@index'
        ],
        'User Management' => [
            'route' => 'admin.users.index',
            'url' => 'admin/users',
            'controller' => 'Admin\UserController@index'
        ],
        'Activity Logs' => [
            'route' => 'admin.logs.index',
            'url' => 'admin/logs',
            'controller' => null // This might be a placeholder
        ]
    ];

    $passedTests = 0;
    $failedTests = 0;
    $issues = [];

    foreach ($menuRoutes as $menuName => $routeInfo) {
        echo "Testing: {$menuName}\n";
        echo str_repeat('-', 40) . "\n";
        
        try {
            // Test 1: Check if route exists
            if (\Illuminate\Support\Facades\Route::has($routeInfo['route'])) {
                echo "✅ Route '{$routeInfo['route']}' exists\n";
                
                // Test 2: Get route details
                $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName($routeInfo['route']);
                $actualController = $route->getActionName();
                $actualUri = $route->uri();
                
                echo "   URI: {$actualUri}\n";
                echo "   Controller: {$actualController}\n";
                
                // Test 3: Generate URL
                try {
                    $generatedUrl = route($routeInfo['route']);
                    echo "   Generated URL: {$generatedUrl}\n";
                    echo "✅ URL generation successful\n";
                } catch (\Exception $e) {
                    echo "❌ URL generation failed: {$e->getMessage()}\n";
                    $issues[] = "{$menuName}: URL generation failed";
                    $failedTests++;
                    continue;
                }
                
                // Test 4: Check controller exists (if specified)
                if ($routeInfo['controller']) {
                    $controllerParts = explode('@', $actualController);
                    $controllerClass = $controllerParts[0];
                    
                    if (class_exists($controllerClass)) {
                        echo "✅ Controller class exists\n";
                    } else {
                        echo "❌ Controller class missing: {$controllerClass}\n";
                        $issues[] = "{$menuName}: Controller missing";
                        $failedTests++;
                        continue;
                    }
                    
                    // Test 5: Check method exists
                    if (isset($controllerParts[1])) {
                        $method = $controllerParts[1];
                        if (method_exists($controllerClass, $method)) {
                            echo "✅ Controller method exists: {$method}\n";
                        } else {
                            echo "❌ Controller method missing: {$method}\n";
                            $issues[] = "{$menuName}: Method missing";
                            $failedTests++;
                            continue;
                        }
                    }
                }
                
                // Test 6: Test route resolution
                try {
                    $request = \Illuminate\Http\Request::create($actualUri, 'GET');
                    $resolvedRoute = \Illuminate\Support\Facades\Route::getRoutes()->match($request);
                    echo "✅ Route resolution successful\n";
                } catch (\Exception $e) {
                    echo "❌ Route resolution failed: {$e->getMessage()}\n";
                    $issues[] = "{$menuName}: Route resolution failed";
                    $failedTests++;
                    continue;
                }
                
                echo "🎉 {$menuName}: ALL TESTS PASSED\n";
                $passedTests++;
                
            } else {
                echo "❌ Route '{$routeInfo['route']}' does NOT exist\n";
                $issues[] = "{$menuName}: Route does not exist";
                $failedTests++;
            }
            
        } catch (\Exception $e) {
            echo "💥 Error testing {$menuName}: {$e->getMessage()}\n";
            $issues[] = "{$menuName}: Exception - {$e->getMessage()}";
            $failedTests++;
        }
        
        echo "\n";
    }

    // Summary
    echo "🤖 AI DIAGNOSTIC SUMMARY\n";
    echo "========================\n";
    echo "Total routes tested: " . count($menuRoutes) . "\n";
    echo "✅ Passed: {$passedTests}\n";
    echo "❌ Failed: {$failedTests}\n\n";

    if (!empty($issues)) {
        echo "🚨 ISSUES FOUND:\n";
        foreach ($issues as $issue) {
            echo "   - {$issue}\n";
        }
        echo "\n";
    }

    // Check AdminLTE menu configuration
    echo "🔍 CHECKING ADMINLTE MENU CONFIG\n";
    echo "=================================\n";
    
    $adminlteConfig = config('adminlte.menu');
    if ($adminlteConfig) {
        echo "✅ AdminLTE menu config loaded\n";
        echo "Menu items found: " . count($adminlteConfig) . "\n";
        
        // Check for specific menu items
        $menuItems = [
            'Dashboard',
            'HB837 Management', 
            'Data Management',
            'Admin Center'
        ];
        
        foreach ($menuItems as $item) {
            $found = false;
            foreach ($adminlteConfig as $configItem) {
                if (isset($configItem['text']) && $configItem['text'] === $item) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                echo "✅ Menu item '{$item}' found in config\n";
            } else {
                echo "⚠️  Menu item '{$item}' not found in config\n";
            }
        }
    } else {
        echo "❌ AdminLTE menu config not found\n";
    }

    echo "\n🎯 RECOMMENDATIONS:\n";
    if ($failedTests === 0) {
        echo "🎉 All menu routes are working correctly!\n";
        echo "   - All routes exist and resolve properly\n";
        echo "   - All controllers and methods are present\n";
        echo "   - Menu system should work as expected\n";
    } else {
        echo "⚠️  Some routes need attention:\n";
        echo "   1. Check the failed routes listed above\n";
        echo "   2. Verify controller files exist\n";
        echo "   3. Ensure methods are implemented\n";
        echo "   4. Check route definitions in web.php\n";
    }

} catch (\Exception $e) {
    echo "💥 DIAGNOSTIC ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== AI MENU DIAGNOSTIC COMPLETE ===\n";

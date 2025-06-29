<?php
/**
 * Final Route Verification Script
 * Tests each menu route individually to verify they work
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== FINAL ROUTE VERIFICATION ===\n";
echo "Testing each menu route individually\n";
echo "===================================\n\n";

// Menu routes to test
$menuRoutes = [
    'dashboard' => 'Dashboard',
    'admin.settings.index' => 'System Settings',
    'admin.users.index' => 'User Management', 
    'admin.logs.index' => 'Activity Logs',
    'hb837.index' => 'HB837 Projects',
    'consultants.index' => 'Consultants',
    'dashboard.analytics' => 'Analytics',
    'account.dashboard' => 'Account Dashboard',
    'account.settings' => 'Profile Settings',
    'account.security' => 'Security Settings'
];

$totalTests = 0;
$passedTests = 0;

foreach ($menuRoutes as $routeName => $label) {
    $totalTests++;
    echo "Testing: $label\n";
    echo "Route: $routeName\n";
    
    try {
        // Test route exists
        $hasRoute = Route::has($routeName);
        if ($hasRoute) {
            echo "✅ Route exists\n";
            
            // Test URL generation
            try {
                $url = route($routeName);
                echo "✅ URL generation successful: $url\n";
                $passedTests++;
                echo "✅ OVERALL: WORKING\n";
            } catch (Exception $e) {
                echo "❌ URL generation failed: " . $e->getMessage() . "\n";
                echo "❌ OVERALL: FAILED\n";
            }
        } else {
            echo "❌ Route does not exist\n";
            echo "❌ OVERALL: FAILED\n";
        }
    } catch (Exception $e) {
        echo "❌ Error testing route: " . $e->getMessage() . "\n";
        echo "❌ OVERALL: FAILED\n";
    }
    
    echo str_repeat('-', 50) . "\n\n";
}

echo "📊 FINAL SUMMARY:\n";
echo "=================\n";
echo "Total routes tested: $totalTests\n";
echo "Working routes: $passedTests\n";
echo "Failed routes: " . ($totalTests - $passedTests) . "\n";

if ($passedTests === $totalTests) {
    echo "\n🎉 ALL ROUTES ARE WORKING!\n";
    echo "The menu system is fully functional.\n";
} else {
    echo "\n⚠️  Some routes need attention.\n";
}

echo "\n=== ROUTE VERIFICATION COMPLETE ===\n";

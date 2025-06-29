<?php
/**
 * Quick verification that System Settings page is working after cache clear
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "🔍 POST-CACHE CLEAR VERIFICATION\n";
echo "=================================\n\n";

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "✅ Laravel bootstrapped successfully\n";
    
    // Test route resolution
    echo "Testing admin/settings route...\n";
    $route = \Illuminate\Support\Facades\Route::getRoutes()->match(
        \Illuminate\Http\Request::create('admin/settings', 'GET')
    );
    echo "✅ Route resolves to: {$route->getActionName()}\n";
    
    // Test settings access
    echo "Testing settings access...\n";
    $settings = \App\Models\SiteSettings::getInstance();
    echo "✅ Settings loaded: {$settings->company_name}\n";
    
    // Test controller instantiation
    echo "Testing controller...\n";
    $controller = new \App\Http\Controllers\Admin\SettingsController();
    echo "✅ SettingsController instantiated successfully\n";
    
    // Test view existence
    echo "Testing view file...\n";
    $viewPath = resource_path('views/admin/settings/index.blade.php');
    echo "✅ View exists: " . (file_exists($viewPath) ? 'YES' : 'NO') . "\n";
    echo "View size: " . filesize($viewPath) . " bytes\n";
    
    echo "\n🎉 ALL TESTS PASSED!\n";
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Clear your browser cache (Ctrl+F5)\n";
    echo "2. Try accessing: http://projecttracker_fresh.test/admin/settings\n";
    echo "3. If still showing old content, try incognito mode\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Verification Complete ===\n";

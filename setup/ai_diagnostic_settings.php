<?php
/**
 * AI-Powered System Settings Diagnostic Tool
 * Analyzes why the wrong settings page is being displayed
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "🤖 AI-POWERED SYSTEM SETTINGS DIAGNOSTIC\n";
echo "=========================================\n\n";

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    // AI Analysis Step 1: Environment Configuration
    echo "🔍 AI STEP 1: Environment Analysis\n";
    echo "URL from .env: " . config('app.url') . "\n";
    echo "Environment: " . config('app.env') . "\n";
    echo "Debug mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
    echo "Database: " . config('database.connections.pgsql.database') . "\n\n";

    // AI Analysis Step 2: Route Verification
    echo "🔍 AI STEP 2: Route Analysis\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $settingsRoutes = [];
    
    foreach ($routes as $route) {
        if (str_contains($route->getName() ?? '', 'settings') || 
            str_contains($route->getActionName(), 'Settings')) {
            $settingsRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ];
        }
    }
    
    echo "Found " . count($settingsRoutes) . " settings-related routes:\n";
    foreach ($settingsRoutes as $route) {
        echo "- {$route['method']} {$route['uri']} → {$route['action']} (name: {$route['name']})\n";
    }
    echo "\n";

    // AI Analysis Step 3: Controller Analysis
    echo "🔍 AI STEP 3: Controller File Analysis\n";
    $controllerPath = app_path('Http/Controllers/Admin/SettingsController.php');
    if (file_exists($controllerPath)) {
        echo "✅ SettingsController exists at: {$controllerPath}\n";
        echo "File size: " . filesize($controllerPath) . " bytes\n";
        echo "Modified: " . date('Y-m-d H:i:s', filemtime($controllerPath)) . "\n";
        
        // Check controller methods
        $reflection = new ReflectionClass('App\Http\Controllers\Admin\SettingsController');
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        echo "Public methods: ";
        foreach ($methods as $method) {
            if ($method->class === 'App\Http\Controllers\Admin\SettingsController') {
                echo $method->name . " ";
            }
        }
        echo "\n";
    } else {
        echo "❌ SettingsController NOT FOUND!\n";
    }
    echo "\n";

    // AI Analysis Step 4: View File Analysis
    echo "🔍 AI STEP 4: View File Analysis\n";
    $viewPath = resource_path('views/admin/settings/index.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ Settings view exists at: {$viewPath}\n";
        echo "File size: " . filesize($viewPath) . " bytes\n";
        echo "Modified: " . date('Y-m-d H:i:s', filemtime($viewPath)) . "\n";
        
        // Check for key indicators in the view
        $viewContent = file_get_contents($viewPath);
        if (str_contains($viewContent, 'UPDATED SYSTEM SETTINGS PAGE')) {
            echo "✅ Contains v2.0 updated markers\n";
        }
        if (str_contains($viewContent, 'functionality will be implemented here')) {
            echo "❌ Contains placeholder text - THIS IS THE PROBLEM!\n";
        }
        if (str_contains($viewContent, '$settings->company_name')) {
            echo "✅ Contains live settings variables\n";
        }
    } else {
        echo "❌ Settings view NOT FOUND!\n";
    }
    echo "\n";

    // AI Analysis Step 5: Cache Analysis
    echo "🔍 AI STEP 5: Cache Analysis\n";
    $cacheFiles = [
        'config' => base_path('bootstrap/cache/config.php'),
        'routes' => base_path('bootstrap/cache/routes-v7.php'),
        'services' => base_path('bootstrap/cache/services.php'),
    ];
    
    foreach ($cacheFiles as $type => $file) {
        if (file_exists($file)) {
            echo "⚠️  {$type} cache exists: " . date('Y-m-d H:i:s', filemtime($file)) . "\n";
        } else {
            echo "✅ {$type} cache: not cached\n";
        }
    }
    echo "\n";

    // AI Analysis Step 6: Test Route Resolution
    echo "🔍 AI STEP 6: Route Resolution Test\n";
    try {
        $url = url('admin/settings');
        echo "Generated URL: {$url}\n";
        
        $route = \Illuminate\Support\Facades\Route::getRoutes()->match(
            \Illuminate\Http\Request::create('admin/settings', 'GET')
        );
        echo "Resolved route: {$route->getActionName()}\n";
        echo "Route name: {$route->getName()}\n";
    } catch (\Exception $e) {
        echo "❌ Route resolution failed: {$e->getMessage()}\n";
    }
    echo "\n";

    // AI Analysis Step 7: Database Connection Test
    echo "🔍 AI STEP 7: Database & Settings Test\n";
    try {
        $settingsCount = \App\Models\SiteSettings::count();
        echo "✅ Settings table accessible, {$settingsCount} records found\n";
        
        $instance = \App\Models\SiteSettings::getInstance();
        echo "✅ Settings instance created successfully\n";
        echo "Company name: {$instance->company_name}\n";
    } catch (\Exception $e) {
        echo "❌ Database/Settings error: {$e->getMessage()}\n";
    }
    echo "\n";

    // AI Analysis Step 8: Possible Issues Detection
    echo "🤖 AI DIAGNOSIS SUMMARY\n";
    echo "=======================\n";
    
    $issues = [];
    $solutions = [];
    
    // Check for common issues
    if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
        $issues[] = "Cached routes may be outdated";
        $solutions[] = "Clear route cache: php artisan route:clear";
    }
    
    if (file_exists(base_path('bootstrap/cache/config.php'))) {
        $issues[] = "Cached config may be outdated";
        $solutions[] = "Clear config cache: php artisan config:clear";
    }
    
    // Check for view cache
    $viewCachePath = storage_path('framework/views');
    if (is_dir($viewCachePath)) {
        $cachedViews = glob($viewCachePath . '/*.php');
        if (count($cachedViews) > 0) {
            $issues[] = "Compiled views may be outdated (" . count($cachedViews) . " cached)";
            $solutions[] = "Clear view cache: php artisan view:clear";
        }
    }
    
    if (empty($issues)) {
        echo "✅ No obvious configuration issues detected\n";
        echo "🤔 The issue may be:\n";
        echo "   - Browser cache (try Ctrl+F5)\n";
        echo "   - Multiple Apache/Nginx sites serving different content\n";
        echo "   - Different .env file being loaded\n";
        echo "   - Multiple Laravel installations\n";
    } else {
        echo "⚠️  Potential issues detected:\n";
        foreach ($issues as $issue) {
            echo "   - {$issue}\n";
        }
        echo "\n🔧 Recommended solutions:\n";
        foreach ($solutions as $solution) {
            echo "   - {$solution}\n";
        }
    }

} catch (\Exception $e) {
    echo "💥 DIAGNOSTIC ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Clear all caches using the solutions above\n";
echo "2. Check if you have multiple Laravel installations\n";
echo "3. Verify your web server configuration\n";
echo "4. Try accessing the URL in an incognito window\n";
echo "\n=== AI DIAGNOSTIC COMPLETE ===\n";

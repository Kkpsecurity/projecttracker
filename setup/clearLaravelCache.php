<?php
/**
 * Clear All Laravel Cache - Comprehensive Cache Clearing Script
 * This script clears all Laravel caches and optimizes the application
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "=== Laravel Cache Clearing & Optimization ===\n\n";

try {
    // Initialize Laravel app
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "🧹 Clearing Laravel Caches...\n\n";

    // Clear configuration cache
    echo "1. Clearing config cache...\n";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✓ Config cache cleared\n";

    // Clear route cache
    echo "2. Clearing route cache...\n";
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "   ✓ Route cache cleared\n";

    // Clear view cache
    echo "3. Clearing view cache...\n";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✓ View cache cleared\n";

    // Clear application cache
    echo "4. Clearing application cache...\n";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "   ✓ Application cache cleared\n";

    // Clear compiled classes
    echo "5. Clearing compiled classes...\n";
    \Illuminate\Support\Facades\Artisan::call('clear-compiled');
    echo "   ✓ Compiled classes cleared\n";

    // Clear event cache (Laravel 8+)
    echo "6. Clearing event cache...\n";
    try {
        \Illuminate\Support\Facades\Artisan::call('event:clear');
        echo "   ✓ Event cache cleared\n";
    } catch (\Exception $e) {
        echo "   ⚠ Event cache clear not available (older Laravel version)\n";
    }

    // Clear queue cache
    echo "7. Clearing queue cache...\n";
    try {
        \Illuminate\Support\Facades\Artisan::call('queue:clear');
        echo "   ✓ Queue cache cleared\n";
    } catch (\Exception $e) {
        echo "   ⚠ Queue cache clear not available\n";
    }

    echo "\n🔄 Running Optimizations...\n\n";

    // Dump autoload (composer)
    echo "8. Running composer dump-autoload...\n";
    $composerOutput = [];
    $composerReturn = 0;
    exec('composer dump-autoload --optimize --classmap-authoritative 2>&1', $composerOutput, $composerReturn);
    
    if ($composerReturn === 0) {
        echo "   ✓ Composer autoload optimized\n";
    } else {
        echo "   ⚠ Composer dump-autoload failed:\n";
        foreach ($composerOutput as $line) {
            echo "     " . $line . "\n";
        }
    }

    // Optimize configuration (optional - caches config for production)
    echo "9. Optimizing configuration...\n";
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    echo "   ✓ Configuration optimized\n";

    // Optimize routes (optional - caches routes for production)
    echo "10. Optimizing routes...\n";
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    echo "    ✓ Routes optimized\n";

    // Optimize views (optional - compiles all views)
    echo "11. Optimizing views...\n";
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    echo "    ✓ Views optimized\n";

    echo "\n🎉 All caches cleared and application optimized!\n";
    echo "\n📋 Summary of actions performed:\n";
    echo "   • Config cache cleared and rebuilt\n";
    echo "   • Route cache cleared and rebuilt\n";
    echo "   • View cache cleared and compiled\n";
    echo "   • Application cache cleared\n";
    echo "   • Compiled classes cleared\n";
    echo "   • Event cache cleared (if available)\n";
    echo "   • Queue cache cleared (if available)\n";
    echo "   • Composer autoload optimized\n";
    echo "   • Configuration cached for performance\n";
    echo "   • Routes cached for performance\n";
    echo "   • Views compiled for performance\n";

    echo "\n💡 Note: The application is now optimized for production.\n";
    echo "   If you're in development, you may want to clear caches again\n";
    echo "   when making configuration or route changes.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nTrying alternative approach...\n";
    
    // Fallback to direct artisan calls
    echo "Running artisan commands directly...\n";
    system('php artisan config:clear');
    system('php artisan route:clear');
    system('php artisan view:clear');
    system('php artisan cache:clear');
    system('php artisan clear-compiled');
    system('composer dump-autoload --optimize');
    echo "✓ Cache clearing completed via system commands\n";
}

echo "\n=== Cache Clearing Complete ===\n";


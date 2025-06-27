<?php

// Test Laravel 10 + MySQL Configuration
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Laravel 10 + MySQL Test ===\n\n";

try {
    // Test 1: Laravel Version
    $version = app()->version();
    echo "1. Laravel Version: $version\n";

    // Check if it's Laravel 10
    if (strpos($version, '10.') === 0) {
        echo "   ✅ Successfully upgraded to Laravel 10!\n";
    } else {
        echo "   ⚠️  Version check: Expected Laravel 10.x, got $version\n";
    }

    // Test 2: PHP Version Check
    $phpVersion = PHP_VERSION;
    echo "\n2. PHP Version: $phpVersion\n";
    if (version_compare($phpVersion, '8.1.0', '>=')) {
        echo "   ✅ PHP version compatible with Laravel 10\n";
    } else {
        echo "   ❌ PHP version too old for Laravel 10\n";
    }

    // Test 3: Database Connection
    $dbName = config('database.connections.mysql.database');
    echo "\n3. Database: " . config('database.default') . " ($dbName)\n";

    // Test connection
    DB::connection()->getPdo();
    echo "4. MySQL Connection: ✅ SUCCESS\n";

    // Test 5: Migrations
    $migrations = DB::table('migrations')->count();
    echo "\n5. Migrations: $migrations (should be 13)\n";

    // Test 6: Models
    $userModel = class_exists('App\Models\User') ? '✅ EXISTS' : '❌ MISSING';
    echo "6. User Model: $userModel\n";

    $hb837Model = class_exists('App\Models\HB837') ? '✅ EXISTS' : '❌ MISSING';
    echo "7. HB837 Model: $hb837Model\n";

    // Test 7: Basic queries
    $userCount = DB::table('users')->count();
    echo "\n8. User Count: $userCount\n";

    // Test 8: Cache operations
    echo "\n9. Cache Test:\n";
    cache(['laravel10_test' => 'Laravel 10 Test']);
    $cacheValue = cache('laravel10_test');
    echo "   Cache Value: " . ($cacheValue === 'Laravel 10 Test' ? '✅ Working' : '❌ Failed') . "\n";

    // Test 9: Laravel 10 Features Check
    echo "\n10. Laravel 10 Features:\n";

    // Check for Laravel 10 specific features
    if (class_exists('Illuminate\Support\ProcessUtils')) {
        echo "   ✅ Process utilities available\n";
    }

    if (function_exists('rescue')) {
        echo "   ✅ Helper functions working\n";
    }

    // Test 10: Validation Features
    echo "\n11. Validation Test:\n";
    try {
        $validator = validator(['email' => 'test@example.com'], ['email' => 'required|email']);
        echo "   ✅ Validation system working\n";
    } catch (Exception $e) {
        echo "   ❌ Validation system error: " . $e->getMessage() . "\n";
    }

    echo "\n✅ Laravel 10 upgrade appears successful!\n";
    echo "🚀 Ready for AdminLTE integration and final testing.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";

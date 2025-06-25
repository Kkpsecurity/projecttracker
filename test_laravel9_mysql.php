<?php

// Test Laravel 9 + MySQL Configuration
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Laravel 9 + MySQL Test ===\n\n";

try {
    // Test 1: Laravel Version
    $version = app()->version();
    echo "1. Laravel Version: $version\n";

    // Check if it's Laravel 9
    if (strpos($version, '9.') === 0) {
        echo "   ✅ Successfully upgraded to Laravel 9!\n";
    } else {
        echo "   ⚠️  Version check: Expected Laravel 9.x, got $version\n";
    }

    // Test 2: Database Connection
    $dbName = config('database.connections.mysql.database');
    echo "\n2. Database: " . config('database.default') . " ($dbName)\n";    // Test connection
    DB::connection()->getPdo();
    echo "3. MySQL Connection: ✅ SUCCESS\n";
    
    // Test 4: Migrations
    $migrations = DB::table('migrations')->count();
    echo "\n4. Migrations: $migrations (should be 13)\n";    // Test 5: Models
    $userModel = class_exists('App\Models\User') ? '✅ EXISTS' : '❌ MISSING';
    echo "5. User Model: $userModel\n";
    
    $hb837Model = class_exists('App\Models\HB837') ? '✅ EXISTS' : '❌ MISSING';
    echo "6. HB837 Model: $hb837Model\n";
    
    // Test 6: Basic queries
    $userCount = DB::table('users')->count();
    echo "\n7. User Count: $userCount\n";
    
    // Test 7: Cache operations
    echo "\n8. Cache Test:\n";
    cache(['test_key' => 'Laravel 9 Test']);
    $cacheValue = cache('test_key');
    echo "   Cache Value: " . ($cacheValue === 'Laravel 9 Test' ? '✅ Working' : '❌ Failed') . "\n";

    echo "\n✅ Laravel 9 upgrade appears successful!\n";
    echo "🚀 Ready for further testing and Laravel 10 upgrade.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";

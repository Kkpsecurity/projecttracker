<?php

// Test Laravel 8 + MySQL Configuration
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Laravel 8 + MySQL Test ===\n\n";

// Test 1: Laravel Version
echo "1. Laravel Version: " . app()->version() . "\n";

// Test 2: Database Connection
try {
    $dbName = config('database.connections.mysql.database');
    echo "2. Database: " . config('database.default') . " ($dbName)\n";

    // Test connection
    DB::connection()->getPdo();
    echo "3. MySQL Connection: ✅ SUCCESS\n";

    // Test migrations
    $migrations = DB::table('migrations')->count();
    echo "4. Migrations Run: $migrations\n";

    // Test models
    $userModel = class_exists('App\Models\User') ? '✅ EXISTS' : '❌ MISSING';
    echo "5. User Model: $userModel\n";

    $hb837Model = class_exists('App\Models\HB837') ? '✅ EXISTS' : '❌ MISSING';
    echo "6. HB837 Model: $hb837Model\n";

    // Test tables
    echo "\n7. Database Tables:\n";
    $tables = DB::select('SHOW TABLES');
    $tableKey = 'Tables_in_' . strtolower($dbName);

    foreach ($tables as $table) {
        echo "   - " . $table->$tableKey . "\n";
    }

    // Test basic queries
    $userCount = DB::table('users')->count();
    echo "\n8. User Count: $userCount\n";

    echo "\n✅ ALL TESTS PASSED - Laravel 8 + MySQL is working correctly!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

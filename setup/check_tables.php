<?php
// Check what tables actually exist in the database
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app to use database connection
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

try {
    // Get database connection
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();

    echo "🔍 Checking database tables...\n";

    // Get all tables
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "📊 Found " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }

    // Check specifically for user-related tables
    echo "\n🔍 Looking for user tables:\n";
    foreach ($tables as $table) {
        if (strpos($table, 'user') !== false) {
            echo "   ✅ $table\n";
        }
    }

    // Check for fresh_ prefixed tables
    echo "\n🔍 Looking for fresh_ prefixed tables:\n";
    $freshTables = array_filter($tables, function($table) {
        return strpos($table, 'fresh_') === 0;
    });

    if (empty($freshTables)) {
        echo "   ❌ No fresh_ prefixed tables found\n";
    } else {
        foreach ($freshTables as $table) {
            echo "   ✅ $table\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

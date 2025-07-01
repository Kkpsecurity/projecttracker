<?php
// Check all tables in pt_test database
echo "🔍 Checking all tables in pt_test database...\n";

try {
    // Test raw PDO connection
    $pdo = new PDO(
        'pgsql:host=criustemp.hq.cisadmin.com;port=5432;dbname=pt_test',
        'rclark',
        'Tmaster$101'
    );

    // Get ALL tables
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "📊 Found " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }

    // Check for specific tables we need
    $requiredTables = ['users', 'fresh_users', 'site_settings', 'fresh_site_settings', 'hb837', 'fresh_hb837'];

    echo "\n🔍 Checking for required tables:\n";
    foreach ($requiredTables as $required) {
        if (in_array($required, $tables)) {
            echo "   ✅ $required - EXISTS\n";
        } else {
            echo "   ❌ $required - NOT FOUND\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

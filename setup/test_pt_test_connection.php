<?php
// Test Laravel database connection with pt_test
require_once __DIR__ . '/vendor/autoload.php';

// Manually set environment for testing
$_ENV['DB_DATABASE'] = 'pt_test';
$_ENV['DB_USERNAME'] = 'rclark';
$_ENV['DB_PASSWORD'] = 'Tmaster$101';

try {
    echo "🔍 Testing Laravel DB connection to pt_test...\n";

    // Create a simple PDO connection to test
    $pdo = new PDO(
        'pgsql:host=criustemp.hq.cisadmin.com;port=5432;dbname=pt_test',
        'rclark',
        'Tmaster$101'
    );

    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Users table: " . $result['count'] . " records\n";

    // Test site_settings table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM site_settings");
    $result = $stmt->fetch();
    echo "✅ Site Settings table: " . $result['count'] . " records\n";

    // Test hb837 table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM hb837");
    $result = $stmt->fetch();
    echo "✅ HB837 table: " . $result['count'] . " records\n";

    // Test for verified users (the query that was failing)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE email_verified_at IS NOT NULL");
    $result = $stmt->fetch();
    echo "✅ Verified users: " . $result['count'] . " records\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

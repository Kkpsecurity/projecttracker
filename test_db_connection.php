<?php
// Simple Database Connection Test
echo "🔍 Testing PostgreSQL Connection...\n";

try {
    // Test raw PDO connection
    $pdo = new PDO(
        'pgsql:host=criustemp.hq.cisadmin.com;port=5432;dbname=pt_test',
        'rclark',
        'Tmaster$101'
    );
    
    echo "✅ Raw PDO connection successful!\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if ($result && $result['test'] == 1) {
        echo "✅ Test query successful!\n";
    } else {
        echo "❌ Test query failed!\n";
    }
    
    // Test table existence
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' LIMIT 5");
    $tables = $stmt->fetchAll();
    
    echo "📊 Found " . count($tables) . " tables in database:\n";
    foreach ($tables as $table) {
        echo "   - " . $table['table_name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Database connection test completed successfully!\n";

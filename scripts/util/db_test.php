<?php

// Simple database connection test
require_once __DIR__ . '/../../vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful!\n";
    echo "Database: " . config('database.default') . "\n";
    
    // Test a simple query
    $result = DB::select('SELECT COUNT(*) as count FROM clients');
    echo "Current clients count: " . $result[0]->count . "\n";
    
    $result = DB::select('SELECT COUNT(*) as count FROM users');
    echo "Current users count: " . $result[0]->count . "\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

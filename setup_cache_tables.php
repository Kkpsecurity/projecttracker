<?php
/**
 * Create missing cache table for Laravel database caching
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Cache Table Setup ===\n\n";

try {
    // Initialize Laravel app
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "Checking existing tables...\n";
    
    // Check if cache table exists
    $tables = \Illuminate\Support\Facades\DB::select("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE'
        ORDER BY table_name
    ");
    
    $tableNames = array_map(function($table) {
        return $table->table_name;
    }, $tables);
    
    echo "Current tables: " . implode(', ', $tableNames) . "\n\n";
    
    if (!in_array('cache', $tableNames)) {
        echo "Cache table missing. Creating it...\n";
        
        // Create cache table
        \Illuminate\Support\Facades\DB::statement("
            CREATE TABLE cache (
                key VARCHAR(255) NOT NULL,
                value TEXT NOT NULL,
                expiration INTEGER NOT NULL,
                PRIMARY KEY (key)
            )
        ");
        
        echo "✓ Cache table created successfully!\n";
    } else {
        echo "✓ Cache table already exists\n";
    }
    
    // Check for cache_locks table (used by some cache drivers)
    if (!in_array('cache_locks', $tableNames)) {
        echo "Creating cache_locks table...\n";
        
        \Illuminate\Support\Facades\DB::statement("
            CREATE TABLE cache_locks (
                key VARCHAR(255) NOT NULL,
                owner VARCHAR(255) NOT NULL,
                expiration INTEGER NOT NULL,
                PRIMARY KEY (key)
            )
        ");
        
        echo "✓ Cache locks table created successfully!\n";
    } else {
        echo "✓ Cache locks table already exists\n";
    }
    
    echo "\n✅ Cache tables setup complete!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Setup Complete ===\n";

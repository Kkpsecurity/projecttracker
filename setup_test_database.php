<?php
/**
 * Test Database Setup Script
 * 
 * This script safely creates a test database and copies live data
 * for DataTables testing without affecting production data.
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Setting up test database for DataTables testing...\n\n";

try {
    // Connect to the main database server (without specifying database)
    $prodConfig = [
        'driver' => 'pgsql',
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'database' => 'postgres', // Connect to default database first
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
    ];

    // Create test database if it doesn't exist
    Config::set('database.connections.setup', $prodConfig);
    $setupDb = DB::connection('setup');
    
    echo "📊 Checking if test database exists...\n";
    $testDbExists = $setupDb->select("SELECT 1 FROM pg_database WHERE datname = 'projecttracker_test'");
    
    if (empty($testDbExists)) {
        echo "🔨 Creating test database 'projecttracker_test'...\n";
        $setupDb->statement("CREATE DATABASE projecttracker_test");
        echo "✅ Test database created successfully!\n\n";
    } else {
        echo "ℹ️ Test database already exists.\n\n";
    }

    // Now configure connections for data copy
    $prodConfig['database'] = 'projecttracker';
    $testConfig = $prodConfig;
    $testConfig['database'] = 'projecttracker_test';

    Config::set('database.connections.production', $prodConfig);
    Config::set('database.connections.testing', $testConfig);

    $prodDb = DB::connection('production');
    $testDb = DB::connection('testing');

    echo "📋 Copying database schema and data...\n";

    // Get all tables from production
    $tables = $prodDb->select("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        AND tablename NOT LIKE 'pg_%'
        AND tablename != 'information_schema'
        ORDER BY tablename
    ");

    echo "🔍 Found " . count($tables) . " tables to copy.\n\n";

    foreach ($tables as $table) {
        $tableName = $table->tablename;
        echo "📝 Processing table: $tableName\n";

        try {
            // Get table structure
            $createTableSql = $prodDb->select("
                SELECT 
                    'CREATE TABLE IF NOT EXISTS ' || schemaname||'.'||tablename || 
                    ' (' || array_to_string(array_agg(column_name||' '||type||coalesce('('||character_maximum_length||')','')|| case when is_nullable='NO' then ' NOT NULL' else '' end), ', ') || ');' 
                AS ddl
                FROM (
                    SELECT 
                        schemaname,
                        tablename,
                        column_name,
                        CASE 
                            WHEN data_type = 'character varying' THEN 'varchar'
                            WHEN data_type = 'character' THEN 'char'
                            WHEN data_type = 'timestamp without time zone' THEN 'timestamp'
                            WHEN data_type = 'timestamp with time zone' THEN 'timestamptz'
                            ELSE data_type
                        END as type,
                        character_maximum_length,
                        is_nullable
                    FROM information_schema.columns 
                    WHERE table_schema = 'public' AND table_name = '$tableName'
                    ORDER BY ordinal_position
                ) t
                GROUP BY schemaname, tablename
            ");

            if (!empty($createTableSql)) {
                // Drop and recreate table
                $testDb->statement("DROP TABLE IF EXISTS $tableName CASCADE");
                
                // Use pg_dump for more reliable schema copy
                $dumpCommand = sprintf(
                    'pg_dump -h %s -p %s -U %s -d %s -t %s --schema-only --no-owner --no-privileges',
                    escapeshellarg(env('DB_HOST')),
                    escapeshellarg(env('DB_PORT')),
                    escapeshellarg(env('DB_USERNAME')),
                    escapeshellarg('projecttracker'),
                    escapeshellarg($tableName)
                );
                
                $restoreCommand = sprintf(
                    'psql -h %s -p %s -U %s -d %s',
                    escapeshellarg(env('DB_HOST')),
                    escapeshellarg(env('DB_PORT')),
                    escapeshellarg(env('DB_USERNAME')),
                    escapeshellarg('projecttracker_test')
                );

                // Set PGPASSWORD environment variable
                putenv('PGPASSWORD=' . env('DB_PASSWORD'));
                
                $fullCommand = "$dumpCommand | $restoreCommand";
                exec($fullCommand, $output, $returnCode);
                
                if ($returnCode === 0) {
                    echo "  ✅ Schema copied\n";
                } else {
                    // Fallback to simple CREATE TABLE
                    echo "  ⚠️ Using fallback method for schema\n";
                    $testDb->statement("CREATE TABLE IF NOT EXISTS $tableName (LIKE projecttracker.$tableName INCLUDING ALL)");
                }

                // Copy data
                $rowCount = $prodDb->table($tableName)->count();
                echo "  📊 Copying $rowCount rows...\n";

                if ($rowCount > 0) {
                    // For large tables, copy in chunks
                    if ($rowCount > 1000) {
                        $chunkSize = 500;
                        $chunks = ceil($rowCount / $chunkSize);
                        
                        for ($i = 0; $i < $chunks; $i++) {
                            $offset = $i * $chunkSize;
                            $data = $prodDb->table($tableName)->offset($offset)->limit($chunkSize)->get();
                            
                            if ($data->isNotEmpty()) {
                                $testDb->table($tableName)->insert($data->toArray());
                            }
                            
                            echo "    📦 Chunk " . ($i + 1) . "/$chunks completed\n";
                        }
                    } else {
                        // Small tables - copy all at once
                        $data = $prodDb->table($tableName)->get();
                        if ($data->isNotEmpty()) {
                            $testDb->table($tableName)->insert($data->toArray());
                        }
                    }
                }

                echo "  ✅ Table $tableName copied successfully\n\n";
                
            } else {
                echo "  ⚠️ Could not get schema for $tableName\n\n";
            }

        } catch (Exception $e) {
            echo "  ❌ Error copying table $tableName: " . $e->getMessage() . "\n\n";
        }
    }

    // Copy sequences and reset them
    echo "🔄 Resetting sequences...\n";
    $sequences = $testDb->select("
        SELECT sequence_name 
        FROM information_schema.sequences 
        WHERE sequence_schema = 'public'
    ");

    foreach ($sequences as $sequence) {
        $seqName = $sequence->sequence_name;
        try {
            // Get current value from production
            $currentVal = $prodDb->select("SELECT last_value FROM $seqName");
            if (!empty($currentVal)) {
                $lastValue = $currentVal[0]->last_value;
                $testDb->statement("SELECT setval('$seqName', $lastValue)");
                echo "  ✅ Sequence $seqName reset to $lastValue\n";
            }
        } catch (Exception $e) {
            echo "  ⚠️ Could not reset sequence $seqName: " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 Test database setup completed successfully!\n";
    echo "📊 Database: projecttracker_test\n";
    echo "🔗 Connection: " . env('DB_HOST') . ":" . env('DB_PORT') . "\n";
    echo "👤 Username: " . env('DB_USERNAME') . "\n";
    echo "\n⚠️ IMPORTANT: This is now pointing to the TEST database.\n";
    echo "📝 Remember to switch back to production database when done testing.\n\n";

    // Verify test data
    $testRecordCount = $testDb->table('hb837')->count();
    echo "🔍 Verification: Test database has $testRecordCount HB837 records\n";

} catch (Exception $e) {
    echo "❌ Error setting up test database: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✨ Ready for DataTables testing!\n";

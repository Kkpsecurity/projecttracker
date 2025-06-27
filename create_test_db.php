<?php
/**
 * Simple Test Database Creator
 * Creates a test database safely using Laravel's database connection
 */

// Load environment
$envFile = __DIR__ . '/.env';
$envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$envVars = [];
foreach ($envLines as $line) {
    if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
        [$key, $value] = explode('=', $line, 2);
        $envVars[trim($key)] = trim($value);
    }
}

// Database configuration
$host = $envVars['DB_HOST'];
$port = $envVars['DB_PORT'];
$username = $envVars['DB_USERNAME'];
$password = $envVars['DB_PASSWORD'];
$testDatabase = 'projecttracker_test';

echo "🚀 Creating test database safely...\n";
echo "📊 Host: $host:$port\n";
echo "👤 User: $username\n";
echo "🗄️ Test DB: $testDatabase\n\n";

try {
    // Connect to PostgreSQL default database
    $dsn = "pgsql:host=$host;port=$port;dbname=postgres";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Connected to PostgreSQL server\n";
    
    // Check if test database exists
    $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
    $stmt->execute([$testDatabase]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "ℹ️ Test database already exists\n";
        echo "🗑️ Dropping existing test database for fresh copy...\n";
        
        // Terminate any existing connections to the test database
        $pdo->exec("
            SELECT pg_terminate_backend(pid) 
            FROM pg_stat_activity 
            WHERE datname = '$testDatabase' AND pid != pg_backend_pid()
        ");
        
        $pdo->exec("DROP DATABASE IF EXISTS $testDatabase");
        echo "✅ Existing test database dropped\n";
    }
    
    // Create test database
    echo "🔨 Creating test database...\n";
    $pdo->exec("CREATE DATABASE $testDatabase");
    echo "✅ Test database created successfully!\n\n";
    
    // Now copy data from production to test database
    echo "📋 Copying data from production database...\n";
    
    // Connect to production database
    $prodDsn = "pgsql:host=$host;port=$port;dbname=projecttracker";
    $prodPdo = new PDO($prodDsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Connect to test database
    $testDsn = "pgsql:host=$host;port=$port;dbname=$testDatabase";
    $testPdo = new PDO($testDsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Get all tables
    $stmt = $prodPdo->query("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename
    ");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Found " . count($tables) . " tables to copy\n";
    
    foreach ($tables as $table) {
        echo "  📝 Copying table: $table\n";
        
        // Get table structure
        $createStmt = $prodPdo->query("
            SELECT 
                'CREATE TABLE ' || schemaname||'.'||tablename || ' (' ||
                array_to_string(
                    array_agg(
                        column_name || ' ' || 
                        CASE 
                            WHEN data_type = 'character varying' THEN 'varchar(' || character_maximum_length || ')'
                            WHEN data_type = 'character' THEN 'char(' || character_maximum_length || ')'
                            WHEN data_type = 'integer' THEN 'integer'
                            WHEN data_type = 'bigint' THEN 'bigint'
                            WHEN data_type = 'smallint' THEN 'smallint'
                            WHEN data_type = 'boolean' THEN 'boolean'
                            WHEN data_type = 'text' THEN 'text'
                            WHEN data_type = 'timestamp without time zone' THEN 'timestamp'
                            WHEN data_type = 'timestamp with time zone' THEN 'timestamptz'
                            WHEN data_type = 'date' THEN 'date'
                            WHEN data_type = 'numeric' THEN 'numeric(' || COALESCE(numeric_precision::text, '10') || ',' || COALESCE(numeric_scale::text, '0') || ')'
                            ELSE data_type
                        END ||
                        CASE WHEN is_nullable = 'NO' THEN ' NOT NULL' ELSE '' END
                    ), 
                    ', '
                ) || 
                ');' as create_sql
            FROM information_schema.columns 
            WHERE table_schema = 'public' AND table_name = '$table'
            GROUP BY schemaname, tablename
        ");
        
        $createSql = $createStmt->fetchColumn();
        
        if ($createSql) {
            // Create table structure
            $testPdo->exec($createSql);
            
            // Copy data
            $countStmt = $prodPdo->query("SELECT COUNT(*) FROM $table");
            $rowCount = $countStmt->fetchColumn();
            
            if ($rowCount > 0) {
                echo "    📊 Copying $rowCount rows...\n";
                
                // Get all data
                $dataStmt = $prodPdo->query("SELECT * FROM $table");
                $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    // Get column names
                    $columns = array_keys($rows[0]);
                    $columnsList = implode(', ', $columns);
                    $placeholders = ':' . implode(', :', $columns);
                    
                    $insertSql = "INSERT INTO $table ($columnsList) VALUES ($placeholders)";
                    $insertStmt = $testPdo->prepare($insertSql);
                    
                    foreach ($rows as $row) {
                        $insertStmt->execute($row);
                    }
                }
            }
            
            echo "    ✅ Table $table copied successfully\n";
        } else {
            echo "    ⚠️ Could not get structure for table $table\n";
        }
    }
    
    // Verify the copy
    echo "\n🔍 Verifying test database...\n";
    $testRecordCount = $testPdo->query("SELECT COUNT(*) FROM hb837")->fetchColumn();
    echo "📊 Test database contains $testRecordCount HB837 records\n";
    
    echo "\n🎉 Test database setup completed successfully!\n";
    echo "📊 Database: $testDatabase\n";
    echo "🔗 Connection: $host:$port\n";
    echo "👤 Username: $username\n";
    echo "\n⚠️ IMPORTANT: Your .env file now points to the TEST database.\n";
    echo "📝 Remember to switch back to production database when done testing.\n";
    echo "\n✨ Ready for DataTables testing!\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

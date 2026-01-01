<?php
/**
 * Simple SQL to Laravel Seeder Converter
 * Directly extract and convert PostgreSQL dump data to Laravel seeders
 */

echo "==========================================\n";
echo "  SIMPLE SQL TO SEEDER CONVERTER         \n";
echo "==========================================\n\n";

$sqlFile = __DIR__ . '/../../docs/backup/db.sql';

if (!file_exists($sqlFile)) {
    echo "❌ SQL file not found: {$sqlFile}\n";
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);

echo "=== 1. EXTRACTING CLIENTS DATA ===\n";

// Find the start and end of clients data
$startPattern = 'COPY public.clients';
$startPos = strpos($sqlContent, $startPattern);

if ($startPos === false) {
    echo "❌ Could not find clients data start\n";
    $clients = [];
} else {
    // Find the start of data (after FROM stdin;)
    $dataStart = strpos($sqlContent, "FROM stdin;\n", $startPos);
    $dataStart += strlen("FROM stdin;\n");
    
    // Find the end marker
    $dataEnd = strpos($sqlContent, "\n\\.\n", $dataStart);
    
    if ($dataEnd === false) {
        echo "❌ Could not find clients data end\n";
        $clients = [];
    } else {
        $clientsData = substr($sqlContent, $dataStart, $dataEnd - $dataStart);
        $clientsLines = explode("\n", trim($clientsData));
        
        echo "Found " . count($clientsLines) . " client lines\n";
        
        $clients = [];
        foreach ($clientsLines as $line) {
            if (empty(trim($line))) continue;
            
            $fields = explode("\t", $line);
            
            if (count($fields) >= 17) {
                $clients[] = [
                    'id' => (int)$fields[0],
                    'client_name' => $fields[1] === '\\N' ? null : str_replace(['\\r\\n', '\\r', '\\n'], ["\r\n", "\r", "\n"], $fields[1]),
                    'project_name' => $fields[2] === '\\N' ? null : str_replace(['\\r\\n', '\\r', '\\n'], ["\r\n", "\r", "\n"], $fields[2]),
                    'poc' => $fields[3] === '\\N' ? null : str_replace(['\\r\\n', '\\r', '\\n'], ["\r\n", "\r", "\n"], $fields[3]),
                    'status' => $fields[4] === '\\N' ? null : $fields[4],
                    'quick_status' => $fields[5] === '\\N' ? null : $fields[5],
                    'description' => $fields[6] === '\\N' ? null : str_replace(['\\r\\n', '\\r', '\\n'], ["\r\n", "\r", "\n"], $fields[6]),
                    'corporate_name' => $fields[7] === '\\N' ? null : $fields[7],
                    'created_at' => $fields[8] === '\\N' ? null : $fields[8],
                    'updated_at' => $fields[9] === '\\N' ? null : $fields[9],
                    'file1' => $fields[10] === '\\N' ? null : $fields[10],
                    'file2' => $fields[11] === '\\N' ? null : $fields[11],
                    'file3' => $fields[12] === '\\N' ? null : $fields[12],
                    'project_services_total' => $fields[13] === '\\N' ? null : (float)$fields[13],
                    'project_expenses_total' => $fields[14] === '\\N' ? null : (float)$fields[14],
                    'final_services_total' => $fields[15] === '\\N' ? null : (float)$fields[15],
                    'final_billing_total' => $fields[16] === '\\N' ? null : (float)$fields[16],
                ];
            }
        }
        
        echo "✅ Parsed " . count($clients) . " client records successfully\n";
    }
}

echo "\n=== 2. EXTRACTING USERS DATA ===\n";

// Find the start and end of users data
$startPattern = 'COPY public.users';
$startPos = strpos($sqlContent, $startPattern);

if ($startPos === false) {
    echo "❌ Could not find users data start\n";
    $users = [];
} else {
    // Find the start of data (after FROM stdin;)
    $dataStart = strpos($sqlContent, "FROM stdin;\n", $startPos);
    $dataStart += strlen("FROM stdin;\n");
    
    // Find the end marker
    $dataEnd = strpos($sqlContent, "\n\\.\n", $dataStart);
    
    if ($dataEnd === false) {
        echo "❌ Could not find users data end\n";
        $users = [];
    } else {
        $usersData = substr($sqlContent, $dataStart, $dataEnd - $dataStart);
        $usersLines = explode("\n", trim($usersData));
        
        echo "Found " . count($usersLines) . " user lines\n";
        
        $users = [];
        foreach ($usersLines as $line) {
            if (empty(trim($line))) continue;
            
            $fields = explode("\t", $line);
            
            if (count($fields) >= 8) {
                $users[] = [
                    'id' => (int)$fields[0],
                    'name' => $fields[1] === '\\N' ? null : $fields[1],
                    'email' => $fields[2] === '\\N' ? null : $fields[2],
                    'email_verified_at' => $fields[3] === '\\N' ? null : $fields[3],
                    'password' => $fields[4] === '\\N' ? null : $fields[4],
                    'remember_token' => $fields[5] === '\\N' ? null : $fields[5],
                    'created_at' => $fields[6] === '\\N' ? null : $fields[6],
                    'updated_at' => $fields[7] === '\\N' ? null : $fields[7],
                ];
            }
        }
        
        echo "✅ Parsed " . count($users) . " user records successfully\n";
    }
}

echo "\n=== 3. GENERATING LARAVEL SEEDER CODE ===\n";

// Generate ClientsTableSeeder
$clientsSeederCode = '<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if clients table already has project data (more than HB837 test records)
        if (DB::table(\'clients\')->count() > 5) {
            $this->command->info(\'Clients table already has project data, skipping seeding\');
            return;
        }

        $this->command->info(\'Seeding clients table with project data...\');

        $clients = ' . var_export($clients, true) . ';

        foreach ($clients as $client) {
            // Skip if client with this ID already exists
            if (!DB::table(\'clients\')->where(\'id\', $client[\'id\'])->exists()) {
                DB::table(\'clients\')->insert($client);
            }
        }

        $this->command->info(\'✅ Seeded \' . count($clients) . \' clients successfully\');
    }
}';

// Generate UsersTableSeeder
$usersSeederCode = '<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if users table already has consultant data (more than test records)
        if (DB::table(\'users\')->count() > 8) {
            $this->command->info(\'Users table already has consultant data, skipping seeding\');
            return;
        }

        $this->command->info(\'Seeding users table with consultant data...\');

        $users = ' . var_export($users, true) . ';

        foreach ($users as $user) {
            // Check if user with this email already exists
            if (!DB::table(\'users\')->where(\'email\', $user[\'email\'])->exists()) {
                DB::table(\'users\')->insert($user);
            }
        }

        $this->command->info(\'✅ Seeded \' . count($users) . \' consultants successfully\');
    }
}';

// Generate main ProjectSeeder
$projectSeederCode = '<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info(\'🏗️  Starting project data seeding...\');
        
        // Seed in proper order (users first, then clients)
        $this->call([
            UsersTableSeeder::class,
            ClientsTableSeeder::class,
        ]);
        
        $this->command->info(\'🎉 Project data seeding completed successfully!\');
        $this->command->info(\'📊 Database now contains full project context for HB837 testing\');
    }
}';

// Write seeder files
$outputDir = __DIR__ . '/../../database/seeds/';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

file_put_contents($outputDir . 'ClientsTableSeeder.php', $clientsSeederCode);
file_put_contents($outputDir . 'UsersTableSeeder.php', $usersSeederCode);
file_put_contents($outputDir . 'ProjectSeeder.php', $projectSeederCode);

echo "✅ Generated ClientsTableSeeder.php (" . count($clients) . " records)\n";
echo "✅ Generated UsersTableSeeder.php (" . count($users) . " records)\n";
echo "✅ Generated ProjectSeeder.php (main seeder)\n";

echo "\n=== 4. SAMPLE DATA PREVIEW ===\n";

if (!empty($clients)) {
    echo "📋 Sample client record:\n";
    $sampleClient = $clients[0];
    foreach ($sampleClient as $key => $value) {
        $displayValue = is_string($value) && strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value;
        echo "├─ {$key}: " . var_export($displayValue, true) . "\n";
    }
}

if (!empty($users)) {
    echo "\n👤 Sample user record:\n";
    $sampleUser = $users[0];
    foreach ($sampleUser as $key => $value) {
        if ($key === 'password') {
            echo "├─ {$key}: [HASHED PASSWORD]\n";
        } else {
            echo "├─ {$key}: " . var_export($value, true) . "\n";
        }
    }
}

echo "\n=== 🚀 NEXT STEPS ===\n";
echo "1. Check database configuration (SQLite vs PostgreSQL)\n";
echo "2. Run: php artisan db:seed --class=ProjectSeeder\n";
echo "3. Verify data in database\n";
echo "4. Continue with HB837 import testing\n";
echo "==========================================\n";

<?php
/**
 * SQL to Laravel Seeder Converter
 * Extracts data from SQL dump and converts to Laravel seeder format
 */

require_once __DIR__ . '/../../vendor/autoload.php';

echo "==========================================\n";
echo "  SQL TO LARAVEL SEEDER CONVERTER        \n";
echo "==========================================\n\n";

$sqlFile = __DIR__ . '/../../docs/backup/db.sql';

if (!file_exists($sqlFile)) {
    echo "❌ SQL file not found: {$sqlFile}\n";
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);

echo "=== 1. EXTRACTING CLIENTS DATA ===\n";

// Extract clients data
preg_match('/COPY public\.clients.*?FROM stdin;\n(.*?)\n\\\.\n/s', $sqlContent, $clientsMatch);

if (isset($clientsMatch[1])) {
    $clientsData = trim($clientsMatch[1]);
    $clientsLines = explode("\n", $clientsData);
    
    echo "Found " . count($clientsLines) . " client records\n";
    
    $clients = [];
    foreach ($clientsLines as $line) {
        if (empty(trim($line))) continue;
        
        // Parse tab-separated values
        $fields = explode("\t", $line);
        
        if (count($fields) >= 17) {
            $clients[] = [
                'id' => (int)$fields[0],
                'client_name' => $fields[1] === '\\N' ? null : str_replace('\\r\\n', "\r\n", $fields[1]),
                'project_name' => $fields[2] === '\\N' ? null : str_replace('\\r\\n', "\r\n", $fields[2]),
                'poc' => $fields[3] === '\\N' ? null : str_replace('\\r\\n', "\r\n", $fields[3]),
                'status' => $fields[4] === '\\N' ? null : $fields[4],
                'quick_status' => $fields[5] === '\\N' ? null : $fields[5],
                'description' => $fields[6] === '\\N' ? null : str_replace('\\r\\n', "\r\n", $fields[6]),
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
    
    echo "Parsed " . count($clients) . " client records successfully\n";
} else {
    echo "❌ Could not extract clients data\n";
    $clients = [];
}

echo "\n=== 2. EXTRACTING USERS DATA ===\n";

// Extract users data
preg_match('/COPY public\.users.*?FROM stdin;\n(.*?)\n\\\.\n/s', $sqlContent, $usersMatch);

if (isset($usersMatch[1])) {
    $usersData = trim($usersMatch[1]);
    $usersLines = explode("\n", $usersData);
    
    echo "Found " . count($usersLines) . " user records\n";
    
    $users = [];
    foreach ($usersLines as $line) {
        if (empty(trim($line))) continue;
        
        // Parse tab-separated values
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
    
    echo "Parsed " . count($users) . " user records successfully\n";
} else {
    echo "❌ Could not extract users data\n";
    $users = [];
}

echo "\n=== 3. GENERATING LARAVEL SEEDER CODE ===\n";

// Generate ClientsTableSeeder
$clientsSeederCode = '<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if clients table already has data
        if (DB::table(\'clients\')->count() > 0) {
            $this->command->info(\'Clients table already has data, skipping seeding\');
            return;
        }

        $this->command->info(\'Seeding clients table...\');

        $clients = ' . var_export($clients, true) . ';

        foreach ($clients as $client) {
            DB::table(\'clients\')->insert($client);
        }

        $this->command->info(\'Seeded \' . count($clients) . \' clients successfully\');
    }
}';

// Generate UsersTableSeeder
$usersSeederCode = '<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if users table already has data (excluding any default admin)
        if (DB::table(\'users\')->count() > 1) {
            $this->command->info(\'Users table already has data, skipping seeding\');
            return;
        }

        $this->command->info(\'Seeding users table...\');

        $users = ' . var_export($users, true) . ';

        foreach ($users as $user) {
            // Check if user with this email already exists
            if (!DB::table(\'users\')->where(\'email\', $user[\'email\'])->exists()) {
                DB::table(\'users\')->insert($user);
            }
        }

        $this->command->info(\'Seeded \' . count($users) . \' users successfully\');
    }
}';

// Generate main ProjectSeeder
$projectSeederCode = '<?php

use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info(\'Starting project data seeding...\');
        
        // Seed in proper order (users first, then clients)
        $this->call([
            UsersTableSeeder::class,
            ClientsTableSeeder::class,
        ]);
        
        $this->command->info(\'Project data seeding completed successfully!\');
    }
}';

// Write seeder files
file_put_contents(__DIR__ . '/../../database/seeds/ClientsTableSeeder.php', $clientsSeederCode);
file_put_contents(__DIR__ . '/../../database/seeds/UsersTableSeeder.php', $usersSeederCode);
file_put_contents(__DIR__ . '/../../database/seeds/ProjectSeeder.php', $projectSeederCode);

echo "✅ Generated ClientsTableSeeder.php (" . count($clients) . " records)\n";
echo "✅ Generated UsersTableSeeder.php (" . count($users) . " records)\n";
echo "✅ Generated ProjectSeeder.php (main seeder)\n";

echo "\n=== 4. SAMPLE DATA PREVIEW ===\n";

if (!empty($clients)) {
    echo "Sample client record:\n";
    $sampleClient = $clients[0];
    foreach ($sampleClient as $key => $value) {
        $displayValue = is_string($value) && strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value;
        echo "├─ {$key}: " . var_export($displayValue, true) . "\n";
    }
}

if (!empty($users)) {
    echo "\nSample user record:\n";
    $sampleUser = $users[0];
    foreach ($sampleUser as $key => $value) {
        if ($key === 'password') {
            echo "├─ {$key}: [HASHED PASSWORD]\n";
        } else {
            echo "├─ {$key}: " . var_export($value, true) . "\n";
        }
    }
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Run: php artisan db:seed --class=ProjectSeeder\n";
echo "2. Verify data in database\n";
echo "3. Update DatabaseSeeder.php if needed\n";
echo "4. Continue with HB837 import testing\n";
echo "==========================================\n";

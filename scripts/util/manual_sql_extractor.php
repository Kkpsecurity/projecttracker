<?php
/**
 * Manual SQL Data Extractor
 * Extract specific lines from the SQL dump
 */

echo "==========================================\n";
echo "  MANUAL SQL DATA EXTRACTOR              \n";
echo "==========================================\n\n";

$sqlFile = __DIR__ . '/../../docs/backup/db.sql';
$lines = file($sqlFile, FILE_IGNORE_NEW_LINES);

echo "Total lines in SQL file: " . count($lines) . "\n\n";

// Extract clients data (approximately lines 280-312)
echo "=== EXTRACTING CLIENTS DATA ===\n";
$clientsStart = null;
$clientsEnd = null;

foreach ($lines as $lineNum => $line) {
    if (strpos($line, 'COPY public.clients') !== false) {
        $clientsStart = $lineNum + 1; // Start after the COPY line
        echo "Found clients start at line: " . ($lineNum + 1) . "\n";
        break;
    }
}

if ($clientsStart !== null) {
    for ($i = $clientsStart; $i < count($lines); $i++) {
        if (trim($lines[$i]) === '\\.') {
            $clientsEnd = $i;
            echo "Found clients end at line: " . ($i + 1) . "\n";
            break;
        }
    }
}

$clients = [];
if ($clientsStart !== null && $clientsEnd !== null) {
    for ($i = $clientsStart; $i < $clientsEnd; $i++) {
        $line = $lines[$i];
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
}

echo "✅ Extracted " . count($clients) . " client records\n";

// Extract users data
echo "\n=== EXTRACTING USERS DATA ===\n";
$usersStart = null;
$usersEnd = null;

foreach ($lines as $lineNum => $line) {
    if (strpos($line, 'COPY public.users') !== false) {
        $usersStart = $lineNum + 1; // Start after the COPY line
        echo "Found users start at line: " . ($lineNum + 1) . "\n";
        break;
    }
}

if ($usersStart !== null) {
    for ($i = $usersStart; $i < count($lines); $i++) {
        if (trim($lines[$i]) === '\\.') {
            $usersEnd = $i;
            echo "Found users end at line: " . ($i + 1) . "\n";
            break;
        }
    }
}

$users = [];
if ($usersStart !== null && $usersEnd !== null) {
    for ($i = $usersStart; $i < $usersEnd; $i++) {
        $line = $lines[$i];
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
}

echo "✅ Extracted " . count($users) . " user records\n";

echo "\n=== GENERATING SEEDERS ===\n";

// Now generate the actual seeder files...
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

if (!empty($clients)) {
    echo "\n📋 Sample client: {$clients[0]['client_name']} - {$clients[0]['project_name']}\n";
}

if (!empty($users)) {
    echo "👤 Sample user: {$users[0]['name']} ({$users[0]['email']})\n";
}

echo "\n=== 🚀 READY TO SEED ===\n";
echo "Run: php artisan db:seed --class=ProjectSeeder\n";
echo "==========================================\n";

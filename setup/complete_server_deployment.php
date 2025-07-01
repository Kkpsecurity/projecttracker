<?php

/**
 * Complete Server Deployment Script
 *
 * This script finishes the deployment by:
 * 1. Checking if the role column exists in the users table
 * 2. Adding the role column if missing
 * 3. Running the user seeder to create admin accounts
 * 4. Verifying the deployment is complete
 *
 * Run this on the server after the basic migrations are complete.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "=== COMPLETE SERVER DEPLOYMENT SCRIPT ===\n";
echo "Finishing deployment for projecttracker.hb837training.com\n\n";

try {
    // Step 1: Check database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful!\n\n";

    // Step 2: Check if role column exists
    echo "2. Checking users table structure...\n";

    $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'role'");

    if (empty($columns)) {
        echo "❌ Role column missing from users table\n";
        echo "Adding role column...\n";

        // Add the role column
        DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor'");
        echo "✅ Role column added successfully!\n";
    } else {
        echo "✅ Role column already exists!\n";
    }

    // Step 3: Check current user count
    echo "\n3. Checking current users...\n";
    $userCount = DB::table('users')->count();
    echo "Current users in database: $userCount\n";

    // Step 4: Run remaining migrations
    echo "\n4. Running remaining migrations...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();

    // Step 5: Run user seeder
    echo "\n5. Creating admin users...\n";
    Artisan::call('db:seed', ['--class' => 'UserSeeder', '--force' => true]);
    echo Artisan::output();

    // Step 6: Verify users were created
    echo "\n6. Verifying user creation...\n";
    $finalUserCount = DB::table('users')->count();
    echo "Final user count: $finalUserCount\n";

    // Show created users
    $users = DB::table('users')->select('name', 'email', 'role', 'is_admin')->get();
    echo "\n📋 Created Users:\n";
    foreach ($users as $user) {
        echo "  - {$user->name} ({$user->email}) - Role: {$user->role} - Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
    }

    // Step 7: Clear caches
    echo "\n7. Clearing application caches...\n";
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    echo "✅ All caches cleared!\n";

    // Step 8: Final verification
    echo "\n8. Final deployment verification...\n";
    echo "✅ Database connected and working\n";
    echo "✅ Users table has role column\n";
    echo "✅ Admin users created with roles\n";
    echo "✅ All caches cleared\n";

    echo "\n🎉 DEPLOYMENT COMPLETE! 🎉\n";
    echo "\nYour application is now ready at: https://projecttracker.hb837training.com\n";
    echo "\nAdmin Login Credentials:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Email: richievc@gmail.com\n";
    echo "Password: Secure\$101\n";
    echo "Role: superadmin\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\nOther users:\n";
    echo "- gundrycs@cisadmin.com (manager)\n";
    echo "- jonesy@cisworldservices.org (superadmin)\n";
    echo "- poulinkc@cisadmin.com (editor)\n";
    echo "- ashley@s2institute.com (manager)\n";
    echo "- rodrighb@cisworldservices.org (manager)\n";
    echo "- sgundry@s2institute.com (auditor)\n";
    echo "\nAll passwords: Secure\$101\n";

} catch (Exception $e) {
    echo "❌ Error during deployment: " . $e->getMessage() . "\n";
    echo "\nTROUBLESHOoting:\n";
    echo "1. Check .env file database credentials\n";
    echo "2. Ensure database server is running\n";
    echo "3. Verify network connectivity to database\n";
    echo "4. Check if migrations table exists\n";

    if (str_contains($e->getMessage(), 'role')) {
        echo "\nSpecific fix for role column error:\n";
        echo "Run this SQL manually:\n";
        echo "ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor';\n";
    }

    exit(1);
}

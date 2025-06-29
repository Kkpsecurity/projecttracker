<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check if role column exists
    $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'role'");
    
    if (empty($columns)) {
        echo "Adding role column to users table...\n";
        DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor'");
        echo "✅ Role column added successfully!\n";
    } else {
        echo "✅ Role column already exists!\n";
    }
    
    // Now update existing users with appropriate roles
    echo "Updating existing users with roles...\n";
    
    // Update specific users with their roles
    DB::table('users')->where('email', 'admin@projecttracker.test')->update(['role' => 'superadmin']);
    DB::table('users')->where('email', 'richievc@gmail.com')->update(['role' => 'superadmin']);
    DB::table('users')->where('email', 'gundrycs@cisadmin.com')->update(['role' => 'manager']);
    DB::table('users')->where('email', 'jonesy@cisworldservices.org')->update(['role' => 'auditor']);
    DB::table('users')->where('email', 'poulinkc@cisadmin.com')->update(['role' => 'editor']);
    
    // Set all users to is_admin = true (this is an admin-only system)
    DB::table('users')->update(['is_admin' => true]);
    
    echo "✅ All users updated with roles and set as admins!\n";
    
    // Show current users
    $users = DB::table('users')->select('name', 'email', 'role', 'is_admin')->get();
    echo "\n📋 Current Users:\n";
    foreach ($users as $user) {
        echo "- {$user->name} ({$user->email}) - Role: {$user->role} - Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Role-based user system setup complete!\n";

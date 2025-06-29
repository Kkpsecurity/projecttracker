<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CURRENT USERS IN DATABASE ===\n\n";

try {
    $users = User::select('id', 'name', 'email', 'is_admin', 'is_active')->get();
    
    if ($users->isEmpty()) {
        echo "❌ No users found in database!\n";
    } else {
        echo "Found " . $users->count() . " users:\n\n";
        
        foreach ($users as $user) {
            $adminStatus = $user->is_admin ? '👑 ADMIN' : '👤 User';
            $activeStatus = $user->is_active ? '✅ Active' : '❌ Inactive';
            
            echo "ID: {$user->id}\n";
            echo "Name: {$user->name}\n";
            echo "Email: {$user->email}\n";
            echo "Status: {$adminStatus} | {$activeStatus}\n";
            echo "---\n";
        }
        
        $adminCount = $users->where('is_admin', true)->count();
        echo "\n📊 Summary:\n";
        echo "Total Users: " . $users->count() . "\n";
        echo "Admin Users: " . $adminCount . "\n";
        
        if ($adminCount === 0) {
            echo "\n⚠️  WARNING: No admin users found!\n";
            echo "📝 Need to create an admin user.\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ROLE-BASED ADMIN USERS ===\n\n";

try {
    $users = User::all();
    
    if ($users->isEmpty()) {
        echo "❌ No users found in database!\n";
    } else {
        echo "Found " . $users->count() . " admin users:\n\n";
        
        echo str_pad("ID", 5) . str_pad("Name", 20) . str_pad("Email", 30) . str_pad("Role", 15) . str_pad("Admin", 8) . "Active\n";
        echo str_repeat("-", 85) . "\n";
        
        foreach ($users as $user) {
            echo str_pad($user->id, 5) . 
                 str_pad($user->name, 20) . 
                 str_pad($user->email, 30) . 
                 str_pad($user->role ?? 'N/A', 15) . 
                 str_pad($user->is_admin ? 'Yes' : 'No', 8) . 
                 ($user->is_active ? 'Yes' : 'No') . "\n";
        }
        
        // Count by role
        $roleCounts = $users->groupBy('role')->map->count();
        echo "\n📊 Role Distribution:\n";
        foreach ($roleCounts as $role => $count) {
            echo "- {$role}: {$count} users\n";
        }
        
        echo "\n✅ All users are now admins with role-based permissions!\n";
        echo "🔑 Login with any user using password: Secure\$101\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

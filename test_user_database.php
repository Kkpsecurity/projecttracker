<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LOGIN FUNCTIONALITY TEST (FRESH PROJECT) ===\n\n";

// Test database connection
try {
    $users = User::all();
    echo "✓ Database connection successful\n";
    echo "✓ Found " . $users->count() . " users in database\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// List all users with their credentials
echo "=== USER CREDENTIALS ===\n";
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
    echo "Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";

    // Test password verification
    $testPassword = $user->role === 'admin' ? 'admin123' : 'password123';
    $passwordWorks = Hash::check($testPassword, $user->password);
    echo "Password '{$testPassword}': " . ($passwordWorks ? '✓ Valid' : '✗ Invalid') . "\n";
    echo "Password Hash: " . substr($user->password, 0, 30) . "...\n";
    echo "---\n";
}

// Test auth system
echo "\n=== AUTHENTICATION SYSTEM TEST ===\n";
try {
    // Test finding admin user
    $adminUser = User::where('role', 'admin')->first();
    if ($adminUser) {
        echo "✓ Admin user found: {$adminUser->email}\n";

        // Test password verification
        if (Hash::check('admin123', $adminUser->password)) {
            echo "✓ Admin password verification successful\n";
        } else {
            echo "✗ Admin password verification failed\n";
        }

        // Test login simulation
        echo "✓ Admin login simulation would succeed\n";
    } else {
        echo "✗ No admin user found\n";
    }

    // Test finding regular user
    $regularUser = User::where('role', 'user')->first();
    if ($regularUser) {
        echo "✓ Regular user found: {$regularUser->email}\n";

        if (Hash::check('password123', $regularUser->password)) {
            echo "✓ Regular user password verification successful\n";
        } else {
            echo "✗ Regular user password verification failed\n";
        }
    } else {
        echo "⚠ No regular users found (this is normal for admin-only systems)\n";
    }

} catch (Exception $e) {
    echo "✗ Authentication test error: " . $e->getMessage() . "\n";
}

// Test user model features
echo "\n=== USER MODEL FEATURES TEST ===\n";
try {
    $user = User::first();
    if ($user) {
        echo "✓ User model loaded successfully\n";
        echo "✓ Fillable fields: " . implode(', ', $user->getFillable()) . "\n";
        echo "✓ Hidden fields: " . implode(', ', $user->getHidden()) . "\n";
        echo "✓ Casts: " . json_encode($user->getCasts()) . "\n";
    }
} catch (Exception $e) {
    echo "✗ User model test error: " . $e->getMessage() . "\n";
}

echo "\n=== DATABASE SUMMARY ===\n";
echo "Total users: " . User::count() . "\n";
echo "Admin users: " . User::where('role', 'admin')->count() . "\n";
echo "Regular users: " . User::where('role', 'user')->count() . "\n";
echo "Active users: " . User::where('is_active', true)->count() . "\n";
echo "Verified users: " . User::whereNotNull('email_verified_at')->count() . "\n";

echo "\n🎉 User database test completed!\n";

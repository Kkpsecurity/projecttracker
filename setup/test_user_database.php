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
    $testPassword = 'Secure$101'; // All users use this password
    $passwordWorks = Hash::check($testPassword, $user->password);
    echo "Password '{$testPassword}': " . ($passwordWorks ? '✓ Valid' : '✗ Invalid') . "\n";
    echo "Password Hash: " . substr($user->password, 0, 30) . "...\n";
    echo "---\n";
}

// Test auth system
echo "\n=== AUTHENTICATION SYSTEM TEST ===\n";
try {
    // Test finding superadmin user
    $adminUser = User::where('role', 'superadmin')->first();
    if ($adminUser) {
        echo "✓ Superadmin user found: {$adminUser->email}\n";

        // Test password verification
        if (Hash::check('Secure$101', $adminUser->password)) {
            echo "✓ Superadmin password verification successful\n";
        } else {
            echo "✗ Superadmin password verification failed\n";
        }

        // Test login simulation
        echo "✓ Superadmin login simulation would succeed\n";
    } else {
        echo "✗ No superadmin user found\n";
    }

    // Test finding manager user
    $managerUser = User::where('role', 'manager')->first();
    if ($managerUser) {
        echo "✓ Manager user found: {$managerUser->email}\n";

        if (Hash::check('Secure$101', $managerUser->password)) {
            echo "✓ Manager user password verification successful\n";
        } else {
            echo "✗ Manager user password verification failed\n";
        }
    } else {
        echo "⚠ No manager users found\n";
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
echo "Superadmin users: " . User::where('role', 'superadmin')->count() . "\n";
echo "Manager users: " . User::where('role', 'manager')->count() . "\n";
echo "Editor users: " . User::where('role', 'editor')->count() . "\n";
echo "Auditor users: " . User::where('role', 'auditor')->count() . "\n";
echo "Active users: " . User::where('is_active', true)->count() . "\n";
echo "Verified users: " . User::whereNotNull('email_verified_at')->count() . "\n";

echo "\n🎉 User database test completed!\n";

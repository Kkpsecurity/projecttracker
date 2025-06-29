<?php

/**
 * Login Debug Script
 * Check users and test login functionality
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Create Laravel application
$app = new Application(realpath(__DIR__));
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Login Debug - Checking Users and Authentication\n";
echo "=============================================\n\n";

try {
    // Check all users
    $users = User::all();
    
    echo "📊 Total Users: " . $users->count() . "\n\n";
    
    if ($users->count() > 0) {
        echo "👥 User Details:\n";
        echo str_pad("ID", 5) . str_pad("Name", 20) . str_pad("Email", 30) . str_pad("Active", 8) . str_pad("Admin", 8) . "Login Count\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($users as $user) {
            echo str_pad($user->id, 5) . 
                 str_pad($user->name, 20) . 
                 str_pad($user->email, 30) . 
                 str_pad($user->is_active ? 'Yes' : 'No', 8) . 
                 str_pad(isset($user->is_admin) && $user->is_admin ? 'Yes' : 'No', 8) . 
                 ($user->login_count ?? 0) . "\n";
        }
    } else {
        echo "❌ No users found in database!\n";
        echo "💡 Run: php artisan db:seed --class=UserSeeder\n\n";
        exit(1);
    }
    
    echo "\n🔐 Testing Password Verification:\n";
    echo str_repeat("-", 40) . "\n";
    
    foreach ($users as $user) {
        // Test common passwords
        $testPasswords = ['password', 'password123', 'admin', 'admin123', 'test123'];
        
        echo "\n👤 Testing user: {$user->email}\n";
        
        $passwordFound = false;
        foreach ($testPasswords as $testPassword) {
            if (Hash::check($testPassword, $user->password)) {
                echo "  ✅ Password '{$testPassword}' works for {$user->email}\n";
                $passwordFound = true;
                break;
            }
        }
        
        if (!$passwordFound) {
            echo "  ❌ None of the test passwords work for {$user->email}\n";
            echo "  🔧 Password hash: " . substr($user->password, 0, 30) . "...\n";
        }
    }
    
    echo "\n🌐 Testing Database Connection:\n";
    echo str_repeat("-", 40) . "\n";
    
    // Test database connection
    $dbName = config('database.connections.pgsql.database');
    $dbHost = config('database.connections.pgsql.host');
    
    echo "✅ Database: {$dbName} on {$dbHost}\n";
    echo "✅ Connection: Working\n";
    
    echo "\n🔑 Authentication Configuration:\n";
    echo str_repeat("-", 40) . "\n";
    
    echo "🔧 Auth Guard: " . config('auth.defaults.guard') . "\n";
    echo "🔧 Auth Provider: " . config('auth.defaults.passwords') . "\n";
    echo "🔧 User Model: " . config('auth.providers.users.model') . "\n";
    echo "🔧 Password Field: password (Laravel default)\n";
    echo "🔧 Email Field: email (Laravel default)\n";
    
    echo "\n📝 Recommendations:\n";
    echo str_repeat("-", 40) . "\n";
    
    if ($users->where('is_active', true)->count() === 0) {
        echo "⚠️  No active users found - activate users with: User::where('email', 'user@email.com')->update(['is_active' => true]);\n";
    }
    
    echo "💡 Try logging in with these credentials:\n";
    foreach ($users->where('is_active', true)->take(3) as $user) {
        echo "   Email: {$user->email} | Try passwords: password, password123, admin\n";
    }
    
    echo "\n🔧 To reset a user's password:\n";
    echo "   User::where('email', 'user@email.com')->update(['password' => Hash::make('newpassword')]);\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🎉 Login debug complete!\n";


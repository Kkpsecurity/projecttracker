<?php
/**
 * Authentication and Login Test Script
 * Tests the login system and authentication routes in projecttracker_fresh
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== AUTHENTICATION & LOGIN TEST ===\n";
echo "Testing authentication system in projecttracker_fresh\n";
echo "====================================================\n\n";

// Test 1: Environment Configuration
echo "1. ENVIRONMENT CONFIGURATION\n";
echo "-----------------------------\n";
echo "APP_NAME: " . config('app.name') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "\n";

// Test 2: Database Configuration
echo "2. DATABASE CONFIGURATION\n";
echo "--------------------------\n";
try {
    $dbConfig = config('database.connections.pgsql');
    echo "DB_CONNECTION: " . config('database.default') . "\n";
    echo "DB_HOST: " . $dbConfig['host'] . "\n";
    echo "DB_PORT: " . $dbConfig['port'] . "\n";
    echo "DB_DATABASE: " . $dbConfig['database'] . "\n";
    echo "DB_USERNAME: " . $dbConfig['username'] . "\n";
    echo "DB_PREFIX: " . ($dbConfig['prefix'] ?? 'none') . "\n";
    echo "✅ Database configuration loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Database configuration error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Database Connection
echo "3. DATABASE CONNECTION TEST\n";
echo "----------------------------\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful\n";
    echo "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Users Table Check
echo "4. USERS TABLE VERIFICATION\n";
echo "----------------------------\n";
try {
    $userCount = DB::table('users')->count();
    echo "✅ Users table exists\n";
    echo "Total users: $userCount\n";
    
    if ($userCount > 0) {
        $sampleUser = DB::table('users')->select('id', 'name', 'email', 'created_at')->first();
        echo "Sample user:\n";
        echo "  - ID: {$sampleUser->id}\n";
        echo "  - Name: {$sampleUser->name}\n";
        echo "  - Email: {$sampleUser->email}\n";
        echo "  - Created: {$sampleUser->created_at}\n";
    }
} catch (Exception $e) {
    echo "❌ Users table error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Authentication Routes
echo "5. AUTHENTICATION ROUTES CHECK\n";
echo "-------------------------------\n";
$authRoutes = [
    'admin.login' => 'Admin Login',
    'admin.logout' => 'Admin Logout',
    'admin.password.request' => 'Password Reset Request',
    'admin.password.reset' => 'Password Reset Form'
];

foreach ($authRoutes as $routeName => $description) {
    try {
        if (Route::has($routeName)) {
            $url = route($routeName);
            echo "✅ $description: $url\n";
        } else {
            echo "❌ $description: Route '$routeName' not found\n";
        }
    } catch (Exception $e) {
        echo "❌ $description: Error - " . $e->getMessage() . "\n";
    }
}
echo "\n";

// Test 6: Authentication Configuration
echo "6. AUTHENTICATION CONFIGURATION\n";
echo "--------------------------------\n";
try {
    $authConfig = config('auth');
    echo "Default guard: " . $authConfig['defaults']['guard'] . "\n";
    echo "Default passwords: " . $authConfig['defaults']['passwords'] . "\n";
    echo "User provider: " . $authConfig['guards']['web']['provider'] . "\n";
    echo "User model: " . $authConfig['providers']['users']['model'] . "\n";
    echo "✅ Authentication configuration loaded\n";
} catch (Exception $e) {
    echo "❌ Authentication configuration error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Session Configuration
echo "7. SESSION CONFIGURATION\n";
echo "-------------------------\n";
try {
    echo "Session driver: " . config('session.driver') . "\n";
    echo "Session lifetime: " . config('session.lifetime') . " minutes\n";
    echo "Session domain: " . config('session.domain') . "\n";
    echo "Session path: " . config('session.path') . "\n";
    echo "✅ Session configuration loaded\n";
} catch (Exception $e) {
    echo "❌ Session configuration error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 8: Check if we can create a simple auth test
echo "8. AUTHENTICATION FUNCTIONALITY TEST\n";
echo "-------------------------------------\n";
try {
    // Test password hashing
    $testPassword = 'test123';
    $hashedPassword = Hash::make($testPassword);
    $passwordCheck = Hash::check($testPassword, $hashedPassword);
    
    echo "✅ Password hashing: " . ($passwordCheck ? 'Working' : 'Failed') . "\n";
    
    // Test auth config
    if (class_exists('App\Models\User')) {
        echo "✅ User model exists: App\Models\User\n";
    } else {
        echo "❌ User model not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Authentication functionality error: " . $e->getMessage() . "\n";
}
echo "\n";

// Summary
echo "=== AUTHENTICATION TEST SUMMARY ===\n";
echo "Project: " . config('app.name') . "\n";
echo "URL: " . config('app.url') . "\n";
echo "Environment: " . config('app.env') . "\n";
echo "Status: Authentication system ready for testing\n\n";

echo "🔍 NEXT STEPS:\n";
echo "1. Test actual login via web interface\n";
echo "2. Verify protected routes require authentication\n";
echo "3. Test logout functionality\n";
echo "4. Confirm session persistence\n\n";

echo "=== AUTHENTICATION TEST COMPLETE ===\n";

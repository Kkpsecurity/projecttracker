<?php

// Debug script to find where the Windows path is being used
echo "=== Path Configuration Debug ===\n";

// Load environment variables first
if (file_exists(__DIR__ . '/.env')) {
    $env = file_get_contents(__DIR__ . '/.env');
    $lines = explode("\n", $env);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

echo "1. Checking storage_path() function...\n";
try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $storagePath = storage_path();
    echo "storage_path() returns: $storagePath\n";

    $logsPath = storage_path('logs');
    echo "storage_path('logs') returns: $logsPath\n";

} catch (Exception $e) {
    echo "Error calling storage_path(): " . $e->getMessage() . "\n";
}

echo "\n2. Checking environment variables...\n";
echo "APP_BASE_PATH: " . ($_ENV['APP_BASE_PATH'] ?? 'not set') . "\n";
echo "__DIR__: " . __DIR__ . "\n";
echo "realpath(__DIR__): " . realpath(__DIR__) . "\n";

echo "\n3. Checking Laravel config...\n";
try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Check base path
    echo "App base path: " . $app->basePath() . "\n";
    echo "App storage path: " . $app->storagePath() . "\n";

} catch (Exception $e) {
    echo "Error checking Laravel config: " . $e->getMessage() . "\n";
}

echo "\n4. Checking filesystem config...\n";
$configFile = __DIR__ . '/config/filesystems.php';
if (file_exists($configFile)) {
    echo "Filesystems config exists\n";
    // Don't include it, just check if it exists
} else {
    echo "Filesystems config not found\n";
}

echo "\n5. Checking logging config...\n";
$loggingFile = __DIR__ . '/config/logging.php';
if (file_exists($loggingFile)) {
    echo "Logging config exists\n";
    $content = file_get_contents($loggingFile);
    if (strpos($content, 'storage_path') !== false) {
        echo "Found storage_path() usage in logging config\n";
    }
} else {
    echo "Logging config not found\n";
}

echo "\nDone.\n";

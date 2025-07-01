<?php
/**
 * Server Deployment Setup Script
 *
 * This script ensures all necessary Laravel directories exist
 * and have proper permissions for live server deployment.
 */

echo "=== Laravel Server Deployment Setup ===\n";

// Get the application root directory
$appRoot = __DIR__;
echo "Application Root: {$appRoot}\n";

// Define required storage directories
$requiredDirs = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

echo "\n=== Creating Required Directories ===\n";
foreach ($requiredDirs as $dir) {
    $fullPath = $appRoot . DIRECTORY_SEPARATOR . $dir;

    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✓ Created: {$dir}\n";
        } else {
            echo "✗ Failed to create: {$dir}\n";
        }
    } else {
        echo "✓ Exists: {$dir}\n";
    }

    // Set permissions (if on Unix-like system)
    if (function_exists('chmod') && !stripos(PHP_OS, 'WIN') === 0) {
        chmod($fullPath, 0755);
    }
}

// Create .gitkeep files to ensure directories are preserved in git
echo "\n=== Creating .gitkeep Files ===\n";
$gitkeepDirs = [
    'storage/app',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs'
];

foreach ($gitkeepDirs as $dir) {
    $gitkeepPath = $appRoot . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . '.gitkeep';
    if (!file_exists($gitkeepPath)) {
        if (file_put_contents($gitkeepPath, '')) {
            echo "✓ Created .gitkeep: {$dir}\n";
        } else {
            echo "✗ Failed to create .gitkeep: {$dir}\n";
        }
    } else {
        echo "✓ .gitkeep exists: {$dir}\n";
    }
}

// Clear Laravel caches
echo "\n=== Clearing Laravel Caches ===\n";
$commands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear',
];

foreach ($commands as $command) {
    echo "Running: php artisan {$command}\n";
    $output = [];
    $returnCode = 0;
    exec("php artisan {$command} 2>&1", $output, $returnCode);

    if ($returnCode === 0) {
        echo "✓ Success: {$command}\n";
    } else {
        echo "✗ Failed: {$command}\n";
        echo "Output: " . implode("\n", $output) . "\n";
    }
}

// Check critical files
echo "\n=== Checking Critical Files ===\n";
$criticalFiles = [
    '.env',
    'artisan',
    'composer.json',
    'bootstrap/app.php',
    'config/app.php',
    'config/database.php',
    'public/index.php'
];

foreach ($criticalFiles as $file) {
    $filePath = $appRoot . DIRECTORY_SEPARATOR . $file;
    if (file_exists($filePath)) {
        echo "✓ Exists: {$file}\n";
    } else {
        echo "✗ Missing: {$file}\n";
    }
}

// Display environment information
echo "\n=== Environment Information ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Operating System: " . PHP_OS . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Current Working Directory: " . getcwd() . "\n";

echo "\n=== Deployment Setup Complete ===\n";
echo "Please upload this entire project to your live server and run this script again on the server.\n";
echo "Make sure to update your .env file with production settings.\n";
?>

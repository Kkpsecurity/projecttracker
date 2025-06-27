<?php

/**
 * Environment Check Script for Laravel Upgrade
 * Run this script to check current environment status
 *
 * Usage: php environment_check.php
 */
echo "🚀 LARAVEL UPGRADE - ENVIRONMENT CHECK\n";
echo "=====================================\n\n";

// PHP Version Check
echo "📋 PHP VERSION:\n";
echo 'Current PHP: '.PHP_VERSION."\n";
$phpMajor = PHP_MAJOR_VERSION;
$phpMinor = PHP_MINOR_VERSION;

if ($phpMajor >= 8 && $phpMinor >= 2) {
    echo "✅ PHP Version: READY for Laravel 11\n";
} elseif ($phpMajor >= 8 && $phpMinor >= 1) {
    echo "⚠️  PHP Version: READY for Laravel 10 (upgrade to 8.2+ recommended)\n";
} else {
    echo "❌ PHP Version: NEEDS UPGRADE (minimum 8.1 required)\n";
}
echo "\n";

// Laravel Version Check
echo "📋 LARAVEL VERSION:\n";
try {
    // Check composer.json for Laravel version
    $composerPath = __DIR__.'/composer.json';
    if (file_exists($composerPath)) {
        $composer = json_decode(file_get_contents($composerPath), true);
        $laravelVersion = $composer['require']['laravel/framework'] ?? 'Unknown';
        echo 'Composer Laravel: '.$laravelVersion."\n";

        if (strpos($laravelVersion, '^7') === 0) {
            echo "⏳ Laravel Version: Ready for upgrade process (Laravel 7)\n";
        } elseif (strpos($laravelVersion, '^8') === 0) {
            echo "🔄 Laravel Version: In upgrade process (8.x)\n";
        } elseif (strpos($laravelVersion, '^9') === 0) {
            echo "🔄 Laravel Version: In upgrade process (9.x)\n";
        } elseif (strpos($laravelVersion, '^10') === 0) {
            echo "🔄 Laravel Version: In upgrade process (10.x)\n";
        } elseif (strpos($laravelVersion, '^11') === 0) {
            echo "✅ Laravel Version: UPGRADE COMPLETE!\n";
        }
    } else {
        echo "❌ composer.json not found\n";
    }
} catch (Exception $e) {
    echo "❌ Laravel Version: Could not detect (error reading composer.json)\n";
}
echo "\n";

// Database Check
echo "📋 DATABASE STATUS:\n";
try {
    $dbPath = __DIR__.'/database/database.sqlite';
    if (file_exists($dbPath)) {
        $size = round(filesize($dbPath) / 1024, 2);
        echo "✅ Database: SQLite file exists ({$size} KB)\n";

        // Check if backup exists
        $backupPath = __DIR__.'/database/database_backup_pre_upgrade.sqlite';
        if (file_exists($backupPath)) {
            echo "✅ Backup: Pre-upgrade backup exists\n";
        } else {
            echo "⚠️  Backup: No pre-upgrade backup found\n";
        }
    } else {
        echo "❌ Database: SQLite file not found\n";
    }
} catch (Exception $e) {
    echo "❌ Database: Error checking database status\n";
}
echo "\n";

// Extensions Check
echo "📋 PHP EXTENSIONS:\n";
$requiredExtensions = [
    'pdo_sqlite' => 'Database (SQLite)',
    'openssl' => 'Encryption',
    'mbstring' => 'String handling',
    'tokenizer' => 'Laravel parsing',
    'xml' => 'XML processing',
    'ctype' => 'Character type checking',
    'json' => 'JSON handling',
    'curl' => 'HTTP requests',
    'zip' => 'File compression',
    'gd' => 'Image processing (for PDF/Excel)',
];

$allExtensionsOk = true;
foreach ($requiredExtensions as $ext => $description) {
    if (extension_loaded($ext)) {
        echo "✅ {$ext}: Loaded\n";
    } else {
        echo "❌ {$ext}: Missing ({$description})\n";
        $allExtensionsOk = false;
    }
}

if ($allExtensionsOk) {
    echo "✅ All required extensions loaded\n";
} else {
    echo "⚠️  Some extensions missing - check Laragon PHP configuration\n";
}
echo "\n";

// Storage Permissions
echo "📋 STORAGE PERMISSIONS:\n";
$storageWritable = is_writable(__DIR__.'/storage');
$logsWritable = is_writable(__DIR__.'/storage/logs');

if ($storageWritable && $logsWritable) {
    echo "✅ Storage: Writable\n";
} else {
    echo "❌ Storage: Permission issues detected\n";
}
echo "\n";

// Memory and Time Limits
echo "📋 PHP CONFIGURATION:\n";
echo 'Memory Limit: '.ini_get('memory_limit')."\n";
echo 'Max Execution Time: '.ini_get('max_execution_time')."s\n";
echo 'Upload Max Filesize: '.ini_get('upload_max_filesize')."\n";
echo 'Post Max Size: '.ini_get('post_max_size')."\n";
echo "\n";

// Overall Status
echo "🎯 OVERALL STATUS:\n";
echo "==================\n";

if ($phpMajor >= 8 && $phpMinor >= 1 && $allExtensionsOk && $storageWritable) {
    echo "✅ ENVIRONMENT: READY for Laravel upgrade!\n";
    echo "📝 Next steps: See docs/LARAGON_NEXT_STEPS.md\n";
} else {
    echo "⚠️  ENVIRONMENT: Needs attention before upgrade\n";
    echo "🔧 Fix the issues above before proceeding\n";
}

echo "\n";
echo '📅 Check completed: '.date('Y-m-d H:i:s')."\n";
echo "🔄 Re-run this script after making environment changes\n";

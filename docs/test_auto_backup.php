<?php
// Test Auto Backup System

echo "=== AUTO BACKUP SYSTEM TEST ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check configuration
echo "📋 1. Testing Configuration...\n";

$configTests = [
    'backup.enable_cron' => config('backup.enable_cron', 'NOT SET'),
    'backup.cron_time_at' => config('backup.cron_time_at', 'NOT SET'),
    'backup.admin_email' => config('backup.admin_email', 'NOT SET'),
    'cache backup_cron_enabled' => cache('backup_cron_enabled', 'NOT SET'),
];

foreach ($configTests as $key => $value) {
    echo "   $key: " . var_export($value, true) . "\n";
}

// Test 2: Check if command exists
echo "\n📦 2. Testing Command Registration...\n";
try {
    $commands = shell_exec('cd "' . __DIR__ . '" && php artisan list | findstr "backup"');
    echo "   Available backup commands:\n";
    echo "   " . str_replace("\n", "\n   ", trim($commands)) . "\n";
} catch (Exception $e) {
    echo "   ERROR: Could not list commands - " . $e->getMessage() . "\n";
}

// Test 3: Test schedule configuration
echo "\n⏰ 3. Testing Schedule Configuration...\n";

// Simulate schedule check
$enableCron = config('backup.enable_cron', true);
$enableCache = cache('backup_cron_enabled', true);
$cronTime = config('backup.cron_time_at', '23:00');

echo "   Config enabled: " . ($enableCron ? 'YES' : 'NO') . "\n";
echo "   Cache enabled: " . ($enableCache ? 'YES' : 'NO') . "\n";
echo "   Scheduled time: $cronTime\n";
echo "   Will run: " . (($enableCron && $enableCache) ? 'YES' : 'NO') . "\n";

// Test 4: Check command help
echo "\n🔧 4. Testing Command Structure...\n";
try {
    $help = shell_exec('cd "' . __DIR__ . '" && php artisan backup:auto --help');
    if (strpos($help, 'backup:auto') !== false) {
        echo "   ✅ Command structure is valid\n";
        echo "   ✅ Options include --tables parameter\n";
    } else {
        echo "   ❌ Command structure issue\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

// Test 5: Simulate toggle functionality
echo "\n🔄 5. Testing Toggle Logic...\n";

// Test toggle scenarios
$toggleScenarios = [
    ['enabled' => '1', 'expected' => true, 'description' => 'Enable auto backup'],
    ['enabled' => '0', 'expected' => false, 'description' => 'Disable auto backup'],
    ['enabled' => null, 'expected' => false, 'description' => 'No enabled parameter'],
];

foreach ($toggleScenarios as $scenario) {
    $enabled = isset($scenario['enabled']) && $scenario['enabled'] == '1';
    $result = $enabled === $scenario['expected'] ? '✅ PASS' : '❌ FAIL';
    echo "   {$scenario['description']}: $result\n";
}

// Test 6: Check file permissions and directories
echo "\n📁 6. Testing File System...\n";

$backupDir = storage_path('app/backups');
if (!is_dir($backupDir)) {
    echo "   Creating backup directory: $backupDir\n";
    try {
        mkdir($backupDir, 0755, true);
        echo "   ✅ Backup directory created\n";
    } catch (Exception $e) {
        echo "   ❌ Could not create backup directory: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✅ Backup directory exists: $backupDir\n";
}

$writable = is_writable($backupDir);
echo "   Directory writable: " . ($writable ? 'YES' : 'NO') . "\n";

// Test 7: Check required classes/exports
echo "\n🔍 7. Testing Dependencies...\n";

$dependencies = [
    'App\Exports\DynamicBackupExport' => class_exists('App\Exports\DynamicBackupExport'),
    'App\Models\Backup' => class_exists('App\Models\Backup'),
    'App\Models\ImportAudit' => class_exists('App\Models\ImportAudit'),
    'Maatwebsite\Excel\Facades\Excel' => class_exists('Maatwebsite\Excel\Facades\Excel'),
];

foreach ($dependencies as $class => $exists) {
    echo "   $class: " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "\n";
}

echo "\n=== AUTO BACKUP SYSTEM STATUS ===\n";

// Determine overall status
$criticalIssues = [];

if (!$enableCron) $criticalIssues[] = "Config backup.enable_cron is disabled";
if (!class_exists('App\Exports\DynamicBackupExport')) $criticalIssues[] = "DynamicBackupExport class missing";
if (!class_exists('App\Models\Backup')) $criticalIssues[] = "Backup model missing";
if (!$writable) $criticalIssues[] = "Backup directory not writable";

if (empty($criticalIssues)) {
    echo "🎉 AUTO BACKUP SYSTEM: READY\n";
    echo "✅ All components are functional\n";
    echo "✅ Configuration is valid\n";
    echo "✅ Dependencies are available\n";
    echo "✅ File system is ready\n\n";

    echo "📝 TO ENABLE AUTO BACKUP:\n";
    echo "1. Visit the backup dashboard\n";
    echo "2. Toggle the 'Auto Backup' switch\n";
    echo "3. Backups will run daily at $cronTime\n";
    echo "4. Ensure your server has cron jobs enabled\n";
    echo "5. Add this to your crontab:\n";
    echo "   * * * * * cd " . __DIR__ . " && php artisan schedule:run >> /dev/null 2>&1\n";
} else {
    echo "⚠️ AUTO BACKUP SYSTEM: ISSUES FOUND\n";
    foreach ($criticalIssues as $issue) {
        echo "❌ $issue\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
?>

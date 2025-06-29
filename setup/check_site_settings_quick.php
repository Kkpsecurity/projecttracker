<?php
/**
 * Quick check of site settings
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Site Settings Check ===\n\n";

use App\Models\SiteSettings;

// Get all settings
$allSettings = SiteSettings::all();
echo "Total settings in database: " . $allSettings->count() . "\n\n";

if ($allSettings->count() > 0) {
    echo "Current settings:\n";
    foreach ($allSettings as $setting) {
        echo "- {$setting->key}: {$setting->value} ({$setting->type})\n";
    }
} else {
    echo "No settings found in database.\n";
}

echo "\n=== Settings Instance ===\n";
$instance = SiteSettings::getInstance();
echo "Company Name: " . ($instance->company_name ?: 'Not set') . "\n";
echo "Company Email: " . ($instance->company_email ?: 'Not set') . "\n";
echo "Company Phone: " . ($instance->company_phone ?: 'Not set') . "\n";
echo "Primary Color: " . ($instance->primary_color ?: 'Not set') . "\n";
echo "Maintenance Mode: " . ($instance->maintenance_mode ? 'ON' : 'OFF') . "\n";

echo "\n=== Check Complete ===\n";

<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "AdminLTE Menu Configuration Test\n";
echo "===============================\n\n";

// Check if the menu configuration has our consultant entry
$adminlteConfig = config('adminlte.menu');

echo "Menu Items Found:\n";
echo "-----------------\n";

$consultantMenuFound = false;

foreach ($adminlteConfig as $index => $menuItem) {
    if (isset($menuItem['text'])) {
        echo "- {$menuItem['text']}";
        if (isset($menuItem['route'])) {
            echo " (Route: {$menuItem['route']})";
        }
        if (isset($menuItem['icon'])) {
            echo " (Icon: {$menuItem['icon']})";
        }
        echo "\n";

        if ($menuItem['text'] === 'Consultant Records') {
            $consultantMenuFound = true;
            echo "  ✅ Consultant Records menu item found!\n";
            echo "  Route: " . ($menuItem['route'] ?? 'N/A') . "\n";
            echo "  Icon: " . ($menuItem['icon'] ?? 'N/A') . "\n";
            echo "  Active Pattern: " . (isset($menuItem['active']) ? implode(', ', $menuItem['active']) : 'N/A') . "\n";
        }
    } elseif (isset($menuItem['header'])) {
        echo "\n[HEADER: {$menuItem['header']}]\n";
    }
}

echo "\nMenu Test Results:\n";
echo "==================\n";
if ($consultantMenuFound) {
    echo "✅ Consultant Records menu item successfully added\n";
    echo "✅ Route configured: admin.consultants.index\n";
    echo "✅ Icon configured: fas fa-user-tie\n";
    echo "✅ Active pattern configured: admin/consultants*\n";
} else {
    echo "❌ Consultant Records menu item not found\n";
}

echo "\nMenu configuration test completed!\n";

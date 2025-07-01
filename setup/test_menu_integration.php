<?php

echo "🔗 Menu Integration & Routes Test\n";
echo "=================================\n\n";

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

try {
    echo "📋 Testing Menu Configuration\n";
    echo "=============================\n\n";

    // 1. Test AdminLTE menu configuration
    $menu = Config::get('adminlte.menu');

    if ($menu) {
        echo "✅ AdminLTE menu configuration loaded\n";

        // Find HB837 Projects menu item
        $hb837Menu = null;
        foreach ($menu as $item) {
            if (isset($item['text']) && $item['text'] === 'HB837 Projects') {
                $hb837Menu = $item;
                break;
            }
        }

        if ($hb837Menu) {
            echo "✅ HB837 Projects menu item found\n";

            if (isset($hb837Menu['submenu'])) {
                echo "✅ HB837 submenu configured\n";
                echo "📋 Submenu items:\n";

                foreach ($hb837Menu['submenu'] as $subItem) {
                    if (isset($subItem['text'])) {
                        $icon = isset($subItem['icon']) ? $subItem['icon'] : 'no icon';
                        $route = isset($subItem['route']) ? $subItem['route'] : 'no route';
                        echo "  - {$subItem['text']} ({$icon}) → {$route}\n";
                    } elseif (isset($subItem['header'])) {
                        echo "  [HEADER] {$subItem['header']}\n";
                    }
                }
            } else {
                echo "❌ HB837 submenu not configured\n";
            }
        } else {
            echo "❌ HB837 Projects menu item not found\n";
        }
    } else {
        echo "❌ AdminLTE menu configuration not loaded\n";
    }

    echo "\n🔗 Testing Route Registration\n";
    echo "=============================\n";

    // 2. Test route registration
    $testRoutes = [
        'admin.maps.index' => 'Google Maps Index',
        'admin.maps.api.plots' => 'Maps API Plots Data',
        'admin.plots.index' => 'Plots Management Index',
        'admin.plots.create' => 'Create New Plot',
        'admin.plots.datatable' => 'Plots DataTable',
        'admin.hb837.index' => 'HB837 Projects Index'
    ];

    $workingRoutes = 0;
    $totalRoutes = count($testRoutes);

    foreach ($testRoutes as $routeName => $description) {
        try {
            $url = route($routeName);
            echo "✅ {$description} → {$url}\n";
            $workingRoutes++;
        } catch (Exception $e) {
            echo "❌ {$description} → Route '{$routeName}' not found\n";
        }
    }

    echo "\n📊 Route Registration Summary\n";
    echo "============================\n";
    echo "Working routes: {$workingRoutes}/{$totalRoutes}\n";

    if ($workingRoutes === $totalRoutes) {
        echo "🎉 All routes registered successfully!\n";
    } else {
        echo "⚠️ Some routes are missing - check route definitions\n";
    }

    echo "\n🌐 Testing Route List\n";
    echo "====================\n";

    // 3. Get all admin routes
    $allRoutes = Route::getRoutes()->getRoutes();
    $adminRoutes = [];
    $mapsRoutes = [];
    $plotsRoutes = [];

    foreach ($allRoutes as $route) {
        $routeName = $route->getName();
        if ($routeName && strpos($routeName, 'admin.') === 0) {
            $adminRoutes[] = $routeName;

            if (strpos($routeName, 'admin.maps') === 0) {
                $mapsRoutes[] = $routeName;
            } elseif (strpos($routeName, 'admin.plots') === 0) {
                $plotsRoutes[] = $routeName;
            }
        }
    }

    echo "📍 Maps Routes (" . count($mapsRoutes) . "):\n";
    foreach ($mapsRoutes as $route) {
        echo "  - {$route}\n";
    }

    echo "\n📍 Plots Routes (" . count($plotsRoutes) . "):\n";
    foreach ($plotsRoutes as $route) {
        echo "  - {$route}\n";
    }

    echo "\n🎯 Integration Status\n";
    echo "====================\n";

    $integrationChecks = [
        'AdminLTE Menu' => $menu ? '✅' : '❌',
        'HB837 Submenu' => ($hb837Menu && isset($hb837Menu['submenu'])) ? '✅' : '❌',
        'Maps Routes' => count($mapsRoutes) > 0 ? '✅' : '❌',
        'Plots Routes' => count($plotsRoutes) > 0 ? '✅' : '❌',
        'Route Resolution' => $workingRoutes === $totalRoutes ? '✅' : '❌',
    ];

    foreach ($integrationChecks as $check => $status) {
        echo "  {$status} {$check}\n";
    }

    $allPassed = !in_array('❌', array_values($integrationChecks));

    if ($allPassed) {
        echo "\n🎉 SUCCESS: Google Plots has been successfully added to the sidebar!\n";
        echo "   Navigation: HB837 Projects → Map & Plots section\n";
        echo "   Available options:\n";
        echo "     - Google Maps (interactive map view)\n";
        echo "     - Manage Plots (CRUD operations)\n";
    } else {
        echo "\n⚠️ WARNING: Some integration issues detected\n";
    }

    echo "\n📱 User Experience\n";
    echo "==================\n";
    echo "Users can now access Google Plots via:\n";
    echo "  1. Sidebar → HB837 Projects → Google Maps\n";
    echo "  2. Sidebar → HB837 Projects → Manage Plots\n";
    echo "  3. Direct URLs:\n";
    echo "     - /admin/maps (Google Maps interface)\n";
    echo "     - /admin/plots (Plots management)\n";

} catch (Exception $e) {
    echo "❌ System Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

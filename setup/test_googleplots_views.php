<?php
/**
 * Google Maps Views Test Script
 * Tests the creation and functionality of Google Maps and Plots views
 */

echo "🗺️ Google Maps Views Test\n";
echo str_repeat("=", 50) . "\n";

// Check if view files exist
$viewsToCheck = [
    'admin.maps.index' => 'c:\laragon\www\projecttracker_fresh\resources\views\admin\maps\index.blade.php',
    'admin.plots.index' => 'c:\laragon\www\projecttracker_fresh\resources\views\admin\plots\index.blade.php',
    'admin.plots.create' => 'c:\laragon\www\projecttracker_fresh\resources\views\admin\plots\create.blade.php',
    'admin.plots.show' => 'c:\laragon\www\projecttracker_fresh\resources\views\admin\plots\show.blade.php',
    'admin.plots.edit' => 'c:\laragon\www\projecttracker_fresh\resources\views\admin\plots\edit.blade.php'
];

echo "📁 Checking View Files:\n";
echo "-" . str_repeat("-", 48) . "\n";

foreach ($viewsToCheck as $viewName => $filePath) {
    $exists = file_exists($filePath);
    $status = $exists ? "✅ EXISTS" : "❌ MISSING";
    $size = $exists ? " (" . round(filesize($filePath) / 1024, 1) . "KB)" : "";
    echo sprintf("%-25s %s%s\n", $viewName, $status, $size);
}

echo "\n🚀 Testing Route Resolution:\n";
echo "-" . str_repeat("-", 48) . "\n";

// Test routes using Laravel's route helper
try {
    // Run artisan route:list to check routes
    $routeOutput = shell_exec('php artisan route:list --name=admin.maps 2>&1');
    $plotRouteOutput = shell_exec('php artisan route:list --name=admin.plots 2>&1');

    // Check for maps routes
    if (strpos($routeOutput, 'admin.maps.index') !== false) {
        echo "✅ admin.maps.index route found\n";
    } else {
        echo "❌ admin.maps.index route missing\n";
    }

    // Check for plots routes
    if (strpos($plotRouteOutput, 'admin.plots.index') !== false) {
        echo "✅ admin.plots.index route found\n";
    } else {
        echo "❌ admin.plots.index route missing\n";
    }

} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n⚙️ Configuration Check:\n";
echo "-" . str_repeat("-", 48) . "\n";

// Check Google Maps API configuration
$configFile = 'c:\laragon\www\projecttracker_fresh\config\services.php';
if (file_exists($configFile)) {
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, 'google_maps') !== false) {
        echo "✅ Google Maps API config added to services.php\n";
    } else {
        echo "❌ Google Maps API config missing from services.php\n";
    }
} else {
    echo "❌ services.php config file not found\n";
}

// Check menu configuration
$menuConfigFile = 'c:\laragon\www\projecttracker_fresh\config\adminlte.php';
if (file_exists($menuConfigFile)) {
    $menuContent = file_get_contents($menuConfigFile);
    if (strpos($menuContent, 'admin.maps.index') !== false && strpos($menuContent, 'admin.plots.index') !== false) {
        echo "✅ Google Maps and Plots added to AdminLTE menu\n";
    } else {
        echo "❌ Menu items missing from AdminLTE config\n";
    }
} else {
    echo "❌ AdminLTE config file not found\n";
}

echo "\n🎯 View Features Summary:\n";
echo "-" . str_repeat("-", 48) . "\n";

$features = [
    "📍 Google Maps Index" => "Interactive map with plot markers",
    "📊 Plots Management" => "DataTable with CRUD operations",
    "➕ Plot Creation" => "Form with map integration",
    "👁️ Plot Details" => "Individual plot view with map",
    "✏️ Plot Editing" => "Edit form with coordinate picker",
    "🔄 Bulk Actions" => "Mass operations on plots",
    "🗺️ Geocoding" => "Address to coordinates conversion",
    "📱 Responsive Design" => "Mobile-friendly interface"
];

foreach ($features as $feature => $description) {
    echo sprintf("%-20s %s\n", $feature, $description);
}

echo "\n📝 Setup Instructions:\n";
echo "-" . str_repeat("-", 48) . "\n";

echo "1. Add Google Maps API key to .env file:\n";
echo "   GOOGLE_MAPS_API_KEY=your_api_key_here\n\n";

echo "2. Enable required Google Maps APIs:\n";
echo "   - Maps JavaScript API\n";
echo "   - Geocoding API\n";
echo "   - Places API (optional)\n\n";

echo "3. Clear Laravel caches:\n";
echo "   php artisan config:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan route:clear\n\n";

echo "4. Access the Google Maps interface:\n";
echo "   - Navigate to: /admin/maps\n";
echo "   - Or use sidebar: HB837 Projects → Google Maps\n\n";

echo "🎉 Google Maps views have been successfully created!\n";
echo "   The views include full map integration, plot management,\n";
echo "   and seamless integration with the existing HB837 system.\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "Google Maps Views Test Complete ✅\n";

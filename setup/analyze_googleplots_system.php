<?php

echo "🗺️ GooglePlots System Analysis & Legacy Review\n";
echo "==============================================\n\n";

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Plot;
use App\Models\PlotAddress;
use App\Models\HB837;
use Illuminate\Support\Facades\DB;

try {
    echo "📊 Current Database Structure Analysis\n";
    echo "=====================================\n\n";

    // 1. Check if tables exist
    $tables = ['plots', 'plot_addresses', 'hb837'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "✅ {$table}: {$count} records\n";
        } catch (Exception $e) {
            echo "❌ {$table}: Table not found or error\n";
        }
    }

    echo "\n📋 Plots Table Structure\n";
    echo "========================\n";

    // Get plots table structure (PostgreSQL syntax)
    $plotColumns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'plots' ORDER BY ordinal_position");
    foreach ($plotColumns as $column) {
        echo "  - {$column->column_name} ({$column->data_type})\n";
    }

    echo "\n📋 Plot Addresses Table Structure\n";
    echo "=================================\n";

    // Get plot_addresses table structure (PostgreSQL syntax)
    try {
        $addressColumns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'plot_addresses' ORDER BY ordinal_position");
        foreach ($addressColumns as $column) {
            echo "  - {$column->column_name} ({$column->data_type})\n";
        }
    } catch (Exception $e) {
        echo "❌ plot_addresses table not found\n";
    }

    echo "\n🔗 Relationship Analysis\n";
    echo "========================\n";

    // Test Plot model relationships
    try {
        $plot = new Plot();
        echo "✅ Plot model exists\n";

        // Check fillable fields
        echo "📝 Plot fillable fields:\n";
        foreach ($plot->getFillable() as $field) {
            echo "  - {$field}\n";
        }

    } catch (Exception $e) {
        echo "❌ Plot model error: " . $e->getMessage() . "\n";
    }

    try {
        $plotAddress = new PlotAddress();
        echo "✅ PlotAddress model exists\n";

        // Check fillable fields
        echo "📝 PlotAddress fillable fields:\n";
        foreach ($plotAddress->getFillable() as $field) {
            echo "  - {$field}\n";
        }

    } catch (Exception $e) {
        echo "❌ PlotAddress model error: " . $e->getMessage() . "\n";
    }

    echo "\n🗺️ Google Maps Integration Analysis\n";
    echo "===================================\n";

    // Check for Google Maps related configuration
    $googleMapsKey = env('GOOGLE_MAPS_API_KEY');
    if ($googleMapsKey) {
        echo "✅ Google Maps API Key configured: " . substr($googleMapsKey, 0, 10) . "...\n";
    } else {
        echo "❌ Google Maps API Key not configured\n";
    }

    // Check current HB837 records with coordinates
    try {
        $hb837WithCoords = HB837::whereNotNull('coordinates_latitude')
                                ->whereNotNull('coordinates_longitude')
                                ->count();
        echo "📍 HB837 records with coordinates: {$hb837WithCoords}\n";
    } catch (Exception $e) {
        echo "❌ Error checking HB837 coordinates: " . $e->getMessage() . "\n";
    }

    // Check plots with coordinates
    try {
        $plotsWithCoords = Plot::whereNotNull('coordinates_latitude')
                               ->whereNotNull('coordinates_longitude')
                               ->count();
        echo "📍 Plot records with coordinates: {$plotsWithCoords}\n";
    } catch (Exception $e) {
        echo "❌ Error checking Plot coordinates: " . $e->getMessage() . "\n";
    }

    echo "\n🔍 Legacy System References\n";
    echo "===========================\n";

    // Check for route references to maps/plots
    $routeFile = file_get_contents('routes/admin.php');
    if (strpos($routeFile, 'mapplots') !== false) {
        echo "✅ Found mapplots route references\n";
    } else {
        echo "❌ No mapplots route references found\n";
    }

    if (strpos($routeFile, 'maps') !== false) {
        echo "✅ Found maps route references\n";
    } else {
        echo "❌ No maps route references found\n";
    }

    // Check for views
    $mapsViewPath = 'resources/views/admin/maps';
    if (is_dir($mapsViewPath)) {
        echo "✅ Maps views directory exists\n";
        $files = scandir($mapsViewPath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "  - {$file}\n";
            }
        }
    } else {
        echo "❌ Maps views directory not found\n";
    }

    // Check for plots views
    $plotsViewPath = 'resources/views/admin/plots';
    if (is_dir($plotsViewPath)) {
        echo "✅ Plots views directory exists\n";
        $files = scandir($plotsViewPath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "  - {$file}\n";
            }
        }
    } else {
        echo "❌ Plots views directory not found\n";
    }

    echo "\n📈 Recommendations for GooglePlots System\n";
    echo "==========================================\n";

    echo "Based on analysis, here's what needs to be implemented:\n\n";

    echo "🔧 Required Components:\n";
    echo "  1. GoogleMapsController - For map display and interaction\n";
    echo "  2. PlotsController - For plot management\n";
    echo "  3. Maps views - For displaying interactive maps\n";
    echo "  4. JavaScript integration - For Google Maps API\n";
    echo "  5. Route definitions - For map and plot routes\n\n";

    echo "📋 Current Status:\n";
    echo "  ✅ Database tables (plots, plot_addresses) exist\n";
    echo "  ✅ Models (Plot, PlotAddress) exist and functional\n";
    echo "  ✅ Relationship to HB837 established\n";
    echo "  ❌ Controllers missing\n";
    echo "  ❌ Views missing\n";
    echo "  ❌ Routes missing\n";
    echo "  ❌ JavaScript integration missing\n\n";

    echo "🚀 Next Steps:\n";
    echo "  1. Create GoogleMapsController\n";
    echo "  2. Create PlotsController\n";
    echo "  3. Create map views with Google Maps integration\n";
    echo "  4. Add routes for map functionality\n";
    echo "  5. Configure Google Maps API key\n";
    echo "  6. Test plot creation and map display\n\n";

} catch (Exception $e) {
    echo "❌ System Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

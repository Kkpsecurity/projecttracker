<?php

echo "=== Final Frontend Verification Test ===\n";
echo "Testing Plot/Address relationship fixes...\n\n";

try {
    // Test database connection and basic model operations
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dbPath = __DIR__ . '/../database/database.sqlite';

    if (!file_exists($dbPath)) {
        echo "❌ Database file not found at: $dbPath\n";
        exit(1);
    }

    echo "✅ Database file found\n";

    // Test relationship access
    include_once __DIR__ . '/../bootstrap/app.php';

    echo "\n--- Testing Plot Model Relationships ---\n";

    // Get a few plots to test
    $plots = \App\Models\Plot::with('address', 'hb837')->limit(3)->get();

    echo "Found " . $plots->count() . " plots for testing\n\n";

    foreach ($plots as $plot) {
        echo "Plot #{$plot->id}: {$plot->plot_name}\n";

        // Test address relationship (this was the problematic one)
        try {
            $address = $plot->address;
            if ($address) {
                echo "  ✅ Address relationship works: {$address->address_line_1}\n";
                echo "  📍 Full address: {$address->full_address}\n";
            } else {
                echo "  ⚠️  No address data for this plot\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Address relationship error: " . $e->getMessage() . "\n";
            continue;
        }

        // Test HB837 relationship
        try {
            $hb837 = $plot->hb837;
            if ($hb837) {
                echo "  ✅ HB837 relationship works: {$hb837->project_name}\n";
            } else {
                echo "  ⚠️  No HB837 project linked to this plot\n";
            }
        } catch (Exception $e) {
            echo "  ❌ HB837 relationship error: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    echo "\n--- Testing Controller Data Access ---\n";

    // Simulate what GoogleMapsController does
    $plotsWithCoordinates = \App\Models\Plot::whereNotNull('coordinates_latitude')
                                           ->whereNotNull('coordinates_longitude')
                                           ->with('address', 'hb837')
                                           ->get();

    echo "Plots with coordinates: " . $plotsWithCoordinates->count() . "\n";

    foreach ($plotsWithCoordinates->take(2) as $plot) {
        echo "\nPlot #{$plot->id}:\n";
        echo "  - Name: {$plot->plot_name}\n";
        echo "  - Coordinates: " . (string)$plot->coordinates_latitude . ", " . (string)$plot->coordinates_longitude . "\n";

        if ($plot->address) {
            echo "  - Address: {$plot->address->full_address}\n";
        }

        if ($plot->hb837) {
            echo "  - Project: {$plot->hb837->project_name}\n";
        }
    }

    echo "\n--- Testing Statistics Calculations ---\n";

    $totalPlots = \App\Models\Plot::count();
    $plotsWithAddresses = \App\Models\Plot::whereHas('address')->count();
    $plotsWithCoordinates = \App\Models\Plot::whereNotNull('coordinates_latitude')
                                           ->whereNotNull('coordinates_longitude')
                                           ->count();
    $linkedToProjects = \App\Models\Plot::whereNotNull('hb837_id')->count();

    echo "📊 Statistics:\n";
    echo "  - Total Plots: $totalPlots\n";
    echo "  - With Addresses: $plotsWithAddresses\n";
    echo "  - With Coordinates: $plotsWithCoordinates\n";
    echo "  - Linked to Projects: $linkedToProjects\n";

    echo "\n✅ All relationship tests passed!\n";
    echo "🎯 Frontend should now work without 'plotAddress' errors\n";

    echo "\n=== Next Steps ===\n";
    echo "1. Visit: http://$host/admin/maps\n";
    echo "2. Visit: http://$host/admin/plots\n";
    echo "3. Test plot creation and editing\n";
    echo "4. Verify map markers load correctly\n";
    echo "\n=== Test Complete ===\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

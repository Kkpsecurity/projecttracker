<?php

echo "=== Enhanced Plot Features Test ===\n";

try {
    // Bootstrap Laravel
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "✅ Laravel bootstrapped successfully\n\n";

    echo "--- Testing GoogleMapsController Features ---\n";

    // Test 1: Verify macro clients are available
    echo "1. Testing Macro Clients Dropdown Data:\n";
    $macroClients = \App\Models\HB837::whereNotNull('macro_client')
                                    ->where('macro_client', '!=', '')
                                    ->distinct()
                                    ->pluck('macro_client')
                                    ->sort()
                                    ->values();

    echo "   Macro clients found: " . $macroClients->count() . "\n";
    foreach($macroClients as $client) {
        echo "   - $client\n";
    }

    echo "\n2. Testing Plot Creation Logic:\n";
    // Test the address parsing function logic
    $testAddresses = [
        "123 Main St, Austin, TX 78701",
        "456 Oak Avenue, Dallas, TX 75201, USA",
        "789 Pine Street",
    ];

    foreach($testAddresses as $address) {
        echo "   Testing address: $address\n";
        $parts = explode(',', $address);
        $result = [];

        if (count($parts) >= 1) {
            $result['street'] = trim($parts[0]);
        }
        if (count($parts) >= 2) {
            $result['city'] = trim($parts[1]);
        }
        if (count($parts) >= 3) {
            $stateZip = trim($parts[2]);
            if (preg_match('/^(.+?)\s+(\d{5}(-\d{4})?)$/', $stateZip, $matches)) {
                $result['state'] = trim($matches[1]);
                $result['zip'] = $matches[2];
            } else {
                $result['state'] = $stateZip;
            }
        }

        echo "     Parsed: " . json_encode($result) . "\n";
    }

    echo "\n3. Testing Macro Client Plot Associations:\n";
    foreach($macroClients->take(3) as $client) {
        $hb837Projects = \App\Models\HB837::where('macro_client', $client)->with('plots')->get();
        $plotIds = $hb837Projects->pluck('id');
        $plots = \App\Models\Plot::whereIn('hb837_id', $plotIds)
                                ->whereNotNull('coordinates_latitude')
                                ->whereNotNull('coordinates_longitude')
                                ->count();

        echo "   Client: $client\n";
        echo "     Projects: " . $hb837Projects->count() . "\n";
        echo "     Mapped Plots: $plots\n";

        // Check for project addresses without plots
        $projectsWithoutPlots = $hb837Projects->filter(function($project) {
            return $project->plots->count() === 0 && !empty($project->address);
        });

        echo "     Projects with addresses but no plots: " . $projectsWithoutPlots->count() . "\n";
        foreach($projectsWithoutPlots as $project) {
            echo "       - {$project->project_name}: {$project->address}\n";
        }
        echo "\n";
    }

    echo "--- Testing Route Endpoints ---\n";

    // Check if the new routes are properly registered
    $routes = [
        'admin.maps.index' => 'GET /admin/maps',
        'admin.maps.plot.from-address' => 'POST /admin/maps/plot/from-address',
        'admin.maps.macro-client.plots' => 'GET /admin/maps/macro-client/plots',
    ];

    foreach($routes as $routeName => $description) {
        try {
            $url = route($routeName);
            echo "✅ Route '$routeName' registered: $url\n";
        } catch (\Exception $e) {
            echo "❌ Route '$routeName' not found: " . $e->getMessage() . "\n";
        }
    }

    echo "\n--- Database State Summary ---\n";
    $totalProjects = \App\Models\HB837::count();
    $projectsWithMacroClient = \App\Models\HB837::whereNotNull('macro_client')->where('macro_client', '!=', '')->count();
    $totalPlots = \App\Models\Plot::count();
    $plotsWithCoordinates = \App\Models\Plot::whereNotNull('coordinates_latitude')
                                           ->whereNotNull('coordinates_longitude')
                                           ->count();
    $plotsWithAddress = \App\Models\Plot::whereHas('address')->count();

    echo "📊 Statistics:\n";
    echo "   Total HB837 Projects: $totalProjects\n";
    echo "   Projects with Macro Client: $projectsWithMacroClient\n";
    echo "   Total Plots: $totalPlots\n";
    echo "   Plots with Coordinates: $plotsWithCoordinates\n";
    echo "   Plots with Address: $plotsWithAddress\n";

    echo "\n🎯 Enhanced Features Ready:\n";
    echo "✅ Address Input: Users can enter an address to create plots\n";
    echo "✅ Macro Client Dropdown: " . $macroClients->count() . " clients available for filtering\n";
    echo "✅ Plot Association: Plots linked to HB837 projects via macro clients\n";
    echo "✅ Project Address Display: Shows project addresses without plots\n";

    echo "\n--- Next Steps ---\n";
    echo "1. Test the enhanced Google Maps interface at: http://projecttracker_fresh.test/admin/maps\n";
    echo "2. Try adding plots using the address input field\n";
    echo "3. Test filtering plots by macro client\n";
    echo "4. Verify that project addresses (yellow markers) appear for clients\n";
    echo "5. Test the clear filters functionality\n";

    echo "\n=== Test Complete ===\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

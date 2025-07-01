<?php

echo "=== Macro Clients Analysis ===\n";

try {
    // Bootstrap Laravel
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Get unique macro clients
    $macroClients = \App\Models\HB837::whereNotNull('macro_client')
                                    ->where('macro_client', '!=', '')
                                    ->distinct()
                                    ->pluck('macro_client')
                                    ->filter()
                                    ->values();

    echo "Unique Macro Clients found: " . $macroClients->count() . "\n\n";

    if ($macroClients->count() > 0) {
        echo "--- Macro Clients List ---\n";
        foreach($macroClients->take(15) as $index => $client) {
            echo ($index + 1) . ". $client\n";
        }

        echo "\n--- Sample Projects by Macro Client ---\n";
        foreach($macroClients->take(5) as $client) {
            $projectCount = \App\Models\HB837::where('macro_client', $client)->count();
            $plotCount = \App\Models\Plot::whereHas('hb837', function($query) use ($client) {
                $query->where('macro_client', $client);
            })->count();

            echo "Client: $client\n";
            echo "  - Projects: $projectCount\n";
            echo "  - Related Plots: $plotCount\n";
            echo "\n";
        }
    } else {
        echo "No macro clients found. Let's check sample data:\n";
        $sampleProjects = \App\Models\HB837::take(5)->get(['id', 'project_name', 'macro_client', 'address']);
        foreach($sampleProjects as $project) {
            echo "Project: {$project->project_name}\n";
            echo "Macro Client: " . ($project->macro_client ?: 'None') . "\n";
            echo "Address: {$project->address}\n";
            echo "---\n";
        }
    }

    echo "\n=== Database Structure Analysis ===\n";
    $totalProjects = \App\Models\HB837::count();
    $projectsWithMacroClient = \App\Models\HB837::whereNotNull('macro_client')->where('macro_client', '!=', '')->count();
    $totalPlots = \App\Models\Plot::count();
    $plotsWithAddress = \App\Models\Plot::whereHas('address')->count();

    echo "Total HB837 Projects: $totalProjects\n";
    echo "Projects with Macro Client: $projectsWithMacroClient\n";
    echo "Total Plots: $totalPlots\n";
    echo "Plots with Address: $plotsWithAddress\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

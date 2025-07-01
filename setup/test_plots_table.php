<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Plot;
use App\Models\HB837;
use Illuminate\Support\Facades\DB;

try {
    echo "Testing plots table structure...\n";

    // First, let's verify we can connect to the database
    $connection = DB::connection();
    echo "✓ Database connection successful\n";

    // Check if plots table exists and has the required columns
    $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'plots' AND table_schema = 'public'");
    echo "✓ Plots table columns:\n";
    foreach ($columns as $column) {
        echo "  - " . $column->column_name . "\n";
    }

    // Try to create a simple plot record
    echo "\nTesting plot creation...\n";

    $plot = new Plot();
    $plot->plot_name = 'Test Plot';
    $plot->hb837_id = 1;
    $plot->lot_number = '88';
    $plot->block_number = '12';
    $plot->subdivision_name = 'Test Subdivision';
    $plot->coordinates_latitude = 30.2489;
    $plot->coordinates_longitude = -97.6836;

    $plot->save();

    echo "✓ Plot created successfully with ID: " . $plot->id . "\n";

    // Clean up - delete the test record
    $plot->delete();
    echo "✓ Test plot deleted\n";

    echo "\n🎉 SUCCESS: Plots table is working correctly!\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

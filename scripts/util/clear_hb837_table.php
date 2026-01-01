<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Starting to check hb837 table...\n";
    
    // Get current record count
    $beforeCount = DB::table('hb837')->count();
    echo "Records in table: {$beforeCount}\n";
    
    if ($beforeCount > 0) {
        $first = DB::table('hb837')->first();
        echo "First record details:\n";
        echo "  ID: {$first->id}\n";
        echo "  Property Name: {$first->property_name}\n";
        echo "  Address: {$first->address}\n";
        echo "  Report Status: {$first->report_status}\n";
        echo "  Contracting Status: {$first->contracting_status}\n";
        
        echo "\nDo you want to clear the table? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim($line) == 'y' || trim($line) == 'Y') {
            // Clear the table
            DB::table('hb837')->truncate();
            
            // Verify it's empty
            $afterCount = DB::table('hb837')->count();
            echo "Records after clearing: {$afterCount}\n";
            
            if ($afterCount === 0) {
                echo "✅ HB837 table cleared successfully!\n";
            } else {
                echo "❌ Warning: Table may not be completely cleared.\n";
            }
            
            echo "Table is now ready for fresh test data.\n";
        } else {
            echo "Table clearing cancelled.\n";
        }
    } else {
        echo "Table is already empty.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error clearing hb837 table: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

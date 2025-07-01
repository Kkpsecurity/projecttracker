<?php
/*
 * Test script to verify ConsultantController DataTables endpoint
 * This simulates the AJAX DataTables request to ensure it works correctly
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\ConsultantController;
use Illuminate\Http\Request;

echo "=== CONSULTANT CONTROLLER DATATABLES ENDPOINT TEST ===\n";

try {
    // Create a mock request for DataTables AJAX
    $request = new Request([
        'draw' => 1,
        'start' => 0,
        'length' => 10,
        'search' => ['value' => ''],
        'order' => [0 => ['column' => 0, 'dir' => 'asc']],
        'columns' => [
            0 => ['data' => 'last_name', 'searchable' => true, 'orderable' => true],
            1 => ['data' => 'first_name', 'searchable' => true, 'orderable' => true],
            2 => ['data' => 'email', 'searchable' => true, 'orderable' => true],
            3 => ['data' => 'dba_company_name', 'searchable' => true, 'orderable' => true],
            4 => ['data' => 'active_assignments', 'searchable' => false, 'orderable' => true],
            5 => ['data' => 'completed_assignments', 'searchable' => false, 'orderable' => true],
            6 => ['data' => 'action', 'searchable' => false, 'orderable' => false],
        ]
    ]);

    // Mock the AJAX request
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    // Create controller instance
    $controller = new ConsultantController();

    echo "1. Testing DataTables AJAX endpoint with mock request...\n";

    // Test the index method with AJAX request (this is where DataTables logic is)
    $response = $controller->index($request);

    // Get the JSON content
    $jsonContent = $response->getContent();
    $data = json_decode($jsonContent, true);

    echo "2. Response structure:\n";
    echo "   Draw: " . ($data['draw'] ?? 'missing') . "\n";
    echo "   Records Total: " . ($data['recordsTotal'] ?? 'missing') . "\n";
    echo "   Records Filtered: " . ($data['recordsFiltered'] ?? 'missing') . "\n";
    echo "   Data Count: " . (count($data['data'] ?? [])) . "\n";

    if (isset($data['data']) && count($data['data']) > 0) {
        echo "\n3. Sample consultant data:\n";
        $firstConsultant = $data['data'][0];
        echo "   Name: " . ($firstConsultant['first_name'] ?? 'N/A') . " " . ($firstConsultant['last_name'] ?? 'N/A') . "\n";
        echo "   Email: " . ($firstConsultant['email'] ?? 'N/A') . "\n";
        echo "   Company: " . ($firstConsultant['dba_company_name'] ?? 'N/A') . "\n";
        echo "   Active Projects: " . ($firstConsultant['active_assignments'] ?? 'N/A') . "\n";
        echo "   Completed Projects: " . ($firstConsultant['completed_assignments'] ?? 'N/A') . "\n";
        echo "   Has Actions: " . (isset($firstConsultant['action']) ? 'Yes' : 'No') . "\n";
    }

    echo "\n✅ ConsultantController DataTables endpoint test completed successfully!\n";
    echo "✅ The SQL error should now be resolved.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";

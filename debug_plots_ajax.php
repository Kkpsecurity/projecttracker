<?php

// Quick test script to debug the plots datatable Ajax endpoint

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request to the datatable endpoint
$request = Illuminate\Http\Request::create('/admin/plots/datatable', 'GET', [
    'draw' => 1,
    'start' => 0,
    'length' => 10
]);

try {
    $response = $kernel->handle($request);

    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Response Length: " . strlen($response->getContent()) . "\n";

    if ($response->getStatusCode() !== 200) {
        echo "Error Response:\n";
        echo $response->getContent() . "\n";
    } else {
        $content = $response->getContent();
        $json = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            echo "Valid JSON Response\n";
            echo "Draw: " . ($json['draw'] ?? 'missing') . "\n";
            echo "Records Total: " . ($json['recordsTotal'] ?? 'missing') . "\n";
            echo "Records Filtered: " . ($json['recordsFiltered'] ?? 'missing') . "\n";
            echo "Data Count: " . (isset($json['data']) ? count($json['data']) : 'missing') . "\n";
        } else {
            echo "Invalid JSON Response:\n";
            echo $content . "\n";
        }
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);

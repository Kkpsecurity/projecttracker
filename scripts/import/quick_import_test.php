<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/test');
$kernel->bootstrap();

use App\Http\Controllers\Admin\HB837\HB837Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

echo "=== Quick Import Test for report_status ===\n\n";

try {
    $controller = new HB837Controller();
    
    // Test file path
    $testFile = 'storage/app/public/test_import.xlsx';
    
    if (!file_exists($testFile)) {
        echo "❌ Test file not found\n";
        exit(1);
    }
    
    echo "✅ Testing import analysis...\n";
    
    // Read file headers using Excel
    $data = \Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
        public function array(array $array): array { return $array; }
    }, $testFile);
    
    if (!empty($data) && !empty($data[0])) {
        $headers = array_shift($data[0]); // First row as headers
        
        echo "\n📋 CSV Headers found:\n";
        foreach ($headers as $index => $header) {
            $cleanHeader = trim($header ?? '');
            if ($cleanHeader) {
                echo "   [$index] '$cleanHeader'\n";
                
                // Check if this looks like report_status
                if (stripos($cleanHeader, 'status') !== false || 
                    stripos($cleanHeader, 'progress') !== false ||
                    stripos($cleanHeader, 'report') !== false) {
                    echo "      👆 POTENTIAL report_status match!\n";
                }
            }
        }
        
        echo "\n🔍 Testing config mapping for report_status...\n";
        $reportStatusConfig = config('hb837_field_mapping.field_mapping.report_status', []);
        
        echo "Config patterns for report_status:\n";
        foreach ($reportStatusConfig as $pattern) {
            echo "   - '$pattern'\n";
        }
        
        echo "\n🎯 Testing mapping logic...\n";
        foreach ($headers as $header) {
            $headerLower = strtolower(trim($header ?? ''));
            foreach ($reportStatusConfig as $pattern) {
                $patternLower = strtolower($pattern);
                
                if ($headerLower === $patternLower) {
                    echo "✅ EXACT MATCH: '$header' → report_status\n";
                } elseif (strpos($headerLower, $patternLower) !== false || strpos($patternLower, $headerLower) !== false) {
                    echo "🔶 PARTIAL MATCH: '$header' → report_status (contains '$pattern')\n";
                }
            }
        }
        
        // Test a few sample rows
        $sampleRows = array_slice($data[0], 0, 3);
        echo "\n📊 Sample data rows:\n";
        
        foreach ($sampleRows as $rowIndex => $row) {
            echo "Row " . ($rowIndex + 1) . ":\n";
            foreach ($headers as $colIndex => $header) {
                $value = $row[$colIndex] ?? '';
                if (trim($header) && trim($value)) {
                    echo "   $header: '$value'\n";
                }
            }
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "=== Test Complete ===\n";

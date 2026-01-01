<?php
/**
 * Three-Phase HB837 Import Test Script
 * Tests the complete workflow with logging verification
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../bootstrap/app.php';

use App\Imports\HB837Import;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

echo "==========================================\n";
echo "  THREE-PHASE HB837 IMPORT TEST\n";
echo "==========================================\n";

$baseDir = dirname(__DIR__, 2);
$testFiles = [
    'initial' => "$baseDir/docs/tasks/extracted_reports/TEST SHEET 01 - Initial Import & Quotation.xlsx",
    'update' => "$baseDir/docs/tasks/extracted_reports/TEST SHEET 02 - Executed & Contacts - VERIFY.XLSX", 
    'review' => "$baseDir/docs/tasks/extracted_reports/TEST SHEET 03 - Details Updated - VERIFY.XLSX"
];

// Verify all test files exist
echo "=== FILE VERIFICATION ===\n";
foreach ($testFiles as $phase => $file) {
    if (file_exists($file)) {
        $size = round(filesize($file) / 1024, 2);
        echo "✅ {$phase}: {$size} KB - " . basename($file) . "\n";
    } else {
        echo "❌ {$phase}: NOT FOUND - {$file}\n";
        exit(1);
    }
}

echo "\n=== STARTING THREE-PHASE IMPORT ===\n";

foreach (['initial', 'update', 'review'] as $phase) {
    echo "\n--- PHASE: " . strtoupper($phase) . " ---\n";
    echo "File: " . basename($testFiles[$phase]) . "\n";
    
    // Log phase start
    Log::info("🚀 Starting {$phase} phase import", [
        'file' => $testFiles[$phase],
        'phase' => $phase,
        'timestamp' => now()
    ]);
    
    try {
        $import = new HB837Import();
        $import->setPhase($phase);
        
        Excel::import($import, $testFiles[$phase]);
        
        echo "✅ Phase {$phase} completed:\n";
        echo "   - Imported: {$import->importedCount}\n";
        echo "   - Updated: {$import->updatedCount}\n";
        echo "   - Skipped: {$import->skippedCount}\n";
        
        Log::info("✅ {$phase} phase completed successfully", [
            'imported' => $import->importedCount,
            'updated' => $import->updatedCount,
            'skipped' => $import->skippedCount,
            'phase' => $phase
        ]);
        
    } catch (Exception $e) {
        echo "❌ Phase {$phase} failed: " . $e->getMessage() . "\n";
        Log::error("❌ {$phase} phase failed", [
            'error' => $e->getMessage(),
            'file' => $testFiles[$phase],
            'phase' => $phase
        ]);
        break;
    }
}

echo "\n=== IMPORT TEST COMPLETED ===\n";
echo "Check logs for detailed consultant assignment and processing details.\n";
echo "Run validation script: php scripts/test/task02_post_import_validation_ready.php\n";

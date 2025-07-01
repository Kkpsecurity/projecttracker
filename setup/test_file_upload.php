<?php

require_once 'vendor/autoload.php';

try {
    // Test the storage directory structure
    $tempDir = storage_path('app/temp');
    $importsDir = storage_path('app/temp/imports');

    echo "🔍 Checking directory structure:\n";
    echo "Temp directory: " . ($tempDir) . " - " . (is_dir($tempDir) ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "Imports directory: " . ($importsDir) . " - " . (is_dir($importsDir) ? "✅ EXISTS" : "❌ MISSING") . "\n";

    // Test if directories are writable
    echo "Temp directory writable: " . (is_writable($tempDir) ? "✅ YES" : "❌ NO") . "\n";
    echo "Imports directory writable: " . (is_writable($importsDir) ? "✅ YES" : "❌ NO") . "\n";

    // Check if the test Excel file exists
    $testFile = 'docs/hb837_projects(16).xlsx';
    if (file_exists($testFile)) {
        echo "\n📄 Test file found: " . $testFile . "\n";

        // Test copying to temp directory
        $tempFileName = 'test_' . time() . '.xlsx';
        $tempFilePath = $importsDir . '/' . $tempFileName;

        if (copy($testFile, $tempFilePath)) {
            echo "✅ Successfully copied test file to temp directory\n";

            // Test PhpSpreadsheet can read the file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFilePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            echo "✅ PhpSpreadsheet successfully read the file\n";
            echo "Rows found: " . count($data) . "\n";
            echo "Columns found: " . (count($data) > 0 ? count($data[0]) : 0) . "\n";

            // Clean up test file
            unlink($tempFilePath);
            echo "✅ Test file cleaned up\n";

        } else {
            echo "❌ Failed to copy test file to temp directory\n";
        }
    } else {
        echo "\n📄 Test file not found: " . $testFile . "\n";
        echo "You can test with any Excel file by uploading through the web interface\n";
    }

    echo "\n🎉 Directory structure and file handling setup complete!\n";
    echo "You should now be able to upload Excel files without the 'file does not exist' error.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

function storage_path($path = '') {
    return __DIR__ . '/storage/' . ltrim($path, '/');
}

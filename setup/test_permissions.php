<?php

echo "Testing directory permissions and file upload simulation:\n";
echo "========================================================\n";

$storageDir = __DIR__ . '/storage/app';
$tempDir = __DIR__ . '/storage/app/temp';
$importsDir = __DIR__ . '/storage/app/temp/imports';

echo "Storage directory: " . $storageDir . "\n";
echo "  Exists: " . (is_dir($storageDir) ? "YES" : "NO") . "\n";
echo "  Writable: " . (is_writable($storageDir) ? "YES" : "NO") . "\n";

echo "\nTemp directory: " . $tempDir . "\n";
echo "  Exists: " . (is_dir($tempDir) ? "YES" : "NO") . "\n";
echo "  Writable: " . (is_writable($tempDir) ? "YES" : "NO") . "\n";

echo "\nImports directory: " . $importsDir . "\n";
echo "  Exists: " . (is_dir($importsDir) ? "YES" : "NO") . "\n";
echo "  Writable: " . (is_writable($importsDir) ? "YES" : "NO") . "\n";

// Test file creation simulation
$testFileName = 'import_' . time() . '_' . uniqid() . '.xlsx';
$testFilePath = $importsDir . '/' . $testFileName;

echo "\nTesting file creation:\n";
echo "Target path: " . $testFilePath . "\n";

// Test if we can create a file
$testContent = "test content";
if (file_put_contents($testFilePath, $testContent) !== false) {
    echo "✅ File creation successful\n";

    // Check if file exists and is readable
    if (file_exists($testFilePath)) {
        echo "✅ File exists after creation\n";

        if (is_readable($testFilePath)) {
            echo "✅ File is readable\n";
        } else {
            echo "❌ File is not readable\n";
        }

        // Clean up
        unlink($testFilePath);
        echo "✅ Test file cleaned up\n";
    } else {
        echo "❌ File does not exist after creation\n";
    }
} else {
    echo "❌ File creation failed\n";
    echo "Last error: " . error_get_last()['message'] . "\n";
}

// Check disk space
$freeBytes = disk_free_space($storageDir);
$totalBytes = disk_total_space($storageDir);
echo "\nDisk space:\n";
echo "  Free: " . round($freeBytes / 1024 / 1024, 2) . " MB\n";
echo "  Total: " . round($totalBytes / 1024 / 1024, 2) . " MB\n";

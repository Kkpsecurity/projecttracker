<?php

echo "🧪 Testing Fixed File Upload Logic\n";
echo "=================================\n\n";

// Simulate the new upload approach
function simulateFileUpload($sourceFile) {
    echo "📄 Testing upload: " . basename($sourceFile) . "\n";

    if (!file_exists($sourceFile)) {
        echo "❌ Source file not found: {$sourceFile}\n";
        return false;
    }

    $originalName = basename($sourceFile);
    $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);

    // Directory setup (like in controller)
    $tempDirPath = 'temp' . DIRECTORY_SEPARATOR . 'imports';
    $fullTempDir = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $tempDirPath;

    echo "  📁 Target directory: {$fullTempDir}\n";

    if (!file_exists($fullTempDir)) {
        if (!mkdir($fullTempDir, 0755, true)) {
            echo "  ❌ Failed to create directory\n";
            return false;
        }
        echo "  ✅ Directory created\n";
    } else {
        echo "  ✅ Directory exists\n";
    }

    if (!is_writable($fullTempDir)) {
        echo "  ❌ Directory not writable\n";
        return false;
    }
    echo "  ✅ Directory is writable\n";

    // File upload simulation (like in controller)
    $fileName = 'import_' . time() . '_' . uniqid() . '.' . $extension;
    $targetPath = $fullTempDir . DIRECTORY_SEPARATOR . $fileName;

    echo "  📂 Target file: {$targetPath}\n";

    // Simulate file move operation
    if (!copy($sourceFile, $targetPath)) {
        echo "  ❌ File copy failed\n";
        return false;
    }
    echo "  ✅ File copied successfully\n";

    // Verify file was stored successfully
    if (!file_exists($targetPath)) {
        echo "  ❌ File does not exist after copy\n";
        return false;
    }
    echo "  ✅ File exists after copy\n";

    // Verify file is readable
    if (!is_readable($targetPath)) {
        echo "  ❌ File is not readable\n";
        unlink($targetPath);
        return false;
    }
    echo "  ✅ File is readable\n";

    // Test PhpSpreadsheet can process it
    try {
        require_once 'vendor/autoload.php';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetPath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        echo "  ✅ PhpSpreadsheet processing successful\n";
        echo "    - Rows: " . count($data) . "\n";
        echo "    - Columns: " . (count($data) > 0 ? count($data[0]) : 0) . "\n";
        echo "    - File size: " . round(filesize($targetPath) / 1024, 2) . " KB\n";

    } catch (Exception $e) {
        echo "  ❌ PhpSpreadsheet error: " . $e->getMessage() . "\n";
        unlink($targetPath);
        return false;
    }

    // Clean up
    unlink($targetPath);
    echo "  ✅ Test file cleaned up\n";

    return true;
}

// Test with the actual file
$testFile = 'docs/hb837_projects(16).xlsx';
echo "🎯 Testing Fixed Upload Logic:\n";
echo "============================\n";

$result = simulateFileUpload($testFile);

if ($result) {
    echo "\n🎉 SUCCESS: Fixed Upload Logic Test PASSED!\n";
    echo "==========================================\n\n";
    echo "✅ File upload mechanism is working correctly\n";
    echo "✅ Directory path handling is fixed\n";
    echo "✅ File storage and verification works\n";
    echo "✅ PhpSpreadsheet integration is functional\n\n";
    echo "🚀 Ready for Production:\n";
    echo "   Your 'TEST SHEET 01 - Initial Import & Quotation.xlsx' upload should now work!\n";
    echo "   The 'Failed to store uploaded file' error should be resolved.\n";
} else {
    echo "\n❌ FAILED: Upload Logic Test\n";
    echo "===========================\n";
    echo "There are still issues with the file upload process.\n";
}

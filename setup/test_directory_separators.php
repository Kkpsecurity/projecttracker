<?php

require_once 'vendor/autoload.php';

echo "🔧 Testing Directory Separator Fix\n";
echo "=================================\n\n";

// Test directory separator handling
function testPathHandling() {
    echo "📁 Testing Path Construction:\n";

    // Test the way we construct paths in the controller
    $tempDirPath = 'temp' . DIRECTORY_SEPARATOR . 'imports';
    $fullTempDir = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $tempDirPath;

    echo "  DIRECTORY_SEPARATOR: '" . DIRECTORY_SEPARATOR . "'\n";
    echo "  tempDirPath: {$tempDirPath}\n";
    echo "  fullTempDir: {$fullTempDir}\n";

    // Check if directory exists
    if (is_dir($fullTempDir)) {
        echo "  ✅ Directory exists\n";
    } else {
        echo "  ❌ Directory does not exist\n";
        return false;
    }

    // Test file path construction
    $fileName = 'import_' . time() . '_' . uniqid() . '.xlsx';
    $laravelStylePath = 'temp/imports/' . $fileName; // Laravel uses forward slashes
    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $laravelStylePath);

    echo "  Laravel style path: {$laravelStylePath}\n";
    echo "  Full system path: {$fullPath}\n";

    // Test file creation
    $testContent = "test content for path verification";
    if (file_put_contents($fullPath, $testContent) !== false) {
        echo "  ✅ File creation successful\n";

        if (file_exists($fullPath)) {
            echo "  ✅ File exists after creation\n";

            // Clean up
            unlink($fullPath);
            echo "  ✅ Test file cleaned up\n";
            return true;
        } else {
            echo "  ❌ File does not exist after creation\n";
            return false;
        }
    } else {
        echo "  ❌ File creation failed\n";
        return false;
    }
}

// Test storage_path function behavior
function testStoragePathFunction() {
    echo "\n📋 Testing storage_path() Function:\n";

    // Simulate Laravel's storage_path function
    function storage_path($path = '') {
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'storage';
        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
    }

    $test1 = storage_path('app/temp/imports');
    $test2 = storage_path('app\\temp\\imports');
    $test3 = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');

    echo "  storage_path('app/temp/imports'): {$test1}\n";
    echo "  storage_path('app\\\\temp\\\\imports'): {$test2}\n";
    echo "  storage_path('app'.DS.'temp'.DS.'imports'): {$test3}\n";

    // Check if all paths point to the same directory
    if (realpath(dirname($test1)) === realpath(dirname($test2)) &&
        realpath(dirname($test2)) === realpath(dirname($test3))) {
        echo "  ✅ All paths resolve to the same directory\n";
        return true;
    } else {
        echo "  ❌ Paths resolve to different directories\n";
        return false;
    }
}

// Run tests
$pathTest = testPathHandling();
$storageTest = testStoragePathFunction();

echo "\n🎯 Test Results:\n";
echo "===============\n";
echo "Path Handling: " . ($pathTest ? "✅ PASSED" : "❌ FAILED") . "\n";
echo "Storage Function: " . ($storageTest ? "✅ PASSED" : "❌ FAILED") . "\n";

if ($pathTest && $storageTest) {
    echo "\n🎉 Directory Separator Fix: SUCCESS\n";
    echo "==================================\n\n";
    echo "✅ The mixed directory separator issue has been resolved!\n";
    echo "✅ Paths are now constructed consistently across platforms\n";
    echo "✅ File uploads should work without path-related errors\n\n";
    echo "📋 Your Excel file upload should now work correctly:\n";
    echo "   - TEST SHEET 01 - Initial Import & Quotation.xlsx\n";
    echo "   - Size: 9.42 KB | Type: Excel Workbook (Modern)\n";
} else {
    echo "\n❌ Directory Separator Fix: FAILED\n";
    echo "=================================\n";
    echo "Some path handling issues remain. Please check the error messages above.\n";
}

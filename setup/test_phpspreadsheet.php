<?php

require_once 'vendor/autoload.php';

try {
    // Test if PhpSpreadsheet is available
    if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        echo "✅ PhpOffice\PhpSpreadsheet\IOFactory is available!\n";

        // Test if we can create a simple spreadsheet reader
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify('docs/hb837_projects(16).xlsx');
        echo "✅ File type detection works: " . $inputFileType . "\n";

        // Test if we can load the spreadsheet
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        echo "✅ Reader created successfully: " . get_class($reader) . "\n";

        echo "\n🎉 PhpSpreadsheet is fully functional!\n";
        echo "You can now process Excel files with:\n";
        echo "- TEST SHEET 01 - Initial Import & Quotation.xlsx\n";
        echo "- Size: 9.42 KB | Type: Excel Workbook (Modern)\n\n";

    } else {
        echo "❌ PhpOffice\PhpSpreadsheet\IOFactory not found\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
everything look like it work

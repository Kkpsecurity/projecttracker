<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Analyze Excel file structure to verify our field mapping
 */

echo "=== Excel File Structure Analysis ===\n\n";

// Check the sample file first
$sampleFile = __DIR__ . '/docs/hb837_projects(16).xlsx';
if (file_exists($sampleFile)) {
    echo "Analyzing sample file: {$sampleFile}\n";
    analyzeExcelFile($sampleFile);
} else {
    echo "Sample file not found.\n";
}

// Check recent import files
$importDir = __DIR__ . '/storage/app/temp/imports/';
$files = glob($importDir . '*.xlsx');

if (!empty($files)) {
    // Get the most recent file
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    echo "\nAnalyzing most recent import file: " . basename($files[0]) . "\n";
    analyzeExcelFile($files[0]);
}

function analyzeExcelFile($filePath)
{
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        echo "File: " . basename($filePath) . "\n";
        echo "----------------------------------------\n";

        // Get headers (first row)
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $columnIndex = 1;

        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = trim($worksheet->getCell($col . '1')->getValue());
            if (!empty($cellValue)) {
                $headers[$columnIndex] = $cellValue;
                echo "Column {$columnIndex} ({$col}): {$cellValue}\n";
            }
            $columnIndex++;
        }

        echo "\nTotal Headers Found: " . count($headers) . "\n";

        // Show some sample data from row 2
        echo "\nSample Data (Row 2):\n";
        echo "----------------------------------------\n";
        $sampleRow = [];
        $columnIndex = 1;
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = trim($worksheet->getCell($col . '2')->getValue());
            if (isset($headers[$columnIndex])) {
                echo "{$headers[$columnIndex]}: {$cellValue}\n";
            }
            $columnIndex++;
        }

        // Check total rows
        $highestRow = $worksheet->getHighestRow();
        echo "\nTotal Rows: {$highestRow} (including header)\n";
        echo "Data Rows: " . ($highestRow - 1) . "\n";

        // Compare with our field mapping
        echo "\n=== Field Mapping Analysis ===\n";
        checkFieldMapping($headers);

    } catch (Exception $e) {
        echo "Error analyzing file: " . $e->getMessage() . "\n";
    }
}

function checkFieldMapping($headers)
{
    // Our complete field mapping from EnhancedHB837Import
    $completeFieldMapping = [
        'property_name' => ['Property Name', 'PropertyName', 'Name', 'Property'],
        'address' => ['Address', 'Property Address', 'Location', 'Street Address'],
        'city' => ['City'],
        'county' => ['County'],
        'state' => ['State', 'ST'],
        'zip' => ['Zip', 'ZIP', 'Zip Code', 'Postal Code'],
        'property_type' => ['Property Type', 'Type', 'Building Type'],
        'units' => ['Units', 'Unit Count', 'Number of Units', '# Units'],
        'owner_name' => ['Owner Name', 'Owner', 'Property Owner'],
        'phone' => ['Phone', 'Phone Number', 'Contact Phone', 'Tel'],
        'management_company' => ['Management Company', 'Manager', 'Property Manager Company'],
        'property_manager_name' => ['Property Manager Name', 'PM Name', 'Manager Name'],
        'property_manager_email' => ['Property Manager Email', 'PM Email', 'Manager Email'],
        'regional_manager_name' => ['Regional Manager Name', 'RM Name', 'Regional Manager'],
        'regional_manager_email' => ['Regional Manager Email', 'RM Email'],
        'report_status' => ['Report Status', 'Status', 'Project Status'],
        'contracting_status' => ['Contracting Status', 'Contract Status', 'Contract State'],
        'securitygauge_crime_risk' => ['SecurityGauge Crime Risk', 'Crime Risk', 'Risk Level', 'Security Risk'],
        'quoted_price' => ['Quoted Price', 'Quote', 'Price', 'Amount'],
        'sub_fees_estimated_expenses' => ['Sub Fees Estimated Expenses', 'Sub Fees', 'Expenses', 'Additional Fees'],
        'project_net_profit' => ['Project Net Profit', 'Net Profit', 'Profit'],
        'assigned_consultant' => ['Assigned Consultant', 'Consultant', 'Inspector', 'Consultant Name'],
        'consultant_notes' => ['Consultant Notes', 'Inspector Notes', 'Field Notes'],
        'scheduled_date_of_inspection' => ['Scheduled Date of Inspection', 'Inspection Date', 'Schedule Date', 'Date'],
        'report_submitted' => ['Report Submitted', 'Report Date', 'Submission Date'],
        'agreement_submitted' => ['Agreement Submitted', 'Agreement Date'],
        'billing_req_sent' => ['Billing Req Sent', 'Billing Date', 'Invoice Date'],
        'macro_client' => ['Macro Client', 'Client', 'Parent Company'],
        'macro_contact' => ['Macro Contact', 'Client Contact', 'Main Contact'],
        'macro_email' => ['Macro Email', 'Client Email', 'Contact Email'],
        'financial_notes' => ['Financial Notes', 'Money Notes', 'Billing Notes'],
        'general_notes' => ['Notes', 'General Notes', 'Comments', 'Additional Info'],
        'private_notes' => ['Private Notes', 'Internal Notes'],
    ];

    $mappedHeaders = [];
    $unmappedHeaders = [];

    foreach ($headers as $header) {
        $mapped = false;
        foreach ($completeFieldMapping as $dbField => $possibleHeaders) {
            foreach ($possibleHeaders as $possibleHeader) {
                if (strcasecmp($header, $possibleHeader) === 0) {
                    $mappedHeaders[$header] = $dbField;
                    $mapped = true;
                    break 2;
                }
            }
        }

        if (!$mapped) {
            $unmappedHeaders[] = $header;
        }
    }

    echo "Mapped Headers:\n";
    foreach ($mappedHeaders as $excelHeader => $dbField) {
        echo "  ✓ '{$excelHeader}' → {$dbField}\n";
    }

    if (!empty($unmappedHeaders)) {
        echo "\nUnmapped Headers (Need Attention):\n";
        foreach ($unmappedHeaders as $header) {
            echo "  ✗ '{$header}' → NOT MAPPED\n";
        }
    } else {
        echo "\n✓ All headers are properly mapped!\n";
    }

    echo "\nMapping Summary:\n";
    echo "  Mapped: " . count($mappedHeaders) . "\n";
    echo "  Unmapped: " . count($unmappedHeaders) . "\n";
    echo "  Total: " . count($headers) . "\n";
}

echo "\n=== Analysis Complete ===\n";

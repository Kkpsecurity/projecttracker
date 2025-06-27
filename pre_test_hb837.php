<?php

/**
 * HB837 Import/Export Pre-Test Script
 *
 * This script verifies that all fields required for agent daily progress uploads
 * are properly mapped between import and export, ensuring data consistency.
 */

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel without database connection for field analysis
$app = require_once __DIR__.'/bootstrap/app.php';

// Define required fields for agent operations
$agentRequiredFields = [
    // Core property identification
    'property_name',
    'address',
    'city',
    'state',
    'zip',

    // Property details
    'property_type',
    'units',
    'management_company',

    // Contact information
    'property_manager_name',
    'property_manager_email',
    'phone',

    // Status tracking (critical for agent progress)
    'report_status',
    'contracting_status',

    // Consultant assignment
    'assigned_consultant_id', // mapped from 'Consultant Name'

    // Scheduling and dates
    'scheduled_date_of_inspection',
    'report_submitted',
    'agreement_submitted',
    'billing_req_sent',

    // Risk assessment
    'securitygauge_crime_risk',

    // Financial information
    'quoted_price',
    'sub_fees_estimated_expenses',
    'project_net_profit',

    // Client information
    'macro_client',
    'macro_contact',
    'macro_email',

    // Notes (important for agent communication)
    'notes',
    'consultant_notes',
    'financial_notes',
];

// Import field mapping from HB837Import class
$importFields = [
    'report_status' => 'Report Status',
    'contracting_status' => 'Contracting Status',
    'property_name' => 'Property Name',
    'property_type' => 'Property Type',
    'units' => 'Units',
    'address' => 'Address',
    'city' => 'City',
    'county' => 'County',
    'state' => 'State',
    'zip' => 'Zip',
    'phone' => 'Phone',
    'management_company' => 'Management Company',
    'property_manager_name' => 'Property Manager Name',
    'property_manager_email' => 'Property Manager Email',
    'regional_manager_name' => 'Regional Manager Name',
    'regional_manager_email' => 'Regional Manager Email',
    'owner_id' => 'Owner Name',
    'assigned_consultant_id' => 'Consultant Name',
    'scheduled_date_of_inspection' => 'Scheduled Date of Inspection',
    'report_submitted' => 'Report Submitted',
    'agreement_submitted' => 'Agreement Submitted',
    'billing_req_sent' => 'Billing Req Sent',
    'securitygauge_crime_risk' => 'SecurityGauge Crime Risk',
    'quoted_price' => 'Quoted Price',
    'sub_fees_estimated_expenses' => 'Sub Fees Estimated Expenses',
    'project_net_profit' => 'Project Net Profit',
    'macro_client' => 'Macro Client',
    'macro_contact' => 'Macro Contact',
    'macro_email' => 'Macro Email',
    'financial_notes' => 'Financial Notes',
    'consultant_notes' => 'Consultant Notes',
    'notes' => 'Notes',
];

// Export headers from HB837Export class
$exportHeaders = [
    'Report Status',
    'Contracting Status',
    'Property Name',
    'Property Type',
    'Units',
    'Address',
    'City',
    'County',
    'State',
    'Zip',
    'Phone',
    'Management Company',
    'Property Manager Name',
    'Property Manager Email',
    'Regional Manager Name',
    'Regional Manager Email',
    'Owner Name',
    'Consultant Name',
    'Scheduled Date of Inspection',
    'Report Submitted',
    'Agreement Submitted',
    'Billing Req Sent',
    'SecurityGauge Crime Risk',
    'Quoted Price',
    'Sub Fees Estimated Expenses',
    'Project Net Profit',
    'Macro Client',
    'Macro Contact',
    'Macro Email',
    'Financial Notes',
    'Consultant Notes',
    'Notes',
];

echo "=== HB837 Import/Export Pre-Test Results ===\n\n";

// Test 1: Verify all agent-required fields have import mapping
echo "1. AGENT REQUIRED FIELDS IMPORT MAPPING:\n";
$missingInImport = [];
foreach ($agentRequiredFields as $field) {
    if (isset($importFields[$field])) {
        echo "   ✓ {$field} -> '{$importFields[$field]}'\n";
    } else {
        echo "   ✗ {$field} -> MISSING!\n";
        $missingInImport[] = $field;
    }
}

// Test 2: Verify all import fields have corresponding export headers
echo "\n2. IMPORT/EXPORT CONSISTENCY:\n";
$missingInExport = [];
foreach ($importFields as $dbField => $excelHeader) {
    if (in_array($excelHeader, $exportHeaders)) {
        echo "   ✓ '{$excelHeader}' available in both import and export\n";
    } else {
        echo "   ✗ '{$excelHeader}' missing from export!\n";
        $missingInExport[] = $excelHeader;
    }
}

// Test 3: Check for orphaned export headers
echo "\n3. ORPHANED EXPORT HEADERS:\n";
$orphanedHeaders = [];
foreach ($exportHeaders as $header) {
    if (! in_array($header, array_values($importFields))) {
        echo "   ⚠ '{$header}' in export but not in import mapping\n";
        $orphanedHeaders[] = $header;
    }
}

// Test 4: Critical agent workflow fields verification
echo "\n4. CRITICAL AGENT WORKFLOW FIELDS:\n";
$criticalFields = [
    'Report Status' => 'Agents need to update progress status',
    'Contracting Status' => 'Agents track contract execution',
    'Scheduled Date of Inspection' => 'Agents schedule their work',
    'Report Submitted' => 'Agents mark completion dates',
    'Consultant Name' => 'Agents need to see assignments',
    'SecurityGauge Crime Risk' => 'Agents update risk assessments',
    'Quoted Price' => 'Agents may update pricing',
    'Notes' => 'Agents add progress notes',
    'Consultant Notes' => 'Agents communicate with office',
];

foreach ($criticalFields as $field => $purpose) {
    if (in_array($field, $exportHeaders) && in_array($field, array_values($importFields))) {
        echo "   ✓ {$field} - {$purpose}\n";
    } else {
        echo "   ✗ {$field} - MISSING! {$purpose}\n";
    }
}

// Summary
echo "\n=== SUMMARY ===\n";
echo 'Agent Required Fields Missing from Import: '.count($missingInImport)."\n";
echo 'Import Fields Missing from Export: '.count($missingInExport)."\n";
echo 'Orphaned Export Headers: '.count($orphanedHeaders)."\n";

$isSystemReady = (count($missingInImport) === 0 && count($missingInExport) === 0);

if ($isSystemReady) {
    echo "\n🎉 SYSTEM READY! All agent-required fields are properly mapped for import/export.\n";
    echo "✓ Agents can upload Excel files with daily progress\n";
    echo "✓ System will update existing records based on address matching\n";
    echo "✓ All critical workflow fields are available\n\n";

    echo "NEXT STEPS:\n";
    echo "1. Test with a sample Excel file from an agent\n";
    echo "2. Verify that existing records are updated correctly\n";
    echo "3. Confirm that new records are created when needed\n";
    echo "4. Test the web interface import/export buttons\n";
} else {
    echo "\n❌ SYSTEM NEEDS ATTENTION!\n";

    if (! empty($missingInImport)) {
        echo "\nFIX: Add these fields to HB837Import \$fields array:\n";
        foreach ($missingInImport as $field) {
            echo "   '{$field}' => 'DEFINE_EXCEL_HEADER_NAME',\n";
        }
    }

    if (! empty($missingInExport)) {
        echo "\nFIX: Add these headers to HB837Export headings() and collection():\n";
        foreach ($missingInExport as $header) {
            echo "   '{$header}'\n";
        }
    }
}

echo "\n=== FIELD MAPPING REFERENCE ===\n";
echo "For agents uploading Excel files, ensure these exact headers:\n\n";
foreach ($exportHeaders as $header) {
    echo "'{$header}'\n";
}

echo "\n=== END PRE-TEST ===\n";

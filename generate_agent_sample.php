<?php

/**
 * Generate a sample Excel file for agent testing
 */

require_once __DIR__ . '/vendor/autoload.php';

// CSV content representing typical agent daily progress upload
$agentSampleData = [
    // Headers
    [
        'Report Status', 'Contracting Status', 'Property Name', 'Property Type', 'Units',
        'Address', 'City', 'County', 'State', 'Zip', 'Phone', 'Management Company',
        'Property Manager Name', 'Property Manager Email', 'Regional Manager Name', 
        'Regional Manager Email', 'Owner Name', 'Consultant Name', 
        'Scheduled Date of Inspection', 'Report Submitted', 'Agreement Submitted', 
        'Billing Req Sent', 'SecurityGauge Crime Risk', 'Quoted Price', 
        'Sub Fees Estimated Expenses', 'Project Net Profit', 'Macro Client', 
        'Macro Contact', 'Macro Email', 'Financial Notes', 'Consultant Notes', 'Notes'
    ],
    
    // Sample agent progress data
    [
        'in-progress', 'executed', 'Sunset Gardens Apartments', 'garden', '150',
        '123 Sunset Blvd', 'Austin', 'Travis', 'TX', '78701', '512-555-0001', 'Austin Property Management',
        'Jane Smith', 'jane.smith@apm.com', 'Bob Johnson', 'bob.johnson@apm.com', 
        'Sunset Holdings LLC', 'John Doe', '2024-07-15', '', '2024-07-01',
        '', 'medium', '7500.00', '1500.00', '6000.00', 'MegaCorp Properties',
        'Susan Wilson', 'susan@megacorp.com', 'Reviewed pricing with client', 
        'Scheduled for next week - all equipment ready', 'Updated contact info and confirmed access'
    ],
    
    // Agent updating existing record
    [
        'completed', 'executed', 'Oak Ridge Complex', 'midrise', '200',
        '456 Oak Ridge Dr', 'Dallas', 'Dallas', 'TX', '75201', '214-555-0002', 'DFW Management Group',
        'Mike Davis', 'mike.davis@dfwmg.com', 'Linda Chen', 'linda.chen@dfwmg.com',
        'Oak Ridge Investors', 'Jane Smith', '2024-07-10', '2024-07-20', '2024-06-28',
        '2024-07-22', 'high', '12000.00', '2500.00', '9500.00', 'Premier Client Group',
        'David Brown', 'david@premierclient.com', 'Final invoice submitted',
        'Report completed and delivered on time', 'Client very satisfied with results'
    ],
    
    // New property from agent field work
    [
        'quoted', 'quoted', 'Riverside Towers', 'highrise', '300',
        '789 River St', 'Houston', 'Harris', 'TX', '77001', '713-555-0003', 'Houston Premier Management',
        'Sarah Lee', 'sarah.lee@hpm.com', 'Kevin Martinez', 'kevin.martinez@hpm.com',
        'Riverside Development Corp', 'John Doe', '2024-08-01', '', '',
        '', 'low', '15000.00', '3000.00', '12000.00', 'Texas Real Estate Group',
        'Maria Rodriguez', 'maria@treg.com', 'Initial quote provided',
        'Site visit completed - excellent property condition', 'New lead from referral'
    ],
    
    // Agent updating progress on existing project
    [
        'in-review', 'executed', 'Pine Valley Estates', 'garden', '100',
        '321 Pine Valley Way', 'San Antonio', 'Bexar', 'TX', '78201', '210-555-0004', 'Valley Management Services',
        'Tom Wilson', 'tom.wilson@vms.com', 'Rachel Green', 'rachel.green@vms.com',
        'Pine Valley LLC', 'Jane Smith', '2024-07-18', '', '2024-07-05',
        '', 'medium', '6000.00', '1200.00', '4800.00', 'Regional Property Solutions',
        'Carlos Mendez', 'carlos@rps.com', 'Awaiting final approval',
        'Report in review stage - minor revisions needed', 'Following up on pending items'
    ]
];

// Create CSV content
$csvContent = '';
foreach ($agentSampleData as $row) {
    $escapedRow = array_map(function($value) {
        // Escape quotes and wrap in quotes if contains comma
        $value = str_replace('"', '""', $value);
        if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
            return '"' . $value . '"';
        }
        return $value;
    }, $row);
    $csvContent .= implode(',', $escapedRow) . "\n";
}

// Save the sample file
file_put_contents(__DIR__ . '/agent_sample_upload.csv', $csvContent);

echo "✓ Created agent_sample_upload.csv with realistic agent progress data\n";
echo "✓ File contains 4 sample records (1 new, 3 updates)\n";
echo "✓ Includes typical agent workflow scenarios:\n";
echo "  - In-progress property with scheduling updates\n";
echo "  - Completed property with final submission\n";
echo "  - New quoted property from field work\n";
echo "  - Property in review with consultant notes\n\n";

echo "This file can be used to test:\n";
echo "1. Import via web interface\n";
echo "2. Record updates based on address matching\n";
echo "3. New record creation\n";
echo "4. All critical agent fields\n\n";

echo "Next: Use this file with the import feature in the admin panel.\n";

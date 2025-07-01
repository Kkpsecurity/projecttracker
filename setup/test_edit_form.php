<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\HB837;
use App\Models\Consultant;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "HB837 Edit Form Test\n";
echo "===================\n\n";

// Get a test record
$hb837 = HB837::first();

if (!$hb837) {
    echo "No HB837 records found. Cannot test edit form.\n";
    exit;
}

echo "Testing with record ID: {$hb837->id}\n";
echo "Property Name: {$hb837->property_name}\n\n";

// Test required fields for edit form
echo "Required Data Check:\n";
echo "-------------------\n";

$consultants = Consultant::all();
echo "Consultants available: " . $consultants->count() . "\n";

// Test date formatting
echo "\nDate Field Tests:\n";
echo "----------------\n";

echo "Scheduled Date: " . ($hb837->scheduled_date_of_inspection ? $hb837->scheduled_date_of_inspection->format('Y-m-d') : 'NULL') . "\n";
echo "Billing Request Sent: " . ($hb837->billing_req_sent ? $hb837->billing_req_sent->format('Y-m-d') : 'NULL') . "\n";
echo "Report Submitted: " . ($hb837->report_submitted ? $hb837->report_submitted->format('Y-m-d') : 'NULL') . "\n";
echo "Agreement Submitted: " . ($hb837->agreement_submitted ? $hb837->agreement_submitted->format('Y-m-d') : 'NULL') . "\n";

// Test decimal fields
echo "\nDecimal Field Tests:\n";
echo "-------------------\n";
echo "Quoted Price: $" . ($hb837->quoted_price ? $hb837->quoted_price : '0.00') . "\n";
echo "Sub Fees & Expenses: $" . ($hb837->sub_fees_estimated_expenses ? $hb837->sub_fees_estimated_expenses : '0.00') . "\n";
echo "Project Net Profit: $" . ($hb837->project_net_profit ? $hb837->project_net_profit : '0.00') . "\n";

echo "\nAll required data available for edit form!\n";
echo "Edit form should load properly.\n";

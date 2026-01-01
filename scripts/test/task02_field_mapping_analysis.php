<?php
/**
 * Task 02 Testing Script: Field Mapping Analysis
 * Analyzes field mapping quality for TEST SHEET 01
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  TASK 02: FIELD MAPPING ANALYSIS        \n";
echo "==========================================\n\n";

// 1. Field Mapping Configuration Analysis
echo "=== 1. FIELD MAPPING CONFIGURATION ===\n";
$configFile = 'config/hb837_field_mapping.php';

if (file_exists($configFile)) {
    $config = require $configFile;
    echo "✅ Field mapping config loaded\n";
    echo "Total mappings configured: " . count($config) . "\n\n";
    
    echo "Configured field mappings:\n";
    foreach ($config as $csvField => $dbField) {
        echo "├─ '{$csvField}' → '{$dbField}'\n";
    }
} else {
    echo "❌ Field mapping config not found\n";
}

// 2. Database Schema Analysis
echo "\n=== 2. DATABASE SCHEMA ANALYSIS ===\n";
try {
    $columns = Illuminate\Support\Facades\Schema::getColumnListing('hb837');
    echo "HB837 table has " . count($columns) . " columns:\n";
    
    $criticalFields = [
        'property_name' => 'Primary identifier',
        'address' => 'Property location',
        'property_type' => 'Building classification',
        'units' => 'Unit count',
        'quoted_price' => 'Financial data',
        'macro_client' => 'Client information',
        'macro_contact' => 'Contact information',
        'macro_email' => 'Communication',
        'report_status' => 'Workflow status',
        'contracting_status' => 'Contract status'
    ];
    
    foreach ($criticalFields as $field => $description) {
        if (in_array($field, $columns)) {
            echo "✅ {$field} - {$description}\n";
        } else {
            echo "❌ MISSING: {$field} - {$description}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Could not analyze schema: " . $e->getMessage() . "\n";
}

// 3. Sample Data Quality Analysis
echo "\n=== 3. SAMPLE DATA QUALITY ANALYSIS ===\n";

// Get recent records (assuming they're from the import)
$recentRecords = App\Models\HB837::latest()->take(10)->get();

if ($recentRecords->count() > 0) {
    echo "Analyzing " . $recentRecords->count() . " recent records:\n\n";
    
    foreach ($recentRecords as $index => $record) {
        echo "Record " . ($index + 1) . " (ID {$record->id}):\n";
        echo "├─ Property: " . ($record->property_name ?? '❌ NULL') . "\n";
        echo "├─ Address: " . (strlen($record->address ?? '') > 50 ? substr($record->address, 0, 47) . '...' : ($record->address ?? '❌ NULL')) . "\n";
        echo "├─ Type: " . ($record->property_type ?? '❌ NULL') . "\n";
        echo "├─ Units: " . ($record->units ?? '❌ NULL') . "\n";
        echo "├─ Price: $" . number_format($record->quoted_price ?? 0, 2) . "\n";
        echo "├─ Client: " . ($record->macro_client ?? '❌ NULL') . "\n";
        echo "├─ Contact: " . ($record->macro_contact ?? '❌ NULL') . "\n";
        echo "├─ Email: " . ($record->macro_email ?? '❌ NULL') . "\n";
        echo "├─ Report Status: " . ($record->report_status ?? '❌ NULL') . "\n";
        echo "└─ Contract Status: " . ($record->contracting_status ?? '❌ NULL') . "\n\n";
    }
} else {
    echo "No records found for analysis\n";
}

// 4. Field Population Statistics
echo "=== 4. FIELD POPULATION STATISTICS ===\n";
$totalRecords = App\Models\HB837::count();

if ($totalRecords > 0) {
    $stats = [
        'property_name' => App\Models\HB837::whereNotNull('property_name')->where('property_name', '!=', '')->count(),
        'address' => App\Models\HB837::whereNotNull('address')->where('address', '!=', '')->count(),
        'property_type' => App\Models\HB837::whereNotNull('property_type')->where('property_type', '!=', '')->count(),
        'units' => App\Models\HB837::whereNotNull('units')->where('units', '>', 0)->count(),
        'quoted_price' => App\Models\HB837::whereNotNull('quoted_price')->where('quoted_price', '>', 0)->count(),
        'macro_client' => App\Models\HB837::whereNotNull('macro_client')->where('macro_client', '!=', '')->count(),
        'macro_contact' => App\Models\HB837::whereNotNull('macro_contact')->where('macro_contact', '!=', '')->count(),
        'macro_email' => App\Models\HB837::whereNotNull('macro_email')->where('macro_email', '!=', '')->count(),
        'report_status' => App\Models\HB837::whereNotNull('report_status')->where('report_status', '!=', '')->count(),
        'contracting_status' => App\Models\HB837::whereNotNull('contracting_status')->where('contracting_status', '!=', '')->count(),
    ];
    
    echo "Field population rates (out of {$totalRecords} total records):\n";
    foreach ($stats as $field => $count) {
        $percentage = round(($count / $totalRecords) * 100, 1);
        $status = $percentage >= 90 ? '✅' : ($percentage >= 70 ? '⚠️ ' : '❌');
        echo "{$status} {$field}: {$count}/{$totalRecords} ({$percentage}%)\n";
    }
} else {
    echo "No records found for statistics\n";
}

// 5. Data Type Validation
echo "\n=== 5. DATA TYPE VALIDATION ===\n";

// Check for numeric fields
$numericValidation = [
    'units' => App\Models\HB837::whereNotNull('units')->where('units', 'REGEXP', '^[0-9]+$')->count(),
    'quoted_price' => App\Models\HB837::whereNotNull('quoted_price')->where('quoted_price', 'REGEXP', '^[0-9]+\.?[0-9]*$')->count(),
];

foreach ($numericValidation as $field => $validCount) {
    $totalFieldCount = App\Models\HB837::whereNotNull($field)->count();
    if ($totalFieldCount > 0) {
        $percentage = round(($validCount / $totalFieldCount) * 100, 1);
        $status = $percentage >= 95 ? '✅' : '⚠️ ';
        echo "{$status} {$field}: {$validCount}/{$totalFieldCount} valid numeric values ({$percentage}%)\n";
    }
}

// Check email format
$emailValid = App\Models\HB837::whereNotNull('macro_email')
    ->where('macro_email', 'REGEXP', '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$')
    ->count();
$emailTotal = App\Models\HB837::whereNotNull('macro_email')->where('macro_email', '!=', '')->count();

if ($emailTotal > 0) {
    $emailPercentage = round(($emailValid / $emailTotal) * 100, 1);
    $emailStatus = $emailPercentage >= 95 ? '✅' : '⚠️ ';
    echo "{$emailStatus} macro_email: {$emailValid}/{$emailTotal} valid email format ({$emailPercentage}%)\n";
}

// 6. Mapping Quality Assessment
echo "\n=== 6. MAPPING QUALITY ASSESSMENT ===\n";

$qualityScore = 0;
$maxScore = 0;

// Calculate overall quality score
foreach ($stats as $field => $count) {
    $percentage = ($count / $totalRecords) * 100;
    if ($percentage >= 90) $qualityScore += 3;
    elseif ($percentage >= 70) $qualityScore += 2;
    elseif ($percentage >= 50) $qualityScore += 1;
    $maxScore += 3;
}

$overallPercentage = round(($qualityScore / $maxScore) * 100, 1);

echo "Overall mapping quality score: {$qualityScore}/{$maxScore} ({$overallPercentage}%)\n";

if ($overallPercentage >= 90) {
    echo "🎉 EXCELLENT: Field mapping quality is excellent\n";
} elseif ($overallPercentage >= 80) {
    echo "✅ GOOD: Field mapping quality is good\n";
} elseif ($overallPercentage >= 70) {
    echo "⚠️  FAIR: Field mapping quality needs improvement\n";
} else {
    echo "❌ POOR: Field mapping quality requires significant work\n";
}

echo "\nRECOMMENDATION: ";
if ($overallPercentage >= 90) {
    echo "Proceed to Task 03 - Field mapping is ready\n";
} else {
    echo "Review and improve field mappings before Task 03\n";
}

echo "==========================================\n";

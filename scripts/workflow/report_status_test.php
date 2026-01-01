<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HB837;
use Illuminate\Support\Facades\Auth;

echo "🧪 REPORT STATUS IMPORT TEST\n";
echo str_repeat('=', 50) . "\n";

// Set up authentication
Auth::loginUsingId(20);
echo "✅ Authenticated as user ID: " . Auth::id() . "\n";

// Record initial state
$initialCount = HB837::count();
echo "📊 Initial HB837 count: $initialCount\n\n";

// Check current report status distribution
echo "📈 CURRENT REPORT STATUS BREAKDOWN:\n";
$statusCounts = HB837::selectRaw('report_status, COUNT(*) as count')
    ->groupBy('report_status')
    ->orderBy('count', 'desc')
    ->get();

foreach ($statusCounts as $status) {
    $statusName = $status->report_status ?: 'NULL';
    echo "  - {$statusName}: {$status->count}\n";
}

echo "\n";

// Test if we have any test records already
$testProperty = HB837::where('property_name', 'TEST PROPERTY A')->first();
if ($testProperty) {
    echo "🔍 Found existing TEST PROPERTY A:\n";
    echo "  - ID: {$testProperty->id}\n";
    echo "  - Report Status: " . ($testProperty->report_status ?: 'NULL') . "\n";
    echo "  - Contracting Status: " . ($testProperty->contracting_status ?: 'NULL') . "\n";
    echo "  - Assigned Consultant ID: " . ($testProperty->assigned_consultant_id ?: 'NULL') . "\n";
    echo "  - Property Type: " . ($testProperty->property_type ?: 'NULL') . "\n";
    echo "  - Units: " . ($testProperty->units ?: 'NULL') . "\n";
    
    // Now let's test the report_status update manually
    echo "\n🧪 Testing manual report_status update:\n";
    echo "  - Current status: " . ($testProperty->report_status ?: 'NULL') . "\n";
    
    $testProperty->report_status = 'in-progress';
    $testProperty->save();
    
    echo "  - Updated to: in-progress\n";
    
    // Verify the update
    $updatedProperty = HB837::find($testProperty->id);
    echo "  - Verified status: " . ($updatedProperty->report_status ?: 'NULL') . "\n";
    
    if ($updatedProperty->report_status === 'in-progress') {
        echo "  ✅ SUCCESS: Manual update works!\n";
    } else {
        echo "  ❌ FAILURE: Manual update failed!\n";
    }
    
    // Test progression to completed
    echo "\n🧪 Testing progression to completed:\n";
    $testProperty->report_status = 'completed';
    $testProperty->save();
    
    $finalProperty = HB837::find($testProperty->id);
    echo "  - Final status: " . ($finalProperty->report_status ?: 'NULL') . "\n";
    
    if ($finalProperty->report_status === 'completed') {
        echo "  ✅ SUCCESS: Progression to completed works!\n";
    } else {
        echo "  ❌ FAILURE: Progression to completed failed!\n";
    }
    
} else {
    echo "🔍 No TEST PROPERTY A found. Looking for any sample record...\n";
    
    $sampleRecord = HB837::first();
    if ($sampleRecord) {
        echo "📋 Sample record (ID: {$sampleRecord->id}):\n";
        echo "  - Property Name: " . ($sampleRecord->property_name ?: 'NULL') . "\n";
        echo "  - Report Status: " . ($sampleRecord->report_status ?: 'NULL') . "\n";
        echo "  - Contracting Status: " . ($sampleRecord->contracting_status ?: 'NULL') . "\n";
        
        // Test updating this record
        echo "\n🧪 Testing report_status update on sample record:\n";
        $originalStatus = $sampleRecord->report_status;
        echo "  - Original: " . ($originalStatus ?: 'NULL') . "\n";
        
        $sampleRecord->report_status = 'in-review';
        $sampleRecord->save();
        
        $updatedSample = HB837::find($sampleRecord->id);
        echo "  - Updated to: " . ($updatedSample->report_status ?: 'NULL') . "\n";
        
        if ($updatedSample->report_status === 'in-review') {
            echo "  ✅ SUCCESS: Report status update works!\n";
        } else {
            echo "  ❌ FAILURE: Report status update failed!\n";
        }
        
        // Restore original status
        $sampleRecord->report_status = $originalStatus;
        $sampleRecord->save();
        echo "  - Restored to: " . ($originalStatus ?: 'NULL') . "\n";
    }
}

echo "\n";

// Test report_status enum validation
echo "🔬 Testing report_status ENUM validation:\n";
$validStatuses = ['not-started', 'in-progress', 'in-review', 'completed'];

foreach ($validStatuses as $status) {
    $count = HB837::where('report_status', $status)->count();
    echo "  - '$status': $count records\n";
}

// Test invalid status (should fail)
echo "\n🧪 Testing invalid status handling:\n";
$testRecord = HB837::first();
if ($testRecord) {
    try {
        $originalStatus = $testRecord->report_status;
        $testRecord->report_status = 'invalid-status';
        $testRecord->save();
        echo "  ❌ ERROR: Invalid status was accepted!\n";
    } catch (Exception $e) {
        echo "  ✅ SUCCESS: Invalid status correctly rejected\n";
        echo "    Error: " . $e->getMessage() . "\n";
    }
    
    // Restore original
    $testRecord->report_status = $originalStatus;
    $testRecord->save();
}

echo "\n🎯 CRITICAL REPORT STATUS TESTS COMPLETE!\n";

// Check if the field mapping config is working
echo "\n🔧 Config verification:\n";
$config = config('hb837_field_mapping.status_maps.report_status');
if ($config) {
    echo "  ✅ Report status mapping config loaded\n";
    echo "  - Mappings available: " . count($config) . "\n";
    foreach ($config as $input => $output) {
        echo "    '$input' → '$output'\n";
    }
} else {
    echo "  ❌ Report status mapping config NOT loaded\n";
}

echo "\n🏁 TEST COMPLETE!\n";

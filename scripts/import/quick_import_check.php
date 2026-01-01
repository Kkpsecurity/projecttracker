<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HB837;

echo "=== Q4 IMPORT ANALYSIS - QUICK CHECK ===\n\n";

// Today's imports
$today = date('Y-m-d');
$todayImports = HB837::whereDate('created_at', $today)->count();
echo "Records created today: $todayImports\n";

if ($todayImports > 0) {
    echo "\n=== TODAY'S IMPORTS WITH MANAGER DATA ===\n";
    $todaySample = HB837::whereDate('created_at', $today)
        ->take(10)
        ->get(['id', 'property_name', 'property_manager_name', 'property_manager_email', 'regional_manager_name', 'regional_manager_email']);
    
    $managerCount = 0;
    foreach ($todaySample as $sample) {
        echo "\nID: {$sample->id} - {$sample->property_name}\n";
        echo "  PM: " . ($sample->property_manager_name ?: 'None') . " | " . ($sample->property_manager_email ?: 'None') . "\n";
        echo "  RM: " . ($sample->regional_manager_name ?: 'None') . " | " . ($sample->regional_manager_email ?: 'None') . "\n";
        
        if ($sample->property_manager_name || $sample->regional_manager_name) {
            $managerCount++;
        }
    }
    
    echo "\n$managerCount out of " . count($todaySample) . " today's records have manager information.\n";
}

// Check for specific data from Q4 file
echo "\n=== LOOKING FOR Q4 FILE SPECIFIC DATA ===\n";

// Look for Highmark Residential (major client in Q4 file)
$highmarkToday = HB837::whereDate('created_at', $today)
    ->where('macro_client', 'LIKE', '%Highmark%')
    ->count();
echo "Highmark Residential records imported today: $highmarkToday\n";

// Look for specific patterns from Q4 file
$emailDomains = ['@highmarkres.com'];
foreach ($emailDomains as $domain) {
    $domainCount = HB837::whereDate('created_at', $today)
        ->where(function($q) use ($domain) {
            $q->where('property_manager_email', 'LIKE', "%$domain")
              ->orWhere('regional_manager_email', 'LIKE', "%$domain");
        })
        ->count();
    echo "Records with $domain emails imported today: $domainCount\n";
}

// Check if the numbers increased from before
echo "\n=== COMPARISON WITH PRE-IMPORT STATE ===\n";
echo "Expected pre-import manager counts:\n";
echo "- Property Manager Name: 230\n";
echo "- Property Manager Email: 204\n";  
echo "- Regional Manager Name: 201\n";
echo "- Regional Manager Email: 199\n";

$currentPMName = HB837::whereNotNull('property_manager_name')->count();
$currentPMEmail = HB837::whereNotNull('property_manager_email')->count();
$currentRMName = HB837::whereNotNull('regional_manager_name')->count();
$currentRMEmail = HB837::whereNotNull('regional_manager_email')->count();

echo "\nCurrent manager counts:\n";
echo "- Property Manager Name: $currentPMName\n";
echo "- Property Manager Email: $currentPMEmail\n";
echo "- Regional Manager Name: $currentRMName\n";
echo "- Regional Manager Email: $currentRMEmail\n";

echo "\n=== IMPORT SUCCESS ASSESSMENT ===\n";

if ($todayImports > 0) {
    echo "✅ Import occurred today ($todayImports records)\n";
    
    if ($currentPMName > 230 || $currentRMName > 201) {
        echo "✅ Manager field counts increased - IMPORT WAS SUCCESSFUL!\n";
        echo "Craig Gundry's issue has been resolved.\n";
    } else {
        echo "⚠️  Manager field counts didn't increase much.\n";
        echo "May need to investigate if manager fields were properly mapped.\n";
    }
} else {
    echo "ℹ️  No records imported today.\n";
    echo "The import may have happened on a different date.\n";
}

echo "\nAnalysis complete.\n";
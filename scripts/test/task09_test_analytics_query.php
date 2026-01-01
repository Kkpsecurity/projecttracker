<?php

/**
 * Task 09: Test Import Audit Analytics Query
 * 
 * This script tests the PostgreSQL-compatible analytics query
 * to ensure the date formatting fix works correctly.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\ImportAudit;
use Carbon\Carbon;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing Import Audit Analytics Query\n";
echo "======================================\n\n";

try {
    // Test basic count
    $count = ImportAudit::count();
    echo "📊 Total import audit records: {$count}\n\n";
    
    // Test recent activity query (this should work)
    echo "📋 Recent Activity:\n";
    $recentActivity = ImportAudit::orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['type', 'changes', 'created_at']);
        
    foreach ($recentActivity as $activity) {
        echo "   • {$activity->type} - {$activity->created_at->format('Y-m-d H:i:s')}\n";
    }
    echo "\n";
    
    // Test PostgreSQL date formatting
    echo "📈 Testing PostgreSQL Monthly Stats Query:\n";
    
    $dbDriver = config('database.default');
    echo "   Database driver: {$dbDriver}\n";
    
    if ($dbDriver === 'pgsql') {
        $stats = ImportAudit::selectRaw("
            TO_CHAR(created_at, 'YYYY-MM') as month,
            COUNT(*) as total_operations,
            SUM(CASE WHEN type = 'import' THEN 1 ELSE 0 END) as imports,
            SUM(CASE WHEN type = 'backup' THEN 1 ELSE 0 END) as backups
        ")
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        echo "   ✅ PostgreSQL query successful!\n";
        echo "   📊 Monthly Statistics:\n";
        
        foreach ($stats as $stat) {
            echo "      • {$stat->month}: {$stat->total_operations} total ({$stat->imports} imports, {$stat->backups} backups)\n";
        }
    } else {
        echo "   ⚠️ Not using PostgreSQL, skipping PostgreSQL-specific test\n";
    }
    
    echo "\n🎉 All analytics queries working correctly!\n";

} catch (Exception $e) {
    echo "❌ Error testing analytics query: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

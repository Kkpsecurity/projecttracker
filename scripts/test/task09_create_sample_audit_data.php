<?php

/**
 * Task 09: Create Sample Import Audit Data
 * 
 * This script creates sample data for the import_audits table
 * to test the analytics functionality.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\ImportAudit;
use Carbon\Carbon;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Creating Sample Import Audit Data\n";
echo "===================================\n\n";

try {
    // Create some sample import audit records
    $records = [
        [
            'import_id' => 'batch_001_' . date('Ymd'),
            'type' => 'import',
            'changes' => json_encode([
                'records_imported' => 25,
                'source' => 'TEST_SHEET_01',
                'duration' => '2.5 minutes',
                'success_rate' => '100%'
            ]),
            'user_id' => 1,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10)
        ],
        [
            'import_id' => 'backup_001_' . date('Ymd'),
            'type' => 'backup',
            'changes' => json_encode([
                'backup_size' => '2.5MB',
                'records_backed_up' => 150,
                'backup_location' => 'storage/backups',
                'compression' => 'gzip'
            ]),
            'user_id' => 1,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8)
        ],
        [
            'import_id' => 'batch_002_' . date('Ymd'),
            'type' => 'import',
            'changes' => json_encode([
                'records_imported' => 12,
                'source' => 'TEST_SHEET_02',
                'duration' => '1.2 minutes',
                'success_rate' => '100%'
            ]),
            'user_id' => 1,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5)
        ],
        [
            'import_id' => 'backup_002_' . date('Ymd'),
            'type' => 'backup',
            'changes' => json_encode([
                'backup_size' => '2.8MB',
                'records_backed_up' => 162,
                'backup_location' => 'storage/backups',
                'compression' => 'gzip'
            ]),
            'user_id' => 1,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3)
        ],
        [
            'import_id' => 'batch_003_' . date('Ymd'),
            'type' => 'import',
            'changes' => json_encode([
                'records_imported' => 18,
                'source' => 'TEST_SHEET_03',
                'duration' => '1.8 minutes',
                'success_rate' => '100%'
            ]),
            'user_id' => 1,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1)
        ]
    ];

    foreach ($records as $record) {
        $audit = ImportAudit::create($record);
        echo "✅ Created audit record: {$record['type']} - {$record['import_id']}\n";
    }

    echo "\n🎉 Sample import audit data created successfully!\n";
    
    // Verify the data
    $count = ImportAudit::count();
    echo "📊 Total import audit records: {$count}\n";
    
    $recent = ImportAudit::orderBy('created_at', 'desc')->take(3)->get();
    echo "\n📋 Recent audit records:\n";
    foreach ($recent as $audit) {
        echo "   • {$audit->type} - {$audit->import_id} ({$audit->created_at->format('Y-m-d H:i')})\n";
    }

} catch (Exception $e) {
    echo "❌ Error creating sample data: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

/**
 * Real-time import tracking script
 * This will monitor the database changes during your import process
 */

class ImportTracker {
    private $logFile;
    private $startTime;
    
    public function __construct() {
        $this->startTime = now();
        $this->logFile = storage_path('logs/import_tracking_' . date('Y-m-d_H-i-s') . '.log');
        $this->log("=== IMPORT TRACKING STARTED ===");
        $this->log("Start Time: " . $this->startTime);
        $this->log("Log File: " . $this->logFile);
    }
    
    public function log($message) {
        $timestamp = now()->format('Y-m-d H:i:s.u');
        $logMessage = "[{$timestamp}] {$message}\n";
        echo $logMessage;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    public function trackInitialState() {
        $this->log("--- INITIAL STATE CHECK ---");
        
        // Check hb837 table
        $hb837Count = DB::table('hb837')->count();
        $this->log("HB837 records: {$hb837Count}");
        
        // Check hb837_files table
        try {
            $filesCount = DB::table('hb837_files')->count();
            $this->log("HB837_files records: {$filesCount}");
        } catch (Exception $e) {
            $this->log("HB837_files table check failed: " . $e->getMessage());
        }
        
        // Check consultants
        try {
            $consultantsCount = DB::table('consultants')->count();
            $this->log("Consultants records: {$consultantsCount}");
        } catch (Exception $e) {
            $this->log("Consultants table check failed: " . $e->getMessage());
        }
        
        // Check users
        try {
            $usersCount = DB::table('users')->count();
            $this->log("Users records: {$usersCount}");
        } catch (Exception $e) {
            $this->log("Users table check failed: " . $e->getMessage());
        }
        
        $this->log("--- INITIAL STATE COMPLETE ---");
    }
    
    public function monitorChanges($intervalSeconds = 2) {
        $this->log("--- MONITORING STARTED (checking every {$intervalSeconds}s) ---");
        $this->log("Press Ctrl+C to stop monitoring");
        
        $lastHb837Count = 0;
        $lastFilesCount = 0;
        $checkCount = 0;
        
        while (true) {
            $checkCount++;
            $currentTime = now()->format('H:i:s');
            
            try {
                // Check hb837 table
                $currentHb837Count = DB::table('hb837')->count();
                
                // Check hb837_files table
                $currentFilesCount = 0;
                try {
                    $currentFilesCount = DB::table('hb837_files')->count();
                } catch (Exception $e) {
                    // Table might not exist
                }
                
                // Log changes
                if ($currentHb837Count != $lastHb837Count || $currentFilesCount != $lastFilesCount) {
                    $this->log("CHANGE DETECTED #{$checkCount} at {$currentTime}:");
                    $this->log("  HB837: {$lastHb837Count} -> {$currentHb837Count} (+" . ($currentHb837Count - $lastHb837Count) . ")");
                    $this->log("  FILES: {$lastFilesCount} -> {$currentFilesCount} (+" . ($currentFilesCount - $lastFilesCount) . ")");
                    
                    // If records were added, get details of the latest ones
                    if ($currentHb837Count > $lastHb837Count) {
                        $this->logLatestRecords($currentHb837Count - $lastHb837Count);
                    }
                    
                    $lastHb837Count = $currentHb837Count;
                    $lastFilesCount = $currentFilesCount;
                } else {
                    // Silent check, just update counts for first run
                    if ($checkCount == 1) {
                        $lastHb837Count = $currentHb837Count;
                        $lastFilesCount = $currentFilesCount;
                        $this->log("Monitoring baseline set: HB837={$currentHb837Count}, FILES={$currentFilesCount}");
                    }
                }
                
            } catch (Exception $e) {
                $this->log("ERROR during monitoring: " . $e->getMessage());
            }
            
            sleep($intervalSeconds);
        }
    }
    
    private function logLatestRecords($count) {
        try {
            $latestRecords = DB::table('hb837')
                ->orderBy('id', 'desc')
                ->limit($count)
                ->get(['id', 'property_name', 'address', 'report_status', 'assigned_consultant_id', 'created_at']);
            
            $this->log("  Latest {$count} record(s) added:");
            foreach ($latestRecords as $record) {
                $this->log("    ID: {$record->id}, Property: {$record->property_name}, Address: {$record->address}, Status: {$record->report_status}, Consultant: {$record->assigned_consultant_id}");
            }
        } catch (Exception $e) {
            $this->log("  Could not fetch latest records: " . $e->getMessage());
        }
    }
    
    public function finalSummary() {
        $this->log("--- FINAL SUMMARY ---");
        $endTime = now();
        $duration = $this->startTime->diffInSeconds($endTime);
        
        $this->log("End Time: {$endTime}");
        $this->log("Total Duration: {$duration} seconds");
        
        // Final counts
        $finalHb837Count = DB::table('hb837')->count();
        $this->log("Final HB837 records: {$finalHb837Count}");
        
        try {
            $finalFilesCount = DB::table('hb837_files')->count();
            $this->log("Final HB837_files records: {$finalFilesCount}");
        } catch (Exception $e) {
            $this->log("HB837_files final count failed: " . $e->getMessage());
        }
        
        $this->log("=== IMPORT TRACKING ENDED ===");
    }
}

// Check command line arguments
$action = $argv[1] ?? 'monitor';

$tracker = new ImportTracker();

switch ($action) {
    case 'initial':
        $tracker->trackInitialState();
        break;
        
    case 'monitor':
        $tracker->trackInitialState();
        $tracker->monitorChanges(1); // Check every 1 second
        break;
        
    case 'summary':
        $tracker->finalSummary();
        break;
        
    default:
        echo "Usage: php track_import_process.php [initial|monitor|summary]\n";
        echo "  initial - Check initial state only\n";
        echo "  monitor - Continuously monitor changes (default)\n";
        echo "  summary - Show final summary\n";
}

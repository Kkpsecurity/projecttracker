<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\ImportAudit;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Exports\DynamicBackupExport;
use Maatwebsite\Excel\Facades\Excel;

class AutoBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:auto {--tables=* : Specific tables to backup}';

    /**
     * The console command description.
     */
    protected $description = 'Create an automatic backup using the same system as manual backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('backup.enable_cron', true)) {
            $this->info('Auto backup is disabled in configuration.');
            Log::info('Auto backup skipped - disabled in configuration');
            return Command::SUCCESS;
        }

        if (!cache('backup_cron_enabled', true)) {
            $this->info('Auto backup is disabled via admin setting.');
            Log::info('Auto backup skipped - disabled by admin');
            return Command::SUCCESS;
        }

        $this->info('Starting automatic backup...');
        Log::info('Auto backup started');

        try {
            // Default tables to backup
            $tables = $this->option('tables') ?: ['hb837', 'consultants', 'owners'];

            $this->info('Backing up tables: ' . implode(', ', $tables));

            // Generate backup name
            $name = 'Auto_Backup_' . date('Y-m-d_H-i-s');

            // Create backup configuration
            $config = [
                'name' => $this->cleanFileName($name),
                'tables' => $tables,
            ];

            // Perform the backup using the same method as manual backups
            $backupData = $this->performBackup($config);

            // Record the backup
            $audit = $this->recordBackup($backupData);

            // Create audit record for auto backup
            $this->recordAutoBackupAudit($backupData, $audit);

            $this->info("✅ Backup completed successfully: {$audit->filename}");
            $this->info("📊 Records backed up: {$backupData['record_count']}");
            $this->info("💾 File size: " . $this->formatBytes($backupData['size']));

            Log::info('Auto backup completed successfully', [
                'filename' => $audit->filename,
                'size' => $backupData['size'],
                'record_count' => $backupData['record_count'],
                'tables' => $tables
            ]);

            // Clean up old backups (keep last 10)
            $this->cleanupOldBackups();

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("❌ Auto backup failed: " . $e->getMessage());

            Log::error('Auto backup failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Record failed backup audit
            $this->recordFailedBackupAudit($e);

            return Command::FAILURE;
        }
    }

    /**
     * Perform backup using the same logic as manual backups
     */
    protected function performBackup(array $config): array
    {
        $name = $config['name'];
        $tables = $config['tables'];

        $backup = [
            'excel_path' => null,
            'record_count' => $this->countRecords($tables),
            'size' => 0,
        ];

        $excelFile = "{$name}.xlsx";
        $path = "backups/{$excelFile}";

        Excel::store(new DynamicBackupExport($tables), $path);

        $backup['excel_path'] = $path;
        $backup['size'] = Storage::size($path);
        $backup['tables'] = array_map('trim', $tables);

        return $backup;
    }

    /**
     * Record backup in database
     */
    protected function recordBackup(array $data): Backup
    {
        return Backup::create([
            'uuid' => Str::uuid(),
            'name' => basename($data['excel_path'], '.xlsx'),
            'filename' => basename($data['excel_path']),
            'size' => $data['size'],
            'record_count' => $data['record_count'],
            'tables' => $data['tables'] ?? [],
            'status' => 'completed',
            'user_id' => 1, // System user for auto backups
        ]);
    }

    /**
     * Record auto backup audit
     */
    protected function recordAutoBackupAudit(array $backupData, Backup $backup): void
    {
        ImportAudit::create([
            'import_id' => Str::uuid(),
            'type' => 'auto_backup',
            'changes' => [
                'filename' => basename($backupData['excel_path']),
                'size' => $backupData['size'],
                'record_count' => $backupData['record_count'],
                'tables' => $backupData['tables'],
                'status' => 'completed',
                'trigger' => 'cron',
                'backup_uuid' => $backup->uuid,
                'message' => 'Automatic backup completed successfully',
            ],
            'user_id' => 1, // System user
        ]);
    }

    /**
     * Record failed backup audit
     */
    protected function recordFailedBackupAudit(\Throwable $e): void
    {
        ImportAudit::create([
            'import_id' => Str::uuid(),
            'type' => 'auto_backup_failed',
            'changes' => [
                'error' => $e->getMessage(),
                'trigger' => 'cron',
                'status' => 'failed',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
            'user_id' => 1,
        ]);
    }

    /**
     * Count records in tables
     */
    protected function countRecords(array $tables): int
    {
        return array_reduce($tables, function($sum, $table) {
            try {
                return $sum + DB::table($table)->count();
            } catch (\Exception $e) {
                Log::warning("Could not count records in table: $table", ['error' => $e->getMessage()]);
                return $sum;
            }
        }, 0);
    }

    /**
     * Clean filename
     */
    protected function cleanFileName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);
    }

    /**
     * Format bytes for display
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);
        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    /**
     * Clean up old backups (keep latest 10 auto backups)
     */
    protected function cleanupOldBackups(): void
    {
        try {
            $autoBackups = Backup::where('name', 'like', 'Auto_Backup_%')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($autoBackups->count() > 10) {
                $oldBackups = $autoBackups->slice(10);

                foreach ($oldBackups as $backup) {
                    $filePath = "backups/{$backup->filename}";

                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                        $this->line("🗑️ Deleted old backup: {$backup->filename}");
                    }

                    $backup->delete();
                }

                $this->info("🧹 Cleaned up " . $oldBackups->count() . " old backups");
                Log::info("Auto backup cleanup completed", ['deleted_count' => $oldBackups->count()]);
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ Cleanup failed: " . $e->getMessage());
            Log::warning('Auto backup cleanup failed', ['error' => $e->getMessage()]);
        }
    }
}

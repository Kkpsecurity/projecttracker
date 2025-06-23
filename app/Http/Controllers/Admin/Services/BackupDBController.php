<?php

namespace App\Http\Controllers\Admin\Services;

use App\Models\HB837;
use App\Models\Backup;
use App\Models\ImportAudit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\HB837Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Exports\DynamicBackupExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class BackupDBController extends Controller
{
    public function index()
    {
        $backups = Backup::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        $importAudits = ImportAudit::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.services.backup.index', [
            'backups' => $backups,
            'stats' => $this->getBackupStats($backups),
            'systemStats' => $this->getSystemStats(),
            'importAudits' => $importAudits,
            'recentActivity' => $this->getRecentActivity(),
        ]);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'tables' => 'required|array|min:1',
            'tables.*' => 'string',
        ]);

        if ($validator->fails()) {
            Log::warning('Backup validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            // Generate a default name if none provided
            $name = $request->input('name') ?: 'Backup_' . date('Y-m-d_H-i-s');

            $config = [
                'name' => $this->cleanFileName($name),
                'tables' => $request->input('tables'),
            ];

            $backupData = $this->performBackup($config);
            $audit = $this->recordBackup($backupData);

            return response()->json([
                'success' => true,
                'message' => 'Backup completed successfully',
                'audit' => $audit,
            ]);

        } catch (\Throwable $e) {
            Log::error('Backup failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
            'truncate' => 'sometimes|in:on'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $file = $request->file('csv_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->withErrors(['error' => 'No valid file uploaded.']);
        }

        logger()->info('Uploaded file details:', [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        try {
            $import = new HB837Import();

            // Use DB transaction for safety
            DB::beginTransaction();

            try {
                // Handle truncation with proper authorization INSIDE the transaction
                if ($request->has('truncate') && $request->input('truncate') == 'on') {
                    if (!auth()->user() || !(auth()->id() == 1 || auth()->id() == 2)) {
                        throw new \Exception('You do not have permission to truncate the HB837 table. This feature is restricted to administrators for debugging purposes.');
                    }

                    logger()->info('Truncating HB837 table before import.', ['user_id' => auth()->id()]);
                    HB837::truncate();

                    // Force the import class to treat all records as new
                    $import->setTruncateMode(true);
                }

                Excel::import($import, $file);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            $imported = $import->importedCount; // New records
            $updated = $import->updatedCount;   // Updated records
            $skipped = $import->skippedCount;   // Skipped records

            $wasTruncated = $request->has('truncate') && $request->input('truncate') == 'on';

            // Properly formatted message with truncate info
            $message = "Import Successful: {$imported} new records imported, {$updated} records updated, {$skipped} skipped.";
            if ($wasTruncated) {
                $message = "Import Successful (Fresh Import - Table Truncated): {$imported} new records imported, {$updated} records updated, {$skipped} skipped.";
            }

            // Create ImportAudit record
            $audit = ImportAudit::create([
                'import_id' => Str::uuid(),
                'type' => $file->getClientOriginalExtension(),
                'changes' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'skipped_details' => $import->skippedProperties ?? [],
                    'message' => $message,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'truncated' => $wasTruncated,
                ],
                'user_id' => auth()->id(),
            ]);

            logger()->info('Import Summary:', [
                'imported' => $imported,
                'updated' => $updated,
                'skipped' => $skipped,
                'truncated' => $wasTruncated,
                'skipped_details' => $import->skippedProperties ?? [],
                'message' => $message,
                'audit_id' => $audit->id,
                'file_name' => $file->getClientOriginalName(),
            ]);

            return redirect()->route('admin.hb837.index')->with('success', $message);

        } catch (\Exception $e) {
            logger()->error('Import Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Create failed audit record
            ImportAudit::create([
                'import_id' => Str::uuid(),
                'type' => 'import_failed',
                'changes' => [
                    'error' => $e->getMessage(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ],
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Error importing file: ' . $e->getMessage()]);
        }
    }

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
            'user_id' => auth()->id(),
        ]);
    }

    protected function recordAudit(array $data): ImportAudit
    {
        return ImportAudit::create([
            'import_id' => Str::uuid(),
            'type' => 'backup',
            'changes' => [
                'filename' => basename($data['excel_path']),
                'size' => $data['size'],
                'record_count' => $data['record_count'],
                'status' => 'completed',
            ],
            'user_id' => auth()->id(),
        ]);
    }

    protected function getBackupStats($backups): array
    {
        $totalSize = $backups->sum('size');
        $success = $this->calculateSuccessRate($backups);

        $last = $backups->first();
        $lastBackup = $last && $last->created_at ? $last->created_at->diffForHumans() : 'Never';

        $totalSizeMB = round($totalSize / 1024 / 1024, 2);

        // Additional useful stats
        $thisMonth = $backups->filter(function($backup) {
            return $backup->created_at && $backup->created_at->isCurrentMonth();
        })->count();

        $avgSize = $backups->isNotEmpty() ? round($totalSize / $backups->count(), 2) : 0;
        $avgSizeMB = round($avgSize / 1024 / 1024, 2);

        // Storage efficiency
        $storageUsed = $this->formatBytes($totalSize);
        $oldestBackup = $backups->last();
        $retentionPeriod = $oldestBackup && $oldestBackup->created_at
            ? $oldestBackup->created_at->diffForHumans()
            : 'No old backups';

        return [
            'total_backups' => $backups->total(),
            'successful_backups' => $backups->filter(function($b) { return $b->status === 'completed'; })->count(),
            'failed_backups' => $backups->filter(function($b) { return $b->status === 'failed'; })->count(),
            'this_month' => $thisMonth,
            'last_backup' => $lastBackup,
            'last_backup_ago' => $lastBackup,
            'total_size' => $this->formatBytes($totalSize),
            'total_size_mb' => $totalSizeMB,
            'average_size' => $this->formatBytes($avgSize),
            'average_size_mb' => $avgSizeMB,
            'success_rate' => "{$success}%",
            'success_rate_numeric' => $success,
            'storage_used' => $storageUsed,
            'retention_period' => $retentionPeriod,
        ];
    }    /**
     * Get comprehensive system statistics for the dashboard
     */
    protected function getSystemStats(): array
    {
        $hb837Count = HB837::count();
        $consultantsCount = DB::table('consultants')->count();
        $ownersCount = DB::table('owners')->count();
        $usersCount = DB::table('users')->count();

        // HB837 specific stats (relevant to backups/imports)
        $hb837Stats = [
            'total_properties' => $hb837Count,
            'active_projects' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])->count(),
            'completed_projects' => HB837::where('report_status', 'completed')->count(),
            'recent_imports' => ImportAudit::where('created_at', '>=', now()->subDays(30))
                ->where('type', '!=', 'backup')
                ->count(),
            'data_health_score' => $this->calculateDataHealthScore($hb837Count),
        ];

        // Import/Export activity stats
        $importStats = [
            'total_imports' => ImportAudit::where('type', '!=', 'backup')->count(),
            'successful_imports' => ImportAudit::where('type', '!=', 'backup')
                ->whereJsonDoesntContain('changes->error', null)
                ->count(),
            'failed_imports' => ImportAudit::where('type', '!=', 'backup')
                ->whereJsonContains('changes->error', null)
                ->count(),
            'this_month_imports' => ImportAudit::where('type', '!=', 'backup')
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        // Status distribution (relevant for data integrity)
        $statusDistribution = [
            'not_started' => HB837::where('report_status', 'not-started')->count(),
            'in_progress' => HB837::where('report_status', 'in-progress')->count(),
            'in_review' => HB837::where('report_status', 'in-review')->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
        ];

        // Monthly backup/import trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                'backups_created' => Backup::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'imports_performed' => ImportAudit::where('type', '!=', 'backup')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'data_volume' => HB837::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return [
            'hb837' => $hb837Stats,
            'imports' => $importStats,
            'status_distribution' => $statusDistribution,
            'monthly_trends' => $monthlyTrends,
            'system_counts' => [
                'consultants' => $consultantsCount,
                'owners' => $ownersCount,
                'users' => $usersCount,
            ],
        ];
    }

    /**
     * Calculate a simple data health score based on completeness
     */
    private function calculateDataHealthScore(int $totalRecords): int
    {
        if ($totalRecords === 0) return 0;

        $completeRecords = HB837::whereNotNull('property_name')
            ->whereNotNull('address')
            ->whereNotNull('report_status')
            ->count();

        return intval(round(($completeRecords / $totalRecords) * 100));
    }

    /**
     * Get recent system activity for dashboard
     */
    protected function getRecentActivity(): array
    {
        $recentBackups = Backup::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($backup) {
                return [
                    'type' => 'backup',
                    'action' => 'Backup Created',
                    'description' => "Backup '{$backup->name}' was created",
                    'user' => $backup->user->name ?? 'Unknown',
                    'timestamp' => $backup->created_at,
                    'status' => $backup->status,
                    'icon' => 'fa-download',
                    'color' => 'success'
                ];
            });

        $recentImports = ImportAudit::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($import) {
                $changes = $import->changes ?? [];
                $imported = $changes['imported'] ?? 0;
                $updated = $changes['updated'] ?? 0;

                return [
                    'type' => 'import',
                    'action' => 'Data Import',
                    'description' => "Imported {$imported} new, updated {$updated} records",
                    'user' => $import->user->name ?? 'Unknown',
                    'timestamp' => $import->created_at,
                    'status' => isset($changes['error']) ? 'failed' : 'success',
                    'icon' => 'fa-upload',
                    'color' => isset($changes['error']) ? 'danger' : 'info'
                ];
            });

        $recentHB837 = HB837::with('user')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'action' => 'Project Updated',
                    'description' => "Property: {$project->property_name} - {$project->address}",
                    'user' => $project->user->name ?? 'System',
                    'timestamp' => $project->updated_at,
                    'status' => $project->report_status,
                    'icon' => 'fa-building',
                    'color' => 'primary'
                ];
            });

        return $recentBackups
            ->concat($recentImports)
            ->concat($recentHB837)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->toArray();
    }

    public function deleteFile(Request $request, string $uuid)
    {
        // Validate UUID format
        if (!Str::isUuid($uuid)) {
            return redirect()->back()->with('error', 'Invalid backup identifier.');
        }

        $backup = Backup::where('uuid', $uuid)->firstOrFail();

        // Check user permissions (only creator or admin can delete)
        if (auth()->id() !== $backup->user_id && !(auth()->id() == 1 || auth()->id() == 2)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this backup.');
        }

        $filePath = $backup->filename ? "backups/{$backup->filename}" : null;

        if ($filePath && Storage::exists($filePath)) {
            try {
                Storage::delete($filePath);
                logger()->info('Backup file deleted', [
                    'file_path' => $filePath,
                    'backup_id' => $backup->id,
                    'deleted_by' => auth()->id()
                ]);
            } catch (\Exception $e) {
                logger()->error('Failed to delete backup file', [
                    'file_path' => $filePath,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', 'Failed to delete backup file.');
            }
        }

        $backup->delete();

        return redirect()->back()->with('success', 'Backup file deleted successfully.');
    }


    public function restore(string $uuid)
    {
        // Validate UUID format
        if (!Str::isUuid($uuid)) {
            return redirect()->back()->with('error', 'Invalid backup identifier.');
        }

        // Only admin can restore
        if (!(auth()->id() == 1 || auth()->id() == 2)) {
            return redirect()->back()->with('error', 'You do not have permission to restore backups. This feature is restricted to administrators.');
        }

        $backup = Backup::where('uuid', $uuid)->firstOrFail();
        $filePath = $backup->filename ? "backups/{$backup->filename}" : null;

        if (!$filePath || !Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        $imported = 0;

        // Use database transaction for safety
        DB::beginTransaction();

        try {
            $data = Excel::toArray(new \stdClass(), Storage::path($filePath));

            foreach ($data as $sheetIndex => $sheet) {
                if (empty($sheet)) continue;

                $headers = array_shift($sheet); // Remove headers
                $table = $this->inferTableNameFromHeaders($headers, $sheetIndex);

                if (!$table || empty($sheet)) {
                    logger()->warning('Skipped sheet during restore', [
                        'sheet_index' => $sheetIndex,
                        'inferred_table' => $table,
                        'headers' => $headers
                    ]);
                    continue;
                }

                // Validate table exists before truncating
                if (!$this->tableExists($table)) {
                    logger()->warning('Table does not exist, skipping', ['table' => $table]);
                    continue;
                }

                DB::table($table)->truncate();

                // Convert rows to associative arrays using headers
                $rows = array_map(function($row) use ($headers) {
                    return array_combine($headers, $row);
                }, $sheet);

                if (!empty($rows)) {
                    // Insert in chunks to avoid memory issues
                    $chunks = array_chunk($rows, 500);
                    foreach ($chunks as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                    $imported += count($rows);
                }
            }

            DB::commit();

            $message = "Restore Complete: {$imported} records inserted. Data fully replaced.";

            ImportAudit::create([
                'import_id' => Str::uuid(),
                'type' => 'restore',
                'changes' => [
                    'imported' => $imported,
                    'updated' => 0,
                    'skipped' => 0,
                    'skipped_details' => [],
                    'message' => $message,
                    'backup_file' => $backup->filename,
                    'backup_uuid' => $uuid,
                ],
                'user_id' => auth()->id(),
            ]);

            logger()->info('Restore completed successfully', [
                'imported' => $imported,
                'backup_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', $message);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Restore failed', [
                'error' => $e->getMessage(),
                'backup_uuid' => $uuid,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Create failed audit record
            ImportAudit::create([
                'import_id' => Str::uuid(),
                'type' => 'restore_failed',
                'changes' => [
                    'error' => $e->getMessage(),
                    'backup_file' => $backup->filename,
                    'backup_uuid' => $uuid,
                ],
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    protected function inferTableNameFromHeaders(array $headers, int $sheetIndex): ?string
    {
        // Map known header patterns to table names
        $tableMapping = [
            'hb837' => ['address', 'property_name', 'report_status'],
            'consultants' => ['first_name', 'last_name', 'email'],
            'owners' => ['name', 'email', 'company_name'],
            'clients' => ['name', 'email', 'phone'],
            'users' => ['name', 'email', 'email_verified_at'],
        ];

        $lowercaseHeaders = array_map('strtolower', $headers);

        foreach ($tableMapping as $table => $requiredFields) {
            $matches = 0;
            foreach ($requiredFields as $field) {
                if (in_array(strtolower($field), $lowercaseHeaders)) {
                    $matches++;
                }
            }

            // If at least 2 fields match, assume this is the correct table
            if ($matches >= 2) {
                return $table;
            }
        }

        Log::warning('Could not infer table name from headers', [
            'headers' => $headers,
            'sheet_index' => $sheetIndex
        ]);

        return null;
    }

    protected function inferTableName(object $sheet): ?string
    {
        // Legacy method - kept for backward compatibility
        if (method_exists($sheet, 'getTitle')) {
            return $sheet->getTitle();
        }
        return null;
    }


    public function download($filename)
    {
        $path = "backups/{$filename}";

        if (!Storage::exists($path)) {
            abort(404, 'Backup file not found');
        }

        return Storage::download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    protected function countRecords(array $tables): int
    {
        return array_reduce($tables, function($sum, $table) {
            return $sum + DB::table($table)->count();
        }, 0);
    }

    protected function calculateSuccessRate($backups): int
    {
        if ($backups->isEmpty()) return 0;
        $successful = $backups->filter(function($b) {
            return ($b->changes['status'] ?? '') === 'completed';
        })->count();
        return intval(round(($successful / $backups->count()) * 100));
    }

    protected function cleanFileName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);
        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    /**
     * Check if a table exists in the database
     */
    protected function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Exception $e) {
            logger()->error('Error checking table existence', [
                'table' => $table,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Test endpoint to verify dashboard data
     */
    public function testDashboard()
    {
        if (app()->environment() !== 'local') {
            abort(404);
        }

        $backups = Backup::query()->latest()->paginate(10);
        $stats = $this->getBackupStats($backups);
        $systemStats = $this->getSystemStats();
        $recentActivity = $this->getRecentActivity();

        return response()->json([
            'backup_stats' => $stats,
            'system_stats' => $systemStats,
            'recent_activity' => array_slice($recentActivity, 0, 3),
            'summary' => [
                'total_hb837_projects' => $systemStats['hb837']['total_properties'],
                'active_projects' => $systemStats['hb837']['active_projects'],
                'total_imports' => $systemStats['imports']['total_imports'],
                'backup_success_rate' => $stats['success_rate'],
                'data_health_score' => $systemStats['hb837']['data_health_score'],
            ]
        ]);
    }

    /**
     * Debug method to test backup validation
     */
    public function testBackupValidation(Request $request)
    {
        $testData = [
            'name' => 'Test Backup',
            'tables' => ['hb837', 'consultants'],
            'include_files' => true
        ];

        $validator = Validator::make($testData, [
            'name' => 'nullable|string|max:255',
            'tables' => 'required|array|min:1',
            'tables.*' => 'string',
        ]);

        return response()->json([
            'test_data' => $testData,
            'validation_passed' => !$validator->fails(),
            'errors' => $validator->errors()->toArray(),
            'message' => $validator->fails() ? 'Validation failed' : 'Validation passed'
        ]);
    }

    /**
     * Test the actual backup save method with simulated data
     */
    public function testBackupSave()
    {
        if (app()->environment() !== 'local') {
            abort(404);
        }

        // Simulate the form data that would be sent
        $requestData = [
            'name' => 'Test Manual Backup',
            'tables' => ['hb837', 'consultants'],
            '_token' => csrf_token()
        ];

        $request = new Request($requestData);

        try {
            $result = $this->save($request);

            return response()->json([
                'test_status' => 'success',
                'result' => $result->getData(),
                'status_code' => $result->getStatusCode()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'test_status' => 'error',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Simple test method to verify backup functionality without database
     */
    public function testBackupLogic()
    {
        if (app()->environment() !== 'local') {
            abort(404);
        }

        $results = [];

        // Test 1: Validation Logic
        $results['validation_test'] = $this->testValidationLogic();

        // Test 2: File Name Cleaning
        $results['filename_test'] = $this->testFileNameCleaning();

        // Test 3: Default Name Generation
        $results['default_name_test'] = $this->testDefaultNameGeneration();

        // Test 4: Configuration Processing
        $results['config_test'] = $this->testConfigProcessing();

        return response()->json([
            'test_summary' => 'Backup Logic Tests Completed',
            'timestamp' => now()->toDateTimeString(),
            'environment' => app()->environment(),
            'results' => $results,
            'overall_status' => $this->determineOverallStatus($results)
        ]);
    }

    private function testValidationLogic(): array
    {
        $testCases = [
            'valid_with_name' => [
                'name' => 'Test Backup',
                'tables' => ['hb837', 'consultants']
            ],
            'valid_without_name' => [
                'tables' => ['hb837']
            ],
            'invalid_no_tables' => [
                'name' => 'Test',
                'tables' => []
            ],
            'invalid_empty_tables' => [
                'name' => 'Test'
            ]
        ];

        $results = [];
        foreach ($testCases as $case => $data) {
            $validator = Validator::make($data, [
                'name' => 'nullable|string|max:255',
                'tables' => 'required|array|min:1',
                'tables.*' => 'string',
            ]);

            $results[$case] = [
                'data' => $data,
                'passed' => !$validator->fails(),
                'errors' => $validator->errors()->toArray()
            ];
        }

        return $results;
    }

    private function testFileNameCleaning(): array
    {
        $testCases = [
            'Test Backup 123' => 'TestBackup123',
            'backup@#$%' => 'backup',
            'My-File_Name' => 'My-File_Name',
            '123 Test!@#' => '123Test',
            '' => ''
        ];

        $results = [];
        foreach ($testCases as $input => $expected) {
            $cleaned = $this->cleanFileName($input);
            $results[] = [
                'input' => $input,
                'expected' => $expected,
                'actual' => $cleaned,
                'passed' => $cleaned === $expected
            ];
        }

        return $results;
    }

    private function testDefaultNameGeneration(): array
    {
        $name1 = 'Test Backup';
        $name2 = '';
        $name3 = null;

        $defaultName = 'Backup_' . date('Y-m-d_H-i-s');

        return [
            'with_name' => [
                'input' => $name1,
                'output' => $name1 ?: $defaultName,
                'used_default' => false
            ],
            'empty_string' => [
                'input' => $name2,
                'output' => $name2 ?: $defaultName,
                'used_default' => true
            ],
            'null_value' => [
                'input' => $name3,
                'output' => $name3 ?: $defaultName,
                'used_default' => true
            ]
        ];
    }

    private function testConfigProcessing(): array
    {
        $testConfigs = [
            [
                'name' => 'Test Backup',
                'tables' => ['hb837', 'consultants']
            ],
            [
                'name' => '',
                'tables' => ['hb837']
            ]
        ];

        $results = [];
        foreach ($testConfigs as $index => $config) {
            $name = $config['name'] ?: 'Backup_' . date('Y-m-d_H-i-s');
            $processedConfig = [
                'name' => $this->cleanFileName($name),
                'tables' => $config['tables'],
            ];

            $results["config_$index"] = [
                'input' => $config,
                'processed' => $processedConfig,
                'name_generated' => empty($config['name']),
                'tables_count' => count($config['tables'])
            ];
        }

        return $results;
    }

    /**
     * Test import validation and logic without database
     */
    public function testImportLogic()
    {
        if (app()->environment() !== 'local') {
            abort(404);
        }

        $results = [];

        // Test 1: Import Validation Rules
        $results['import_validation'] = $this->testImportValidation();

        // Test 2: File Type Validation
        $results['file_type_validation'] = $this->testFileTypeValidation();

        // Test 3: Import Configuration Logic
        $results['import_config_test'] = $this->testImportConfiguration();

        return response()->json([
            'test_summary' => 'Import Logic Tests Completed',
            'timestamp' => now()->toDateTimeString(),
            'environment' => app()->environment(),
            'results' => $results,
            'overall_status' => $this->determineOverallStatus($results)
        ]);
    }

    private function testImportValidation(): array
    {
        $testCases = [
            'valid_csv' => [
                'csv_file' => [
                    'name' => 'test.csv',
                    'type' => 'text/csv',
                    'size' => 1024
                ]
            ],
            'valid_xlsx' => [
                'csv_file' => [
                    'name' => 'test.xlsx',
                    'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'size' => 2048
                ]
            ],
            'invalid_type' => [
                'csv_file' => [
                    'name' => 'test.txt',
                    'type' => 'text/plain',
                    'size' => 1024
                ]
            ],
            'too_large' => [
                'csv_file' => [
                    'name' => 'test.csv',
                    'type' => 'text/csv',
                    'size' => 11 * 1024 * 1024 // 11MB
                ]
            ],
            'missing_file' => []
        ];

        $results = [];
        foreach ($testCases as $case => $data) {
            // Simulate file validation rules
            $rules = [
                'csv_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
                'truncate' => 'sometimes|in:on'
            ];

            // Simulate validation (can't actually validate UploadedFile without real file)
            $hasFile = isset($data['csv_file']);
            $validMime = $hasFile && in_array($data['csv_file']['type'], [
                'text/csv',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ]);
            $validSize = $hasFile && $data['csv_file']['size'] <= (10240 * 1024);

            $results[$case] = [
                'data' => $data,
                'has_file' => $hasFile,
                'valid_mime' => $validMime,
                'valid_size' => $validSize,
                'passed' => $hasFile && $validMime && $validSize
            ];
        }

        return $results;
    }

    private function testFileTypeValidation(): array
    {
        $allowedExtensions = ['csv', 'xlsx', 'xls'];
        $allowedMimes = [
            'text/csv',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];

        $testFiles = [
            ['name' => 'data.csv', 'mime' => 'text/csv'],
            ['name' => 'data.xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['name' => 'data.xls', 'mime' => 'application/vnd.ms-excel'],
            ['name' => 'data.txt', 'mime' => 'text/plain'],
            ['name' => 'data.pdf', 'mime' => 'application/pdf'],
        ];

        $results = [];
        foreach ($testFiles as $file) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $validExtension = in_array($extension, $allowedExtensions);
            $validMime = in_array($file['mime'], $allowedMimes);

            $results[] = [
                'file' => $file,
                'extension' => $extension,
                'valid_extension' => $validExtension,
                'valid_mime' => $validMime,
                'passed' => $validExtension && $validMime
            ];
        }

        return $results;
    }

    private function testImportConfiguration(): array
    {
        $testConfigs = [
            ['truncate' => 'on', 'expected_truncate' => true],
            ['truncate' => 'off', 'expected_truncate' => false],
            ['truncate' => null, 'expected_truncate' => false],
            ['other_param' => 'value', 'expected_truncate' => false]
        ];

        $results = [];
        foreach ($testConfigs as $index => $config) {
            $shouldTruncate = isset($config['truncate']) && $config['truncate'] === 'on';
            $expected = $config['expected_truncate'];

            $results["config_$index"] = [
                'input' => $config,
                'should_truncate' => $shouldTruncate,
                'expected' => $expected,
                'passed' => $shouldTruncate === $expected
            ];
        }

        return $results;
    }

    private function determineOverallStatus(array $results): string
    {
        $hasFailures = false;

        foreach ($results as $testType => $testResults) {
            if (is_array($testResults)) {
                foreach ($testResults as $result) {
                    if (isset($result['passed']) && !$result['passed']) {
                        $hasFailures = true;
                        break 2;
                    }
                }
            }
        }

        return $hasFailures ? 'SOME_TESTS_FAILED' : 'ALL_TESTS_PASSED';
    }

    /**
     * Toggle the auto backup cron job setting
     */
    public function toggleCron(Request $request)
    {
        $enabled = $request->has('enabled') && $request->input('enabled') == '1';

        // Store the setting in cache
        cache()->put('backup_cron_enabled', $enabled, now()->addYears(1));

        // Log the change
        Log::info('Auto backup cron setting changed', [
            'enabled' => $enabled,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
            'timestamp' => now()->toDateTimeString()
        ]);

        // Create audit record
        ImportAudit::create([
            'import_id' => Str::uuid(),
            'type' => 'cron_toggle',
            'changes' => [
                'action' => $enabled ? 'enabled' : 'disabled',
                'previous_state' => !$enabled,
                'new_state' => $enabled,
                'message' => 'Auto backup ' . ($enabled ? 'enabled' : 'disabled'),
            ],
            'user_id' => auth()->id(),
        ]);

        $message = $enabled
            ? 'Auto backup has been enabled. Backups will run daily at ' . config('backup.cron_time_at', '23:00')
            : 'Auto backup has been disabled.';

        return redirect()->back()->with('success', $message);
    }
}

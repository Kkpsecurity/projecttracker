<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\Consultant;
use App\Models\User;
use App\Models\Backup;
use App\Models\ImportAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // For now, just require authentication. Add role-based access later if needed.
    }

    /**
     * Display the main analytics dashboard
     */
    public function index()
    {
        // Initialize all variables with defaults first
        $overview = [
            'total_projects' => 0,
            'active_projects' => 0,
            'completed_projects' => 0,
            'total_consultants' => 0,
            'total_clients' => 0,
            'monthly_completions' => 0,
            'completion_rate' => 0,
            'avg_processing_time' => 0,
        ];
        
        $projectAnalytics = [
            'status_distribution' => [],
            'monthly_trends' => [],
            'property_type_breakdown' => [],
            'geographic_distribution' => [],
            'backlog_analysis' => ['overdue' => 0, 'due_this_week' => 0, 'due_this_month' => 0],
        ];
        
        $teamMetrics = [
            'consultant_workload' => [],
            'consultant_performance' => [],
            'team_efficiency' => ['completion_rate' => 0, 'avg_processing_time' => 0, 'productivity_score' => 0],
            'capacity_utilization' => ['current_utilization' => 0, 'active_consultants' => 0, 'active_projects' => 0, 'total_capacity' => 0],
        ];
        
        $systemHealth = [
            'database_stats' => ['total_records' => [], 'growth_metrics' => [], 'storage_usage' => []],
            'import_export_activity' => ['recent_activity' => [], 'monthly_stats' => []],
            'backup_compliance' => ['last_backup' => 'Never', 'monthly_backups' => 0, 'backup_health' => 0, 'storage_used' => 0],
            'user_activity' => ['total_users' => 0, 'active_users' => 0, 'new_users_this_month' => 0, 'user_roles' => []],
        ];

        try {
            // Get overview metrics
            Log::info('Getting overview metrics...');
            $overview = $this->getOverviewMetrics();
            
            // Test each method individually
            Log::info('Getting project analytics...');
            $projectAnalytics = $this->getProjectAnalytics();
            
            Log::info('Getting team metrics...');
            $teamMetrics = $this->getTeamMetrics();
            
            Log::info('Getting system health...');
            $systemHealth = $this->getSystemHealth();

            Log::info('Analytics data loaded successfully');

        } catch (\Exception $e) {
            Log::error('Analytics dashboard error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Keep default values that were already set
            $error = 'Some analytics data could not be loaded: ' . $e->getMessage();
            
            return view('admin.analytics.index', compact(
                'overview',
                'projectAnalytics', 
                'teamMetrics',
                'systemHealth'
            ))->with('error', $error);
        }

        return view('admin.analytics.index', compact(
            'overview',
            'projectAnalytics', 
            'teamMetrics',
            'systemHealth'
        ));
    }

    /**
     * Get overview metrics for the dashboard
     */
    private function getOverviewMetrics()
    {
        try {
            return [
                'total_projects' => HB837::count(),
                'active_projects' => HB837::active()->count(), // Use model scope
                'completed_projects' => HB837::completed()->count(), // Use model scope
                'total_consultants' => Consultant::count(),
                'total_clients' => 0, // No clients table exists, set to 0
                'monthly_completions' => $this->getMonthlyCompletions(),
                'completion_rate' => $this->getCompletionRate(),
                'avg_processing_time' => $this->getAverageProcessingTime(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getOverviewMetrics: ' . $e->getMessage());
            return [
                'total_projects' => 0,
                'active_projects' => 0,
                'completed_projects' => 0,
                'total_consultants' => 0,
                'total_clients' => 0,
                'monthly_completions' => 0,
                'completion_rate' => 0,
                'avg_processing_time' => 0,
            ];
        }
    }

    /**
     * Get project analytics data
     */
    private function getProjectAnalytics()
    {
        return [
            'status_distribution' => $this->getProjectStatusDistribution(),
            'monthly_trends' => $this->getMonthlyProjectTrends(),
            'property_type_breakdown' => $this->getPropertyTypeBreakdown(),
            'geographic_distribution' => $this->getGeographicDistribution(),
            'backlog_analysis' => $this->getBacklogAnalysis(),
        ];
    }

    /**
     * Get team productivity metrics
     */
    private function getTeamMetrics()
    {
        return [
            'consultant_workload' => $this->getConsultantWorkload(),
            'consultant_performance' => $this->getConsultantPerformance(),
            'team_efficiency' => $this->getTeamEfficiency(),
            'capacity_utilization' => $this->getCapacityUtilization(),
        ];
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealth()
    {
        return [
            'database_stats' => $this->getDatabaseStats(),
            'import_export_activity' => $this->getImportExportActivity(),
            'backup_compliance' => $this->getBackupCompliance(),
            'user_activity' => $this->getUserActivity(),
        ];
    }

    /**
     * Get project status distribution
     */
    private function getProjectStatusDistribution()
    {
        return HB837::select('report_status', DB::raw('count(*) as count'))
            ->groupBy('report_status')
            ->pluck('count', 'report_status')
            ->toArray();
    }

    /**
     * Get monthly project trends
     */
    private function getMonthlyProjectTrends()
    {
        $months = collect();
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) {
            $month = $startDate->copy()->addMonths($i);
            $created = HB837::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $completed = HB837::where('report_status', 'completed')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->count();
                
            $months->push([
                'month' => $month->format('M Y'),
                'created' => $created,
                'completed' => $completed,
            ]);
        }
        
        return $months;
    }

    /**
     * Get property type breakdown
     */
    private function getPropertyTypeBreakdown()
    {
        return HB837::select('property_type', DB::raw('count(*) as count'))
            ->whereNotNull('property_type')
            ->groupBy('property_type')
            ->pluck('count', 'property_type')
            ->toArray();
    }

    /**
     * Get geographic distribution (placeholder)
     */
    private function getGeographicDistribution()
    {
        // This would need plot addresses joined
        return HB837::select('city', DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->limit(10)
            ->pluck('count', 'city')
            ->toArray();
    }

    /**
     * Get backlog analysis
     */
    private function getBacklogAnalysis()
    {
        $now = Carbon::now();
        
        return [
            'overdue' => HB837::where('scheduled_date_of_inspection', '<', $now)
                ->whereNotIn('report_status', ['completed'])
                ->count(),
            'due_this_week' => HB837::whereBetween('scheduled_date_of_inspection', [$now, $now->copy()->addWeek()])
                ->whereNotIn('report_status', ['completed'])
                ->count(),
            'due_this_month' => HB837::whereBetween('scheduled_date_of_inspection', [$now, $now->copy()->addMonth()])
                ->whereNotIn('report_status', ['completed'])
                ->count(),
        ];
    }

    /**
     * Get consultant workload distribution
     */
    private function getConsultantWorkload()
    {
        try {
            return Consultant::select(
                    DB::raw('CONCAT(consultants.first_name, \' \', consultants.last_name) as name'), 
                    DB::raw('count(hb837.id) as project_count')
                )
                ->leftJoin('hb837', 'consultants.id', '=', 'hb837.assigned_consultant_id')
                ->whereNotIn('hb837.report_status', ['completed'])
                ->groupBy('consultants.id', 'consultants.first_name', 'consultants.last_name')
                ->pluck('project_count', 'name')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error in getConsultantWorkload: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get consultant performance metrics
     */
    private function getConsultantPerformance()
    {
        try {
            return Consultant::select(
                    DB::raw('CONCAT(consultants.first_name, \' \', consultants.last_name) as name'),
                    DB::raw('count(hb837.id) as total_projects'),
                    DB::raw('count(case when hb837.report_status = \'completed\' then 1 end) as completed_projects'),
                    DB::raw('avg(case when hb837.report_status = \'completed\' then extract(epoch from (hb837.updated_at - hb837.created_at))/86400 end) as avg_completion_days')
                )
                ->leftJoin('hb837', 'consultants.id', '=', 'hb837.assigned_consultant_id')
                ->groupBy('consultants.id', 'consultants.first_name', 'consultants.last_name')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error in getConsultantPerformance: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get team efficiency metrics
     */
    private function getTeamEfficiency()
    {
        $totalProjects = HB837::count();
        $completedProjects = HB837::completed()->count();
        $avgProcessingTime = $this->getAverageProcessingTime();
        
        return [
            'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0,
            'avg_processing_time' => $avgProcessingTime,
            'productivity_score' => $this->calculateProductivityScore(),
        ];
    }

    /**
     * Get capacity utilization
     */
    private function getCapacityUtilization()
    {
        try {
            $activeConsultants = Consultant::count();
            $activeProjects = HB837::active()->count(); // Use model scope
            
            // Assuming each consultant can handle 10 projects optimally
            $totalCapacity = $activeConsultants * 10;
            $utilization = $totalCapacity > 0 ? round(($activeProjects / $totalCapacity) * 100, 1) : 0;
            
            return [
                'current_utilization' => $utilization,
                'active_consultants' => $activeConsultants,
                'active_projects' => $activeProjects,
                'total_capacity' => $totalCapacity,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getCapacityUtilization: ' . $e->getMessage());
            return [
                'current_utilization' => 0,
                'active_consultants' => 0,
                'active_projects' => 0,
                'total_capacity' => 0,
            ];
        }
    }

    /**
     * Get database statistics
     */
    private function getDatabaseStats()
    {
        return [
            'total_records' => [
                'hb837' => HB837::count(),
                'consultants' => Consultant::count(),
                'clients' => 0, // No clients table exists
                'users' => User::count(),
            ],
            'growth_metrics' => $this->getDataGrowthMetrics(),
            'storage_usage' => $this->getStorageUsage(),
        ];
    }

    /**
     * Get import/export activity
     */
    private function getImportExportActivity()
    {
        $recentActivity = ImportAudit::orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['type', 'changes', 'created_at'])
            ->toArray();
            
        $monthlyStats = ImportAudit::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total_operations,
                SUM(CASE WHEN type = "import" THEN 1 ELSE 0 END) as imports,
                SUM(CASE WHEN type = "backup" THEN 1 ELSE 0 END) as backups
            ')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
            
        return [
            'recent_activity' => $recentActivity,
            'monthly_stats' => $monthlyStats,
        ];
    }

    /**
     * Get backup compliance metrics
     */
    private function getBackupCompliance()
    {
        try {
            // Check if backups table exists by attempting a simple query
            $lastBackup = Backup::latest()->first();
            $backupCount = Backup::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            
            return [
                'last_backup' => $lastBackup ? $lastBackup->created_at->diffForHumans() : 'Never',
                'monthly_backups' => $backupCount,
                'backup_health' => $this->calculateBackupHealth(),
                'storage_used' => Backup::sum('size') ?: 0,
            ];
        } catch (\Exception $e) {
            // Backups table doesn't exist or other database error
            Log::warning('Backup table not available: ' . $e->getMessage());
            return [
                'last_backup' => 'No backup system configured',
                'monthly_backups' => 0,
                'backup_health' => 0,
                'storage_used' => 0,
            ];
        }
    }

    /**
     * Get user activity metrics
     */
    private function getUserActivity()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'new_users_this_month' => User::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'user_roles' => $this->getUserRoleDistribution(),
        ];
    }

    // Helper methods
    private function getMonthlyCompletions()
    {
        return HB837::completed()
            ->whereMonth('updated_at', Carbon::now()->month)
            ->count();
    }

    private function getCompletionRate()
    {
        $total = HB837::count();
        $completed = HB837::completed()->count();
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    private function getAverageProcessingTime()
    {
        try {
            // Use PostgreSQL syntax instead of MySQL DATEDIFF
            $avgDays = HB837::completed()
                ->whereNotNull('created_at')
                ->whereNotNull('updated_at')
                ->selectRaw('avg(extract(epoch from (updated_at - created_at))/86400) as avg_days')
                ->value('avg_days');
                
            return $avgDays ? round($avgDays, 1) : 0;
        } catch (\Exception $e) {
            Log::error('Error calculating average processing time: ' . $e->getMessage());
            return 0;
        }
    }

    private function calculateProductivityScore()
    {
        // Simple productivity calculation based on completion rate and processing time
        $completionRate = $this->getCompletionRate();
        $processingTime = $this->getAverageProcessingTime();
        
        // Lower processing time is better, so invert it for scoring
        $timeScore = $processingTime > 0 ? (30 / $processingTime) * 50 : 50;
        
        return round(($completionRate + $timeScore) / 2, 1);
    }

    private function getDataGrowthMetrics()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $currentCount = HB837::where('created_at', '>=', $currentMonth)->count();
        $lastMonthCount = HB837::whereBetween('created_at', [$lastMonth, $currentMonth])->count();
        
        $growth = $lastMonthCount > 0 ? 
            round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1) : 0;
            
        return [
            'monthly_growth' => $growth,
            'current_month' => $currentCount,
            'last_month' => $lastMonthCount,
        ];
    }

    private function getStorageUsage()
    {
        try {
            return [
                'total_backup_size' => Backup::sum('size') ?: 0,
                'average_backup_size' => Backup::avg('size') ?: 0,
                'largest_backup' => Backup::max('size') ?: 0,
            ];
        } catch (\Exception $e) {
            // Backup table doesn't exist
            Log::warning('Backup table not available for storage usage: ' . $e->getMessage());
            return [
                'total_backup_size' => 0,
                'average_backup_size' => 0,
                'largest_backup' => 0,
            ];
        }
    }

    private function calculateBackupHealth()
    {
        try {
            $lastBackup = Backup::latest()->first();
            if (!$lastBackup) return 0;
            
            $daysSinceLastBackup = $lastBackup->created_at->diffInDays(Carbon::now());
            
            if ($daysSinceLastBackup <= 1) return 100;
            if ($daysSinceLastBackup <= 3) return 80;
            if ($daysSinceLastBackup <= 7) return 60;
            if ($daysSinceLastBackup <= 14) return 40;
            return 20;
        } catch (\Exception $e) {
            // Backup table doesn't exist
            return 0;
        }
    }

    private function getUserRoleDistribution()
    {
        // Placeholder - would need proper role system
        return [
            'admin' => 1,
            'consultant' => Consultant::count(),
            'user' => User::count() - 1,
        ];
    }

    /**
     * AJAX endpoints for dynamic chart data
     */
    
    public function getProjectTrendsData(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        switch ($period) {
            case 'weekly':
                return response()->json($this->getWeeklyTrends());
            case 'quarterly':
                return response()->json($this->getQuarterlyTrends());
            default:
                return response()->json($this->getMonthlyProjectTrends());
        }
    }

    public function getConsultantMetrics(Request $request)
    {
        $consultantId = $request->get('consultant_id');
        
        if ($consultantId) {
            return response()->json($this->getIndividualConsultantMetrics($consultantId));
        }
        
        return response()->json([
            'workload' => $this->getConsultantWorkload(),
            'performance' => $this->getConsultantPerformance(),
        ]);
    }

    /**
     * Get real-time statistics for dashboard updates
     */
    public function realtimeStats()
    {
        try {
            $stats = [
                'active_projects' => HB837::active()->count(),
                'pending_reviews' => HB837::where('report_status', 'pending_review')->count(),
                'completed_today' => HB837::whereDate('updated_at', today())->where('report_status', 'completed')->count(),
                'system_health' => $this->calculateSystemHealth(),
                'total_projects' => HB837::count(),
                'completed_projects' => HB837::completed()->count(),
                'completion_rate' => $this->getCompletionRate(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error getting real-time stats: ' . $e->getMessage());
            return response()->json([
                'active_projects' => 0,
                'pending_reviews' => 0,
                'completed_today' => 0,
                'system_health' => 0,
                'total_projects' => 0,
                'completed_projects' => 0,
                'completion_rate' => 0,
            ], 500);
        }
    }

    /**
     * Calculate overall system health score
     */
    private function calculateSystemHealth()
    {
        try {
            $score = 100;
            
            // Deduct points for various issues
            try {
                $recentBackup = Backup::latest()->first();
                if (!$recentBackup || $recentBackup->created_at->diffInDays() > 7) {
                    $score -= 20; // No recent backup
                }
            } catch (\Exception $e) {
                // Backup table doesn't exist, don't penalize for this
                Log::info('Backup table not available for system health calculation');
            }
            
            try {
                $recentImportErrors = ImportAudit::where('status', 'failed')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();
                if ($recentImportErrors > 0) {
                    $score -= min(30, $recentImportErrors * 5); // Deduct for import errors
                }
            } catch (\Exception $e) {
                // ImportAudit table might not exist
                Log::info('ImportAudit table not available for system health calculation');
            }
            
            return max(0, $score);
        } catch (\Exception $e) {
            Log::error('Error calculating system health: ' . $e->getMessage());
            return 50; // Default moderate health
        }
    }

    /**
     * Export analytics data to CSV
     */
    public function exportAnalytics(Request $request)
    {
        try {
            $type = $request->get('type', 'overview');
            $filename = 'analytics_' . $type . '_' . date('Y-m-d') . '.csv';

            switch ($type) {
                case 'projects':
                    return $this->exportProjectAnalytics($filename);
                case 'team':
                    return $this->exportTeamAnalytics($filename);
                case 'system':
                    return $this->exportSystemAnalytics($filename);
                default:
                    return $this->exportOverviewAnalytics($filename);
            }
        } catch (\Exception $e) {
            Log::error('Error exporting analytics: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export analytics data.');
        }
    }

    /**
     * Export project analytics to CSV
     */
    private function exportProjectAnalytics($filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Property Address', 'Report Status', 'Property Type', 'Scheduled Date', 'Consultant', 'Client']);
            
            // Data rows
            HB837::with(['consultant', 'client'])->chunk(1000, function($projects) use ($file) {
                foreach ($projects as $project) {
                    fputcsv($file, [
                        $project->property_address,
                        $project->report_status,
                        $project->property_type,
                        $project->scheduled_date_of_inspection,
                        $project->consultant ? $project->consultant->name : 'N/A',
                        $project->client ? $project->client->name : 'N/A',
                    ]);
                }
            });
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export team analytics to CSV
     */
    private function exportTeamAnalytics($filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Consultant Name', 'Active Projects', 'Completed Projects', 'Total Projects', 'Completion Rate']);
            
            // Data rows
            $consultantMetrics = $this->getConsultantWorkload();
            foreach ($consultantMetrics as $metric) {
                $totalProjects = ($metric['active_projects'] ?? 0) + ($metric['completed_projects'] ?? 0);
                $completionRate = $totalProjects > 0 ? round(($metric['completed_projects'] ?? 0) / $totalProjects * 100, 1) : 0;
                
                fputcsv($file, [
                    $metric['consultant_name'] ?? 'Unknown',
                    $metric['active_projects'] ?? 0,
                    $metric['completed_projects'] ?? 0,
                    $totalProjects,
                    $completionRate . '%'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export overview analytics to CSV
     */
    private function exportOverviewAnalytics($filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $overview = $this->getOverviewMetrics();

        $callback = function() use ($overview) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Metric', 'Value']);
            
            // Data rows
            foreach ($overview as $key => $value) {
                fputcsv($file, [
                    ucwords(str_replace('_', ' ', $key)),
                    $value
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export analytics data to various formats
     */
    public function exportData(Request $request)
    {
        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'overview');
        
        try {
            switch ($type) {
                case 'overview':
                    $data = $this->getOverviewMetrics();
                    $filename = 'analytics_overview_' . date('Y_m_d');
                    break;
                case 'projects':
                    $data = $this->getProjectAnalytics();
                    $filename = 'project_analytics_' . date('Y_m_d');
                    break;
                case 'team':
                    $data = $this->getTeamMetrics();
                    $filename = 'team_metrics_' . date('Y_m_d');
                    break;
                case 'system':
                    $data = $this->getSystemHealth();
                    $filename = 'system_health_' . date('Y_m_d');
                    break;
                default:
                    return response()->json(['error' => 'Invalid export type'], 400);
            }

            if ($format === 'csv') {
                return $this->exportToCsv($data, $filename);
            } elseif ($format === 'pdf') {
                return $this->exportToPdf($data, $filename);
            } elseif ($format === 'excel') {
                return $this->exportToExcel($data, $filename);
            } else {
                return response()->json($data);
            }
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    /**
     * Export data to CSV format
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Flatten the array for CSV export
            $flatData = $this->flattenArray($data);
            
            // Write headers
            if (!empty($flatData)) {
                fputcsv($file, array_keys($flatData[0]));
                
                // Write data
                foreach ($flatData as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Flatten multi-dimensional array for CSV export
     */
    private function flattenArray($array, $prefix = '')
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;
            
            if (is_array($value)) {
                if (isset($value[0]) && is_array($value[0])) {
                    // It's an array of arrays (like status_distribution)
                    foreach ($value as $index => $subArray) {
                        $subResult = $this->flattenArray($subArray, $newKey . '.' . $index);
                        $result = array_merge($result, $subResult);
                    }
                } else {
                    // It's a single-level array
                    $result = array_merge($result, $this->flattenArray($value, $newKey));
                }
            } else {
                $result[] = [$newKey => $value];
            }
        }
        
        return $result;
    }

    /**
     * Get filtered analytics data based on request parameters
     */
    public function getFilteredData(Request $request)
    {
        try {
            $dateRange = $request->get('date_range', 'last_30_days');
            $consultant = $request->get('consultant');
            $status = $request->get('status');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            // Build query based on filters
            $query = HB837::query();
            
            // Apply date filter
            if ($dateRange === 'custom' && $startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                switch ($dateRange) {
                    case 'last_30_days':
                        $query->where('created_at', '>=', now()->subDays(30));
                        break;
                    case 'last_90_days':
                        $query->where('created_at', '>=', now()->subDays(90));
                        break;
                    case 'last_6_months':
                        $query->where('created_at', '>=', now()->subMonths(6));
                        break;
                    case 'last_year':
                        $query->where('created_at', '>=', now()->subYear());
                        break;
                }
            }
            
            // Apply consultant filter
            if ($consultant) {
                $query->where('consultant_id', $consultant);
            }
            
            // Apply status filter
            if ($status) {
                $query->where('report_status', $status);
            }
            
            // Get filtered data
            $filteredProjects = $query->get();
            
            // Calculate filtered analytics
            $analytics = [
                'total_projects' => $filteredProjects->count(),
                'status_distribution' => $this->calculateStatusDistribution($filteredProjects),
                'monthly_trends' => $this->calculateMonthlyTrends($filteredProjects),
                'property_breakdown' => $this->calculatePropertyBreakdown($filteredProjects),
            ];
            
            return response()->json($analytics);
            
        } catch (\Exception $e) {
            Log::error('Error getting filtered analytics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get filtered data'], 500);
        }
    }
    
    /**
     * Get performance benchmarks and comparison data
     */
    public function getPerformanceBenchmarks()
    {
        try {
            $currentMonth = now()->format('Y-m');
            $lastMonth = now()->subMonth()->format('Y-m');
            
            $currentMonthData = HB837::whereRaw("to_char(created_at, 'YYYY-MM') = ?", [$currentMonth]);
            $lastMonthData = HB837::whereRaw("to_char(created_at, 'YYYY-MM') = ?", [$lastMonth]);
            
            $benchmarks = [
                'current_month' => [
                    'total_projects' => $currentMonthData->count(),
                    'completed_projects' => $currentMonthData->where('report_status', 'completed')->count(),
                    'avg_processing_time' => $this->calculateAvgProcessingTime($currentMonthData->get()),
                ],
                'last_month' => [
                    'total_projects' => $lastMonthData->count(),
                    'completed_projects' => $lastMonthData->where('report_status', 'completed')->count(),
                    'avg_processing_time' => $this->calculateAvgProcessingTime($lastMonthData->get()),
                ],
                'trends' => [
                    'project_growth' => $this->calculateGrowthRate($lastMonthData->count(), $currentMonthData->count()),
                    'completion_growth' => $this->calculateGrowthRate(
                        $lastMonthData->where('report_status', 'completed')->count(),
                        $currentMonthData->where('report_status', 'completed')->count()
                    ),
                ],
                'industry_benchmarks' => [
                    'avg_completion_rate' => 75, // Industry average
                    'avg_processing_time' => 30, // Industry average in days
                    'quality_score' => 85, // Industry average
                ]
            ];
            
            return response()->json($benchmarks);
            
        } catch (\Exception $e) {
            Log::error('Error getting performance benchmarks: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get benchmarks'], 500);
        }
    }
    
    /**
     * Calculate growth rate between two values
     */
    private function calculateGrowthRate($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }
    
    /**
     * Calculate average processing time for a collection of projects
     */
    private function calculateAvgProcessingTime($projects)
    {
        if ($projects->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        $processedCount = 0;
        
        foreach ($projects as $project) {
            if ($project->scheduled_date_of_inspection && $project->updated_at) {
                $days = \Carbon\Carbon::parse($project->scheduled_date_of_inspection)
                    ->diffInDays($project->updated_at);
                $totalDays += $days;
                $processedCount++;
            }
        }
        
        return $processedCount > 0 ? round($totalDays / $processedCount, 1) : 0;
    }

    // ...existing code...
}

<?php

namespace App\Services\HB837;

use App\Models\HB837;
use Illuminate\Support\Facades\Cache;

class HB837StatisticsService
{
    /**
     * Calculate comprehensive statistics for dashboard
     */
    public function getStatistics(): array
    {
        return Cache::remember('hb837_statistics', 60, function () {
            return [
                'basic' => $this->getBasicStats(),
                'overdue' => $this->getOverdueStats(),
                'warnings' => $this->getWarningMetrics(),
                'business' => $this->getBusinessMetrics(),
                'tabCounts' => $this->getTabCounts(),
                'statusCounts' => $this->getStatusCounts(),
                'contractingCounts' => $this->getContractingCounts()
            ];
        });
    }

    /**
     * Get basic statistics
     */
    private function getBasicStats(): array
    {
        return [
            'active' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
            'total' => HB837::count()
        ];
    }

    /**
     * Get overdue statistics (Task 18 Enhancement)
     */
    private function getOverdueStats(): array
    {
        $overdue = HB837::whereNotNull('scheduled_date_of_inspection')
            ->where('scheduled_date_of_inspection', '<', now())
            ->where('report_status', '!=', 'completed')
            ->count();

        $thirtyDaysAgo = now()->subDays(30);
        $thirtyDayOverdue = HB837::where('created_at', '<', $thirtyDaysAgo)
            ->whereNotIn('report_status', ['completed'])
            ->count();

        return [
            'overdue' => $overdue,
            'thirty_day_overdue' => $thirtyDayOverdue
        ];
    }

    /**
     * Get warning metrics
     */
    public function getWarningMetrics(): array
    {
        return Cache::remember('hb837_warnings', 60, function () {
            return [
                'unassigned_projects' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->where('contracting_status', 'executed')
                    ->whereNull('assigned_consultant_id')
                    ->count(),
                'unscheduled_projects' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->where('contracting_status', 'executed')
                    ->whereNull('scheduled_date_of_inspection')
                    ->count(),
                'late_reports' => HB837::where('report_status', 'not-started')
                    ->where('contracting_status', 'executed')
                    ->whereNotNull('scheduled_date_of_inspection')
                    ->where('scheduled_date_of_inspection', '<', now()->subDays(30))
                    ->count()
            ];
        });
    }

    /**
     * Get business metrics
     */
    public function getBusinessMetrics(): array
    {
        $executedProjects = HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
            ->where('contracting_status', 'executed');

        // Calculate net profit with estimation for missing data
        $actualNetProfit = $executedProjects->sum('project_net_profit') ?? 0;
        $billingWithoutProfit = HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
            ->where('contracting_status', 'executed')
            ->whereNull('project_net_profit')
            ->sum('quoted_price') ?? 0;
        $estimatedNetProfit = $billingWithoutProfit * 0.75; // 75% profit margin estimate
        $totalEstimatedNetProfit = $actualNetProfit + $estimatedNetProfit;

        return [
            'active_projects' => $executedProjects->count(),
            'gross_billing_in_process' => $executedProjects->sum('quoted_price') ?? 0,
            'net_profit_in_process' => $totalEstimatedNetProfit,
            'actual_net_profit' => $actualNetProfit,
            'estimated_net_profit' => $estimatedNetProfit
        ];
    }

    /**
     * Get tab counts for navigation
     */
    public function getTabCounts(): array
    {
        $basic = $this->getBasicStats();
        
        return [
            'all' => $basic['total'],
            'active' => $basic['active'],
            'quoted' => $basic['quoted'],
            'completed' => $basic['completed'],
            'closed' => $basic['closed']
        ];
    }

    /**
     * Get detailed status breakdown
     */
    private function getStatusCounts(): array
    {
        return [
            'not_started' => HB837::where('report_status', 'not-started')->count(),
            'in_progress' => HB837::where('report_status', 'underway')->count(),
            'in_review' => HB837::where('report_status', 'in-review')->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
        ];
    }

    /**
     * Get contracting status breakdown
     */
    private function getContractingCounts(): array
    {
        return [
            'quoted' => HB837::where('contracting_status', 'quoted')->count(),
            'started' => HB837::where('contracting_status', 'started')->count(),
            'executed' => HB837::where('contracting_status', 'executed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
        ];
    }

    /**
     * Refresh statistics cache
     */
    public function refreshCache(): void
    {
        Cache::forget('hb837_statistics');
        Cache::forget('hb837_warnings');
    }

    /**
     * Get general statistics for dashboard (alias for basic stats)
     */
    public function getGeneralStatistics(): array
    {
        return $this->getBasicStats();
    }

    /**
     * Get consultant performance statistics
     */
    public function getConsultantStats(): array
    {
        return Cache::remember('hb837_consultant_stats', 120, function () {
            return \App\Models\Consultant::where('is_active', true)
                ->withCount([
                    'hb837Records as active_count' => function ($query) {
                        $query->whereIn('report_status', ['not-started', 'underway', 'in-review'])
                              ->where('contracting_status', 'executed');
                    },
                    'hb837Records as completed_count' => function ($query) {
                        $query->where('report_status', 'completed');
                    }
                ])
                ->with([
                    'hb837Records' => function ($query) {
                        $query->select('assigned_consultant_id', 'project_net_profit')
                              ->where('report_status', 'completed');
                    }
                ])
                ->get()
                ->map(function ($consultant) {
                    return [
                        'id' => $consultant->id,
                        'name' => $consultant->name,
                        'active_projects' => $consultant->active_count,
                        'completed_projects' => $consultant->completed_count,
                        'total_profit' => $consultant->hb837Records->sum('project_net_profit') ?? 0
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get monthly statistics for a given year
     */
    public function getMonthlyStats(int $year): array
    {
        return Cache::remember("hb837_monthly_stats_{$year}", 180, function () use ($year) {
            $monthlyData = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                
                $monthlyData[] = [
                    'month' => $startDate->format('M'),
                    'created' => HB837::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'completed' => HB837::whereBetween('report_submitted', [$startDate, $endDate])->count(),
                    'revenue' => HB837::whereBetween('report_submitted', [$startDate, $endDate])
                                     ->sum('quoted_price') ?? 0
                ];
            }
            
            return $monthlyData;
        });
    }
}

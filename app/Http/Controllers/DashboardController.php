<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\HB837;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        // Get dashboard statistics
        $stats = [
            'total_consultants' => Consultant::count(),
            'active_consultants' => Consultant::active()->count(),
            'expiring_soon' => Consultant::expiringSoon()->count(),
            'total_projects' => HB837::count(),
            'active_projects' => HB837::active()->count(),
            'completed_projects' => HB837::completed()->count(),
            'in_progress_projects' => HB837::inProgress()->count(),
        ];

        // Get recent projects
        $recent_projects = HB837::with(['consultant'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get projects by status for chart
        $project_status_data = HB837::selectRaw('report_status, COUNT(*) as count')
            ->groupBy('report_status')
            ->pluck('count', 'report_status')
            ->toArray();

        // Get consultants with expiring certifications
        $expiring_consultants = Consultant::expiringSoon()
            ->orderBy('fcp_expiration_date', 'asc')
            ->limit(10)
            ->get();

        // Get projects by contracting status
        $contracting_data = HB837::selectRaw('contracting_status, COUNT(*) as count')
            ->groupBy('contracting_status')
            ->pluck('count', 'contracting_status')
            ->toArray();

        return view('dashboard.security', compact(
            'stats',
            'recent_projects',
            'project_status_data',
            'expiring_consultants',
            'contracting_data'
        ));
    }

    public function analytics(): View
    {
        // Monthly project creation trend
        $monthly_projects = HB837::selectRaw('EXTRACT(MONTH FROM created_at) as month, EXTRACT(YEAR FROM created_at) as year, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Revenue analysis
        $revenue_data = HB837::selectRaw('SUM(contract_value) as total_value, status')
            ->groupBy('status')
            ->pluck('total_value', 'status')
            ->toArray();

        // Consultant performance
        $consultant_performance = Consultant::withCount([
            'hb837Projects',
            'hb837Projects as completed_projects_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ])->orderBy('hb837_projects_count', 'desc')
        ->limit(10)
        ->get();

        return view('dashboard.analytics', compact(
            'monthly_projects',
            'revenue_data',
            'consultant_performance'
        ));
    }
}

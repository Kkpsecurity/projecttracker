<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\Client;
use App\Models\Consultant;
use App\Models\Backup;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Gather dashboard statistics
        $stats = [
            'total_projects' => HB837::count(),
            'active_projects' => HB837::where('report_status', 'Active')->count(),
            'completed_projects' => HB837::where('report_status', 'Completed')->count(),
            'total_consultants' => Consultant::count(),
            'recent_backups' => Backup::latest()->take(5)->get(),
            'recent_projects' => HB837::latest()->take(10)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

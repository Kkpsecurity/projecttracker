<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the activity logs index page
     */
    public function index()
    {
        // Get recent activity data for the dashboard
        $stats = $this->getActivityStats();
        $recentLogs = $this->getRecentLogs(10);

        return view('admin.logs.index', compact('stats', 'recentLogs'));
    }

    /**
     * Get activity statistics for the dashboard
     */
    private function getActivityStats()
    {
        // For now, we'll create mock data until we implement proper logging
        return [
            'total_actions_today' => $this->getMockTodayActions(),
            'total_logins_today' => $this->getMockTodayLogins(),
            'active_users' => $this->getActiveUsersCount(),
            'total_users' => $this->getTotalUsersCount(),
            'recent_signups' => $this->getRecentSignupsCount(),
            'critical_events' => 0, // Will be implemented with proper logging
        ];
    }

    /**
     * Get recent activity logs
     */
    private function getRecentLogs($limit = 50)
    {
        // Mock data structure for activity logs
        // In future implementation, this will query an activity_logs table
        return collect([
            [
                'id' => 1,
                'user_name' => Auth::user()->name,
                'user_email' => Auth::user()->email,
                'action' => 'User Login',
                'description' => 'User logged into the system',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'severity' => 'info',
                'category' => 'authentication'
            ],
            [
                'id' => 2,
                'user_name' => Auth::user()->name,
                'user_email' => Auth::user()->email,
                'action' => 'Viewed Admin Center',
                'description' => 'Accessed Activity Logs page',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()->subMinutes(5),
                'severity' => 'info',
                'category' => 'navigation'
            ]
        ])->take($limit);
    }

    /**
     * Get mock data for today's actions
     */
    private function getMockTodayActions()
    {
        // In real implementation, this would query activity logs for today
        return rand(15, 45);
    }

    /**
     * Get mock data for today's logins
     */
    private function getMockTodayLogins()
    {
        // In real implementation, this would query login logs for today
        return rand(3, 12);
    }

    /**
     * Get count of active users
     */
    private function getActiveUsersCount()
    {
        // Count users who have logged in within the last 30 days
        // For now, return total user count as we don't have last_login tracking yet
        return DB::table('users')->where('email_verified_at', '!=', null)->count();
    }

    /**
     * Get total users count
     */
    private function getTotalUsersCount()
    {
        return DB::table('users')->count();
    }

    /**
     * Get recent signups count (last 7 days)
     */
    private function getRecentSignupsCount()
    {
        return DB::table('users')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
    }

    /**
     * Get activity logs data for DataTables (future implementation)
     */
    public function getData(Request $request)
    {
        // This method will be implemented when we create the activity_logs table
        // For now, return the mock data in DataTables format

        $logs = $this->getRecentLogs(100);

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log['id'],
                    'user' => $log['user_name'] . ' (' . $log['user_email'] . ')',
                    'action' => $log['action'],
                    'description' => $log['description'],
                    'ip_address' => $log['ip_address'],
                    'created_at' => Carbon::parse($log['created_at'])->format('M j, Y g:i A'),
                    'severity' => $log['severity'],
                    'category' => $log['category']
                ];
            })
        ]);
    }

    /**
     * Clear old logs (admin function)
     */
    public function clearOldLogs(Request $request)
    {
        $days = $request->input('days', 30);

        // This will be implemented when we have the activity_logs table
        // DB::table('activity_logs')->where('created_at', '<', Carbon::now()->subDays($days))->delete();

        return response()->json([
            'success' => true,
            'message' => "Logs older than {$days} days have been cleared."
        ]);
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        // Implementation for exporting logs to CSV/Excel
        // Will be implemented in future iterations

        return response()->json([
            'success' => false,
            'message' => 'Export functionality will be implemented in a future update.'
        ]);
    }
}

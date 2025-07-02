<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentActivity = $this->getRecentActivity();
        $systemInfo = $this->getSystemInfo();

        return view('admin.dashboard', compact('stats', 'recentActivity', 'systemInfo'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_users' => $this->getTotalUsers(),
            'active_users' => $this->getActiveUsers(),
            'new_users_today' => $this->getNewUsersToday(),
            'total_hb837_projects' => $this->getTotalHB837Projects(),
            'active_projects' => $this->getActiveProjects(),
            'completed_projects' => $this->getCompletedProjects(),
            'login_attempts_today' => $this->getLoginAttemptsToday(),
            'system_health' => $this->getSystemHealth()
        ];
    }

    /**
     * Get recent activity for the dashboard
     */
    private function getRecentActivity()
    {
        // Mock data for now - will be replaced with actual activity logs
        return collect([
            [
                'icon' => 'fas fa-user-plus',
                'color' => 'text-success',
                'action' => 'New user registered',
                'details' => 'User joined the system',
                'time' => Carbon::now()->subMinutes(10),
                'user' => Auth::user()->name
            ],
            [
                'icon' => 'fas fa-sign-in-alt',
                'color' => 'text-info',
                'action' => 'User logged in',
                'details' => 'Successful authentication',
                'time' => Carbon::now()->subMinutes(25),
                'user' => Auth::user()->name
            ],
            [
                'icon' => 'fas fa-cogs',
                'color' => 'text-warning',
                'action' => 'Settings updated',
                'details' => 'System settings modified',
                'time' => Carbon::now()->subHour(),
                'user' => 'Admin'
            ],
            [
                'icon' => 'fas fa-project-diagram',
                'color' => 'text-primary',
                'action' => 'HB837 project created',
                'details' => 'New compliance project added',
                'time' => Carbon::now()->subHours(2),
                'user' => 'Project Manager'
            ]
        ]);
    }

    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        return [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server_time' => Carbon::now()->format('M j, Y g:i A T'),
            'database_type' => config('database.default'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => app()->isDownForMaintenance()
        ];
    }

    /**
     * Get total users count
     */
    private function getTotalUsers()
    {
        return DB::table('users')->count();
    }

    /**
     * Get active users count (users who have logged in within 30 days)
     */
    private function getActiveUsers()
    {
        // For now, return all verified users since we don't track last_login yet
        return DB::table('users')->whereNotNull('email_verified_at')->count();
    }

    /**
     * Get new users today
     */
    private function getNewUsersToday()
    {
        return DB::table('users')
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    /**
     * Get total HB837 projects
     */
    private function getTotalHB837Projects()
    {
        // Check if table exists first
        try {
            return DB::table('hb837')->count();
        } catch (\Exception $e) {
            return 0; // Table doesn't exist yet
        }
    }

    /**
     * Get active projects count
     */
    private function getActiveProjects()
    {
        try {
            return DB::table('hb837')
                ->whereIn('report_status', ['in-progress', 'not-started'])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get completed projects count
     */
    private function getCompletedProjects()
    {
        try {
            return DB::table('hb837')
                ->where('report_status', 'completed')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get login attempts today (mock data for now)
     */
    private function getLoginAttemptsToday()
    {
        return rand(10, 50); // Mock data until we implement login tracking
    }

    /**
     * Get system health status
     */
    private function getSystemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
        ];

        // Determine overall health
        $issues = collect($health)->filter(function ($status) {
            return $status !== 'good';
        });

        if ($issues->contains('critical')) {
            $health['overall'] = 'critical';
        } elseif ($issues->contains('warning')) {
            $health['overall'] = 'warning';
        } else {
            $health['overall'] = 'good';
        }

        return $health;
    }

    /**
     * Check database health
     */
    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return 'good';
        } catch (\Exception $e) {
            return 'critical';
        }
    }

    /**
     * Check storage health
     */
    private function checkStorageHealth()
    {
        try {
            $storagePercent = $this->getStorageUsagePercent();
            if ($storagePercent > 90) {
                return 'critical';
            } elseif ($storagePercent > 75) {
                return 'warning';
            }
            return 'good';
        } catch (\Exception $e) {
            return 'warning';
        }
    }

    /**
     * Check cache health
     */
    private function checkCacheHealth()
    {
        try {
            cache()->put('health_check', true, 60);
            $retrieved = cache()->get('health_check');
            return $retrieved ? 'good' : 'warning';
        } catch (\Exception $e) {
            return 'warning';
        }
    }

    /**
     * Get storage usage percentage (mock for now)
     */
    private function getStorageUsagePercent()
    {
        return rand(20, 60); // Mock data
    }
}

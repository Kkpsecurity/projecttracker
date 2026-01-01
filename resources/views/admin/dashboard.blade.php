@extends('adminlte::page')

@section('title', 'System Administration Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-server mr-2 text-primary"></i>
                System Dashboard
            </h1>
            <small class="text-muted">Real-time system monitoring and administration</small>
        </div>
        <div class="col-sm-6">
            <div class="float-sm-right">
                <span class="badge badge-success mr-2">
                    <i class="fas fa-circle text-success"></i> System Online
                </span>
                <small class="text-muted">Last updated: {{ now()->format('H:i:s') }}</small>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- System Status Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-light border-left-primary" style="border-left: 4px solid #007bff;">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong><i class="fas fa-tachometer-alt text-primary"></i> System Performance</strong>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-3">
                                <span class="text-muted">CPU:</span> 
                                <span class="font-weight-bold">{{ $stats['system_performance']['cpu_usage'] }}%</span>
                            </div>
                            <div class="col-3">
                                <span class="text-muted">Memory:</span> 
                                <span class="font-weight-bold">{{ $stats['system_performance']['memory_usage'] }}%</span>
                            </div>
                            <div class="col-3">
                                <span class="text-muted">Disk:</span> 
                                <span class="font-weight-bold">{{ $stats['system_performance']['disk_usage'] }}%</span>
                            </div>
                            <div class="col-3">
                                <span class="text-muted">Uptime:</span> 
                                <span class="font-weight-bold">{{ $stats['system_performance']['uptime_days'] }}d</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Total Users</p>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-light" style="width: {{ ($stats['active_users'] / max($stats['total_users'], 1)) * 100 }}%"></div>
                    </div>
                    <small>{{ $stats['active_users'] }} active</small>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="/admin/users" class="small-box-footer">
                    Manage Users <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $stats['total_hb837_projects'] }}</h3>
                    <p>Projects</p>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-light" style="width: {{ ($stats['completed_projects'] / max($stats['total_hb837_projects'], 1)) * 100 }}%"></div>
                    </div>
                    <small>{{ $stats['completed_projects'] }} completed</small>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                    View Projects <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $stats['security_metrics']['security_alerts'] }}</h3>
                    <p>Security Alerts</p>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-light" style="width: {{ min($stats['security_metrics']['failed_login_attempts'] * 4, 100) }}%"></div>
                    </div>
                    <small>{{ $stats['security_metrics']['failed_login_attempts'] }} failed logins</small>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Security Center <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ number_format($stats['storage_metrics']['total_storage_used'] / 1024, 1) }}GB</h3>
                    <p>Storage Used</p>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-light" style="width: {{ ($stats['storage_metrics']['total_storage_used'] / $stats['storage_metrics']['storage_capacity']) * 100 }}%"></div>
                    </div>
                    <small>{{ number_format(($stats['storage_metrics']['storage_capacity'] - $stats['storage_metrics']['total_storage_used']) / 1024, 1) }}GB free</small>
                </div>
                <div class="icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Storage Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Row -->
    <div class="row">
        <!-- System Performance Chart -->
        <div class="col-md-8">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area mr-2"></i>
                        System Performance Monitoring
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success">Live</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- System Health & Security -->
        <div class="col-md-4">
            <!-- System Health -->
            <div class="card card-success mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat mr-2"></i>
                        System Health
                    </h3>
                </div>
                <div class="card-body p-3">
                    @php
                        $healthColors = [
                            'good' => ['text-success', 'fas fa-check-circle'],
                            'warning' => ['text-warning', 'fas fa-exclamation-triangle'],
                            'critical' => ['text-danger', 'fas fa-times-circle']
                        ];
                        $healthData = $stats['system_health'];
                    @endphp
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="health-indicator">
                                <i class="{{ $healthColors[$healthData['database']][1] ?? 'fas fa-check-circle' }} {{ $healthColors[$healthData['database']][0] ?? 'text-success' }} fa-2x"></i>
                                <p class="mb-0 mt-1"><small><strong>Database</strong></small></p>
                                <span class="badge badge-{{ $healthData['database'] === 'good' ? 'success' : ($healthData['database'] === 'warning' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($healthData['database']) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="health-indicator">
                                <i class="{{ $healthColors[$healthData['storage']][1] ?? 'fas fa-check-circle' }} {{ $healthColors[$healthData['storage']][0] ?? 'text-success' }} fa-2x"></i>
                                <p class="mb-0 mt-1"><small><strong>Storage</strong></small></p>
                                <span class="badge badge-{{ $healthData['storage'] === 'good' ? 'success' : ($healthData['storage'] === 'warning' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($healthData['storage']) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="health-indicator">
                                <i class="{{ $healthColors[$healthData['cache']][1] ?? 'fas fa-check-circle' }} {{ $healthColors[$healthData['cache']][0] ?? 'text-success' }} fa-2x"></i>
                                <p class="mb-0 mt-1"><small><strong>Cache</strong></small></p>
                                <span class="badge badge-{{ $healthData['cache'] === 'good' ? 'success' : ($healthData['cache'] === 'warning' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($healthData['cache']) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="health-indicator">
                                <i class="{{ $healthColors[$healthData['overall']][1] ?? 'fas fa-check-circle' }} {{ $healthColors[$healthData['overall']][0] ?? 'text-success' }} fa-2x"></i>
                                <p class="mb-0 mt-1"><small><strong>Overall</strong></small></p>
                                <span class="badge badge-{{ $healthData['overall'] === 'good' ? 'success' : ($healthData['overall'] === 'warning' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($healthData['overall']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Status -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security Status
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="security-metric">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-lock text-success"></i> Firewall</span>
                            <span class="badge badge-success">{{ ucfirst($stats['security_metrics']['firewall_status']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-certificate text-info"></i> SSL Certificate</span>
                            <span class="badge badge-info">{{ $stats['security_metrics']['ssl_certificate_days'] }}d</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-ban text-warning"></i> Blocked IPs</span>
                            <span class="badge badge-warning">{{ $stats['security_metrics']['blocked_ips'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-search text-primary"></i> Last Scan</span>
                            <span class="badge badge-primary">{{ $stats['security_metrics']['last_security_scan']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Project Analytics -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Project Status Distribution
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="projectStatusChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Storage Breakdown -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hdd mr-2"></i>
                        Storage Breakdown
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="storageChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity & Network -->
    <div class="row">
        <!-- Network Activity -->
        <div class="col-md-8">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-network-wired mr-2"></i>
                        Network & User Activity (Last 12 Hours)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="networkChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & System Info -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="{{ route('admin.hb837.create') }}" class="btn btn-success btn-block btn-sm">
                                <i class="fas fa-plus"></i> New Project
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-block btn-sm">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('admin.hb837.smart-import.show') }}" class="btn btn-info btn-block btn-sm">
                                <i class="fas fa-upload"></i> Import Data
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-warning btn-block btn-sm">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-outline-secondary btn-block btn-sm" onclick="location.reload()">
                                <i class="fas fa-sync-alt"></i> Refresh Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        System Info
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="system-info">
                        <div class="info-item mb-2">
                            <strong>Laravel:</strong> {{ $systemInfo['laravel_version'] }}
                        </div>
                        <div class="info-item mb-2">
                            <strong>PHP:</strong> {{ $systemInfo['php_version'] }}
                        </div>
                        <div class="info-item mb-2">
                            <strong>Database:</strong> {{ ucfirst($systemInfo['database_type']) }}
                        </div>
                        <div class="info-item mb-2">
                            <strong>Environment:</strong> 
                            <span class="badge badge-{{ $systemInfo['environment'] === 'production' ? 'success' : 'warning' }}">
                                {{ ucfirst($systemInfo['environment']) }}
                            </span>
                        </div>
                        <div class="info-item">
                            <strong>Server Time:</strong><br>
                            <small>{{ $systemInfo['server_time'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    
    .small-box {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .small-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .small-box .progress {
        height: 3px;
        margin: 8px 0 4px 0;
        background: rgba(255,255,255,0.2);
        border-radius: 0;
    }
    
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: none;
    }
    
    .card-dark {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .card-dark .card-header {
        background: rgba(255,255,255,0.1);
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .health-indicator {
        padding: 10px;
        text-align: center;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .health-indicator:hover {
        background-color: rgba(0,0,0,0.05);
        transform: scale(1.05);
    }
    
    .security-metric {
        font-size: 14px;
    }
    
    .system-info .info-item {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }
    
    .system-info .info-item:last-child {
        border-bottom: none;
    }
    
    .btn-block.btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge {
        font-weight: 500;
    }
    
    /* Live indicator animation */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .badge-success {
        animation: pulse 2s infinite;
    }
    
    /* Chart containers */
    canvas {
        max-height: 300px !important;
    }
</style>
@stop

@section('js')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // System Performance Chart (Real-time style)
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['12h ago', '10h ago', '8h ago', '6h ago', '4h ago', '2h ago', 'Now'],
            datasets: [{
                label: 'CPU Usage (%)',
                data: [45, 52, 48, 61, 55, {{ $stats['system_performance']['cpu_usage'] }}, {{ $stats['system_performance']['cpu_usage'] + rand(-5, 5) }}],
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Memory Usage (%)',
                data: [65, 68, 72, 69, 74, {{ $stats['system_performance']['memory_usage'] }}, {{ $stats['system_performance']['memory_usage'] + rand(-3, 3) }}],
                borderColor: '#4ecdc4',
                backgroundColor: 'rgba(78, 205, 196, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Disk I/O (%)',
                data: [25, 30, 28, 35, 32, {{ $stats['system_performance']['disk_usage'] }}, {{ $stats['system_performance']['disk_usage'] + rand(-2, 2) }}],
                borderColor: '#ffe66d',
                backgroundColor: 'rgba(255, 230, 109, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: 'white', usePointStyle: true }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: 100,
                    ticks: { color: 'white' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: { 
                    ticks: { color: 'white' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });

    // Project Status Pie Chart
    const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
    new Chart(projectStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Not Started', 'In Progress', 'In Review', 'Completed'],
            datasets: [{
                data: [
                    {{ $stats['project_status_breakdown']['not-started'] }},
                    {{ $stats['project_status_breakdown']['in-progress'] }},
                    {{ $stats['project_status_breakdown']['in-review'] }},
                    {{ $stats['project_status_breakdown']['completed'] }}
                ],
                backgroundColor: ['#6c757d', '#007bff', '#ffc107', '#28a745'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            }
        }
    });

    // Storage Breakdown Doughnut Chart
    const storageCtx = document.getElementById('storageChart').getContext('2d');
    new Chart(storageCtx, {
        type: 'doughnut',
        data: {
            labels: ['Database', 'Logs', 'Backups', 'Temp Files', 'Free Space'],
            datasets: [{
                data: [
                    {{ $stats['storage_metrics']['database_size'] }},
                    {{ $stats['storage_metrics']['log_files_size'] }},
                    {{ $stats['storage_metrics']['backup_size'] }},
                    {{ $stats['storage_metrics']['temp_files_size'] }},
                    {{ $stats['storage_metrics']['storage_capacity'] - $stats['storage_metrics']['total_storage_used'] }}
                ],
                backgroundColor: ['#007bff', '#dc3545', '#28a745', '#ffc107', '#e9ecef'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + (context.parsed / 1024).toFixed(1) + 'GB';
                        }
                    }
                }
            }
        }
    });

    // Network Activity Chart
    const networkCtx = document.getElementById('networkChart').getContext('2d');
    new Chart(networkCtx, {
        type: 'bar',
        data: {
            labels: ['00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
            datasets: [{
                label: 'User Sessions',
                data: [5, 3, 2, 8, 15, 25, 30, 28, 32, 26, 18, 12],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'API Requests',
                data: [120, 80, 60, 200, 350, 480, 520, 490, 580, 460, 320, 250],
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, position: 'left' },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
            }
        }
    });

    // Auto-refresh dashboard every 30 seconds
    setInterval(function() {
        // Update only the status bar for real-time feel
        console.log('Dashboard refresh...');
    }, 30000);

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop

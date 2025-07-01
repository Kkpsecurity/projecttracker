@extends('adminlte::page')

@section('title', 'Admin Dashboard - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Admin Dashboard
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Admin Center</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Admin Center Quick Access -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>
                        Admin Center
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-app bg-primary">
                                <i class="fas fa-users-cog"></i>
                                User Management
                            </a>
                            <p class="text-muted">Manage user accounts, roles, and permissions</p>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-app bg-warning">
                                <i class="fas fa-sliders-h"></i>
                                System Settings
                            </a>
                            <p class="text-muted">Configure system-wide settings and preferences</p>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.logs.index') }}" class="btn btn-app bg-info">
                                <i class="fas fa-list-alt"></i>
                                Activity Logs
                            </a>
                            <p class="text-muted">Monitor system activity and audit trails</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['active_users'] }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_hb837_projects'] }}</h3>
                    <p>HB837 Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['login_attempts_today'] }}</h3>
                    <p>Login Attempts Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <a href="{{ route('admin.logs.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- System Health -->
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat mr-2"></i>
                        System Health
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $healthColors = [
                            'good' => ['text-success', 'fas fa-check-circle'],
                            'warning' => ['text-warning', 'fas fa-exclamation-triangle'],
                            'critical' => ['text-danger', 'fas fa-times-circle']
                        ];
                        $healthData = $systemInfo['database'] ?? 'good';
                    @endphp

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="description-block border-right">
                                <span class="{{ $healthColors[$healthData][0] ?? 'text-success' }}">
                                    <i class="{{ $healthColors[$healthData][1] ?? 'fas fa-check-circle' }} fa-2x"></i>
                                </span>
                                <h5 class="description-header">Database</h5>
                                <span class="description-text">{{ ucfirst($healthData) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="description-block">
                                @php $storageData = $systemInfo['storage'] ?? 'good'; @endphp
                                <span class="{{ $healthColors[$storageData][0] ?? 'text-success' }}">
                                    <i class="{{ $healthColors[$storageData][1] ?? 'fas fa-check-circle' }} fa-2x"></i>
                                </span>
                                <h5 class="description-header">Storage</h5>
                                <span class="description-text">{{ ucfirst($storageData) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="description-block border-right">
                                @php $cacheData = $systemInfo['cache'] ?? 'good'; @endphp
                                <span class="{{ $healthColors[$cacheData][0] ?? 'text-success' }}">
                                    <i class="{{ $healthColors[$cacheData][1] ?? 'fas fa-check-circle' }} fa-2x"></i>
                                </span>
                                <h5 class="description-header">Cache</h5>
                                <span class="description-text">{{ ucfirst($cacheData) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="description-block">
                                @php $overallData = $systemInfo['overall'] ?? 'good'; @endphp
                                <span class="{{ $healthColors[$overallData][0] ?? 'text-success' }}">
                                    <i class="{{ $healthColors[$overallData][1] ?? 'fas fa-check-circle' }} fa-2x"></i>
                                </span>
                                <h5 class="description-header">Overall</h5>
                                <span class="description-text">{{ ucfirst($overallData) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-2"></i>
                        Recent Activity
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-tool">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recentActivity as $activity)
                        <div class="time-label">
                            <span class="bg-gray">{{ $activity['time']->diffForHumans() }}</span>
                        </div>
                        <div>
                            <i class="{{ $activity['icon'] }} {{ $activity['color'] }}"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">
                                    <strong>{{ $activity['action'] }}</strong>
                                    <small class="text-muted">by {{ $activity['user'] }}</small>
                                </h3>
                                <div class="timeline-body">
                                    {{ $activity['details'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        System Information
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header">{{ $systemInfo['laravel_version'] }}</h5>
                                <span class="description-text">Laravel Version</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header">{{ $systemInfo['php_version'] }}</h5>
                                <span class="description-text">PHP Version</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header">{{ ucfirst($systemInfo['database_type']) }}</h5>
                                <span class="description-text">Database</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block">
                                <h5 class="description-header">{{ ucfirst($systemInfo['environment']) }}</h5>
                                <span class="description-text">Environment</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="description-block border-right">
                                <h5 class="description-header">{{ $systemInfo['server_time'] }}</h5>
                                <span class="description-text">Server Time</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="description-block">
                                <h5 class="description-header">
                                    @if($systemInfo['maintenance_mode'])
                                        <span class="text-warning">Enabled</span>
                                    @else
                                        <span class="text-success">Disabled</span>
                                    @endif
                                </h5>
                                <span class="description-text">Maintenance Mode</span>
                            </div>
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
    .btn-app {
        min-width: 80px;
        text-align: center;
        border-radius: 5px;
        margin: 5px;
        padding: 15px 5px;
    }
    .small-box .icon {
        top: 10px;
    }
    .description-block {
        margin: 0;
        padding: 15px;
    }
    .timeline {
        position: relative;
        margin: 0;
    }
    .timeline > div > .timeline-item {
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        border-radius: 3px;
        position: relative;
        margin: 0 0 15px 25px;
        padding: 10px;
        background: #fff;
    }
    .timeline > div > .timeline-item > .timeline-header {
        margin: 0;
        color: #555;
        border-bottom: 1px solid #e9ecef;
        padding: 0 0 10px 0;
        font-weight: 600;
    }
    .timeline > div > .timeline-item > .timeline-body {
        padding: 10px 0 0 0;
        color: #666;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto-refresh health status every 5 minutes
    setInterval(function() {
        // Will implement AJAX refresh later
        console.log('Health check refresh');
    }, 300000);

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop

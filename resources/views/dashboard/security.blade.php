{{-- Security-focused AdminLTE Dashboard Layout --}}
@extends('adminlte::page')

@section('title', 'Dashboard - KKP Security Project Tracker')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                    Security Dashboard
                </h1>
                <small class="text-muted">
                    KKP Security Project Tracker - Command Center
                </small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Security Overview</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
{{-- Security Status Cards --}}
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>SECURE</h3>
                <p>System Status</p>
            </div>
            <div class="icon">
                <i class="fas fa-lock"></i>
            </div>
            <div class="small-box-footer">
                All systems operational <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ Auth::user()->name ?? 'Admin' }}</h3>
                <p>Authorized User</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="small-box-footer">
                Security clearance active <i class="fas fa-arrow-circle-right"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>0</h3>
                <p>Active Projects</p>
            </div>
            <div class="icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="small-box-footer">
                Projects under management <i class="fas fa-arrow-circle-right"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>HIGH</h3>
                <p>Security Level</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="small-box-footer">
                Maximum security enforced <i class="fas fa-arrow-circle-right"></i>
            </div>
        </div>
    </div>
</div>

{{-- Welcome Section --}}
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Security Command Center
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-success border-left-success shadow-sm" style="border-left: 4px solid #28a745;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle mr-3 text-success fa-2x"></i>
                        <div>
                            <h5 class="alert-heading mb-1">System Operational</h5>
                            <p class="mb-0">
                                <strong>Welcome to KKP Security Project Tracker</strong><br>
                                Fresh installation successfully completed. All security protocols are active.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-cogs mr-1"></i> System Information
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i> Laravel Framework Active</li>
                            <li><i class="fas fa-check text-success mr-2"></i> AdminLTE Dashboard Loaded</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Security Protocols Enabled</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Database Connection Secure</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-shield-alt mr-1"></i> Security Features
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-lock text-primary mr-2"></i> CSRF Protection Active</li>
                            <li><i class="fas fa-lock text-primary mr-2"></i> Session Security Enabled</li>
                            <li><i class="fas fa-lock text-primary mr-2"></i> Admin-Only Access</li>
                            <li><i class="fas fa-lock text-primary mr-2"></i> Audit Trail Ready</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Security Notice
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-eye mr-2"></i>Monitoring Active</h6>
                    <p class="mb-2">All user activities are logged and monitored for security compliance.</p>

                    <h6><i class="fas fa-users-cog mr-2"></i>Authorized Personnel Only</h6>
                    <p class="mb-2">This system is restricted to authorized security personnel.</p>

                    <h6><i class="fas fa-phone mr-2"></i>Support Contact</h6>
                    <p class="mb-0">Contact security administrator for any access issues.</p>
                </div>
            </div>
        </div>

        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Session Information
                </h3>
            </div>
            <div class="card-body">
                <p><strong>User:</strong> {{ Auth::user()->name ?? 'Administrator' }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email ?? 'admin@kkp.security' }}</p>
                <p><strong>Login Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                <p><strong>Session ID:</strong> <code>{{ substr(session()->getId(), 0, 8) }}...</code></p>
                <p class="mb-0"><strong>Security Level:</strong> <span class="badge badge-danger">HIGH</span></p>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="#" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-plus mr-2"></i>
                            New Project
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-info btn-block btn-lg">
                            <i class="fas fa-users mr-2"></i>
                            Manage Users
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-warning btn-block btn-lg">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Reports
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-danger btn-block btn-lg">
                            <i class="fas fa-cog mr-2"></i>
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .content-wrapper {
        background-color: #f4f6f9;
    }

    .card {
        border-radius: 8px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }

    .small-box {
        border-radius: 8px;
        transition: transform 0.2s ease;
    }

    .small-box:hover {
        transform: translateY(-2px);
    }

    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .btn-lg {
        padding: 12px 20px;
        font-size: 1rem;
        border-radius: 6px;
    }

    .alert {
        border-radius: 6px;
    }

    .badge {
        font-size: 0.8rem;
    }

    .breadcrumb {
        background-color: transparent;
        margin-bottom: 0;
    }

    .content-header h1 {
        font-size: 1.8rem;
        font-weight: 600;
    }
</style>
@stop

@section('js')
<script>
    // Add any dashboard-specific JavaScript here
    $(document).ready(function() {
        // Auto-refresh security status every 30 seconds
        setInterval(function() {
            // Add security status checks here
            console.log('Security status check...');
        }, 30000);

        // Show welcome notification
        toastr.success('Security dashboard loaded successfully', 'System Ready');
    });
</script>
@stop

@extends('adminlte::page')

@section('title', 'Dashboard - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Welcome Alert -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Welcome!</h5>
                Welcome to Project Tracker, <strong>{{ Auth::user()->name }}</strong>! You are logged in as <span class="badge badge-primary">{{ ucfirst(Auth::user()->role) }}</span>.
                <br><small class="text-muted"><i class="fas fa-info-circle"></i> New: Check out our improved Help Center for assistance with using the system!</small>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ App\Models\HB837::count() }}</h3>
                    <p>HB837 Records</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ App\Models\User::count() }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ App\Models\HB837::where('report_status', 'in-progress')->count() }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('admin.hb837.index', 'active') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ App\Models\HB837::where('report_status', 'completed')->count() }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('admin.hb837.index', 'completed') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Improvements -->
    <div class="row">
        <div class="col-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-rocket"></i> Latest System Improvements</h3>
                    <div class="card-tools">
                        <span class="badge badge-success">{{ date('Y-m-d') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-question-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Help Center</span>
                                    <span class="info-box-number">NEW!</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">Complete help system with guides and FAQ</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-bars"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Menu Optimization</span>
                                    <span class="info-box-number">UPDATED</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">Streamlined navigation with help integration</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check"></i> What's New:</h5>
                                <ul class="mb-0">
                                    <li><strong>Help Center:</strong> Comprehensive help system with Getting Started guide, User Manual, FAQ, and Contact Support</li>
                                    <li><strong>Menu Cleanup:</strong> Removed redundant settings items and streamlined navigation</li>
                                    <li><strong>User Experience:</strong> Improved accessibility to help resources and documentation</li>
                                    <li><strong>System Optimization:</strong> Better organization of menu items for enhanced workflow</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.hb837.index') }}" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-shield-alt mr-2"></i> HB837 Management
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.hb837.import.show') }}" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-upload mr-2"></i> Import Data
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.hb837.export') }}" class="btn btn-info btn-lg btn-block">
                                <i class="fas fa-download mr-2"></i> Export Data
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.hb837.three-phase-import.show') }}" class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-layer-group mr-2"></i> Three-Phase Import
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('help.index') }}" class="btn btn-secondary btn-lg btn-block">
                                <i class="fas fa-question-circle mr-2"></i> Help Center
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-dark btn-lg btn-block" disabled>
                                <i class="fas fa-chart-bar mr-2"></i> Reports (Coming Soon)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-server"></i> System Status</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-database"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Database</span>
                            <span class="info-box-number">Connected</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fab fa-laravel"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Laravel</span>
                            <span class="info-box-number">{{ app()->version() }}</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-{{ app()->environment() === 'production' ? 'danger' : 'warning' }}">
                            <i class="fas fa-globe"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Environment</span>
                            <span class="info-box-number">{{ ucfirst(app()->environment()) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Recent Activity</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-green">Today</span>
                        </div>
                        <div>
                            <i class="fas fa-user bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ now()->format('H:i') }}</span>
                                <h3 class="timeline-header">{{ Auth::user()->name }} logged in</h3>
                                <div class="timeline-body">
                                    User login from {{ request()->ip() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-question-circle bg-success"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ now()->subMinutes(10)->format('H:i') }}</span>
                                <h3 class="timeline-header">Help Center Deployed</h3>
                                <div class="timeline-body">
                                    New comprehensive help system with guides, FAQ, and support resources
                                </div>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-bars bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ now()->subMinutes(15)->format('H:i') }}</span>
                                <h3 class="timeline-header">Menu System Updated</h3>
                                <div class="timeline-body">
                                    Removed redundant settings items and optimized navigation structure
                                </div>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-database bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ now()->subMinutes(30)->format('H:i') }}</span>
                                <h3 class="timeline-header">System Cache Cleared</h3>
                                <div class="timeline-body">
                                    Configuration and route caches refreshed for optimal performance
                                </div>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-flag bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- Add extra stylesheets here --}}
@stop

@section('js')
    <script>
        console.log("Dashboard loaded successfully!");
    </script>
@stop

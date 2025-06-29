@extends('dashboard.security')

@section('dashboard_content')
<div class="row">
    {{-- Welcome Card --}}
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-shield mr-2"></i>
                    Welcome, {{ Auth::user()->name }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">
                        <i class="fas fa-circle mr-1"></i>
                        ONLINE
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Security Notice</h5>
                    You are logged into the KKP Security Project Tracker. All activities are monitored and logged for security purposes.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-tasks text-primary mr-2"></i>Quick Actions</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-plus-circle text-success mr-2"></i>Create New Project</li>
                            <li><i class="fas fa-eye text-info mr-2"></i>View Active Projects</li>
                            <li><i class="fas fa-chart-line text-warning mr-2"></i>Generate Reports</li>
                            <li><i class="fas fa-cog text-secondary mr-2"></i>System Settings</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-bell text-primary mr-2"></i>Recent Activity</h5>
                        <div class="timeline timeline-inverse">
                            <div class="time-label">
                                <span class="bg-success">Today</span>
                            </div>
                            <div>
                                <i class="fas fa-user bg-primary"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ now()->format('H:i') }}</span>
                                    <h3 class="timeline-header">Successfully logged in</h3>
                                    <div class="timeline-body">
                                        Secure authentication completed from {{ request()->ip() }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-shield-alt bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> System</span>
                                    <h3 class="timeline-header">Security check passed</h3>
                                    <div class="timeline-body">
                                        All security protocols are functioning normally
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Security Metrics Row --}}
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-project-diagram mr-2"></i>
                    Active Projects
                </h3>
            </div>
            <div class="card-body text-center">
                <h2 class="text-primary">0</h2>
                <p class="text-muted">Currently being tracked</p>
                <a href="#" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i>
                    Add Project
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-check-circle mr-2"></i>
                    Completed Tasks
                </h3>
            </div>
            <div class="card-body text-center">
                <h2 class="text-success">0</h2>
                <p class="text-muted">Tasks completed today</p>
                <a href="#" class="btn btn-success btn-sm">
                    <i class="fas fa-eye mr-1"></i>
                    View Details
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Pending Reviews
                </h3>
            </div>
            <div class="card-body text-center">
                <h2 class="text-warning">0</h2>
                <p class="text-muted">Items awaiting approval</p>
                <a href="#" class="btn btn-warning btn-sm">
                    <i class="fas fa-list mr-1"></i>
                    Review Queue
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

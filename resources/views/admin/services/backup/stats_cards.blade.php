<div class="row mb-4">
    <!-- HB837 Projects Overview -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-card-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-building fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-muted mb-1">Total Properties</h5>
                        <h2 class="mb-0">{{ $systemStats['hb837']['total_properties'] ?? 0 }}</h2>
                        <small class="text-success">
                            <i class="fa fa-check-circle me-1"></i>
                            {{ $systemStats['hb837']['completed_projects'] ?? 0 }} Completed
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-card-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-clock fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-muted mb-1">Active Projects</h5>
                        <h2 class="mb-0">{{ $systemStats['hb837']['active_projects'] ?? 0 }}</h2>
                        <small class="text-danger">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            {{ $systemStats['hb837']['overdue_reports'] ?? 0 }} Overdue
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Activity -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-card-icon bg-success bg-opacity-10 text-success">
                        <i class="fa fa-upload fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-muted mb-1">Total Imports</h5>
                        <h2 class="mb-0">{{ $systemStats['imports']['total_imports'] ?? 0 }}</h2>
                        <small class="text-success">
                            <i class="fa fa-check-circle me-1"></i>
                            {{ $systemStats['imports']['this_month_imports'] ?? 0 }} This Month
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Status -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-card-icon bg-info bg-opacity-10 text-info">
                        <i class="fa fa-database fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-muted mb-1">Backups</h5>
                        <h2 class="mb-0">{{ $stats['total_backups'] ?? 0 }}</h2>
                        <small class="text-muted">
                            <i class="fa fa-clock me-1"></i>
                            Last: {{ $stats['last_backup_ago'] ?? 'Never' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Stats Row -->
<div class="row mb-4">
    <!-- Data Health & Import Stats -->
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header bg-light border-0 pb-2 d-flex align-items-center justify-content-between">
                <span class="fw-semibold text-secondary">
                    <i class="fa fa-heartbeat me-2"></i>Data Health & Import Activity
                </span>
                <span class="badge rounded-pill bg-success" style="font-size: 1rem;">
                    {{ $systemStats['hb837']['data_health_score'] ?? 0 }}%
                </span>
            </div>
            <div class="card-body pt-3">
                <!-- Data Health Score -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Data Completeness</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $systemStats['hb837']['data_health_score'] ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">Based on required field completeness</small>
                </div>
                <!-- Import Statistics -->
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="p-2 border-end">
                            <h6 class="text-success mb-0">{{ $systemStats['imports']['successful_imports'] ?? 0 }}</h6>
                            <small class="text-muted">Successful</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <h6 class="text-danger mb-0">{{ $systemStats['imports']['failed_imports'] ?? 0 }}</h6>
                            <small class="text-muted">Failed</small>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <!-- Recent Import Activity -->
                <div class="text-center">
                    <h6 class="mb-0">{{ $systemStats['hb837']['recent_imports'] ?? 0 }}</h6>
                    <small class="text-muted">Imports in Last 30 Days</small>
                </div>
            </div>
        </div>
    </div>

    <!-- System Resources -->
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h6 class="text-muted mb-0">
                    <i class="fa fa-server me-2"></i>System Resources
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="p-3">
                            <i class="fa fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="mb-0">{{ $systemStats['system_counts']['consultants'] ?? 0 }}</h5>
                            <small class="text-muted">Consultants</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3">
                            <i class="fa fa-user-tie fa-2x text-success mb-2"></i>
                            <h5 class="mb-0">{{ $systemStats['system_counts']['owners'] ?? 0 }}</h5>
                            <small class="text-muted">Owners</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3">
                            <i class="fa fa-user-shield fa-2x text-info mb-2"></i>
                            <h5 class="mb-0">{{ $systemStats['system_counts']['users'] ?? 0 }}</h5>
                            <small class="text-muted">Users</small>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Storage Stats -->
                <div class="row text-center">
                    <div class="col-6">
                        <div class="p-2">
                            <h6 class="mb-0">{{ $stats['total_size'] ?? '0 B' }}</h6>
                            <small class="text-muted">Storage Used</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <h6 class="mb-0">{{ $stats['success_rate'] ?? '0%' }}</h6>
                            <small class="text-muted">Success Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-xl-4 col-lg-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h6 class="text-muted mb-0">
                    <i class="fa fa-history me-2"></i>Recent Activity
                </h6>
            </div>
            <div class="card-body">
                @if(!empty($recentActivity))
                    <div class="activity-feed" style="max-height: 300px; overflow-y: auto;">
                        @foreach(array_slice($recentActivity, 0, 5) as $activity)
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-{{ $activity['color'] }} bg-opacity-10 text-{{ $activity['color'] }} d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fa {{ $activity['icon'] }} fa-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 small">{{ $activity['action'] }}</h6>
                                    <p class="mb-1 small text-muted">{{ $activity['description'] }}</p>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                                        by {{ $activity['user'] }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fa fa-clock fa-2x mb-2"></i>
                        <p>No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.services.backup.monthly_trends_new')

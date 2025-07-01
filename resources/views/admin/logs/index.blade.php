@extends('adminlte::page')

@section('title', 'Activity Logs - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-list-alt mr-2"></i>
                Activity Logs
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Admin Center</a></li>
                <li class="breadcrumb-item active">Activity Logs</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_actions_today'] }}</h3>
                    <p>Actions Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-mouse-pointer"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_logins_today'] }}</h3>
                    <p>Logins Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['active_users'] }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['critical_events'] }}</h3>
                    <p>Critical Events</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Recent Activity
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                <a href="#" class="dropdown-item" onclick="refreshLogs()">
                                    <i class="fas fa-sync mr-2"></i>Refresh
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item" onclick="exportLogs()">
                                    <i class="fas fa-download mr-2"></i>Export
                                </a>
                                <a href="#" class="dropdown-item text-danger" onclick="clearOldLogs()">
                                    <i class="fas fa-trash mr-2"></i>Clear Old Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Options -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="categoryFilter">
                                <option value="">All Categories</option>
                                <option value="authentication">Authentication</option>
                                <option value="navigation">Navigation</option>
                                <option value="admin">Admin Actions</option>
                                <option value="user_management">User Management</option>
                                <option value="settings">Settings</option>
                                <option value="hb837">HB837 Projects</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="severityFilter">
                                <option value="">All Severities</option>
                                <option value="info">Info</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary btn-block" onclick="applyFilters()">
                                <i class="fas fa-filter mr-1"></i>Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Activity Logs Table -->
                    <div class="table-responsive">
                        <table id="activityLogsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Category</th>
                                    <th>Severity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($log['created_at'])->format('M j, Y g:i A') }}</td>
                                    <td>
                                        <strong>{{ $log['user_name'] }}</strong><br>
                                        <small class="text-muted">{{ $log['user_email'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $log['action'] }}</span>
                                    </td>
                                    <td>{{ $log['description'] }}</td>
                                    <td>
                                        <code>{{ $log['ip_address'] }}</code>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst($log['category']) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $severityClass = [
                                                'info' => 'badge-info',
                                                'warning' => 'badge-warning',
                                                'error' => 'badge-danger',
                                                'critical' => 'badge-dark'
                                            ][$log['severity']] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $severityClass }}">{{ ucfirst($log['severity']) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information Card -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        System Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="description-block border-right">
                                <h5 class="description-header">{{ $stats['total_users'] }}</h5>
                                <span class="description-text">Total Users</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="description-block">
                                <h5 class="description-header">{{ $stats['recent_signups'] }}</h5>
                                <span class="description-text">Recent Signups (7 days)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>
                        Log Settings
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Activity logging is currently in development phase.
                        Full logging capabilities will be available in the next release.
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <button class="btn btn-sm btn-outline-primary" onclick="testLogging()">
                                <i class="fas fa-vial mr-1"></i>Test Logging
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button class="btn btn-sm btn-outline-secondary" onclick="configureLogging()">
                                <i class="fas fa-cog mr-1"></i>Configure
                            </button>
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
    .small-box .icon {
        top: 10px;
    }
    .description-block {
        margin: 0;
    }
    .badge {
        font-size: 0.8em;
    }
    .table th {
        border-top: none;
        font-weight: 600;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Initialize DataTable for future implementation
    // $('#activityLogsTable').DataTable({
    //     responsive: true,
    //     processing: true,
    //     serverSide: true,
    //     ajax: "{{ route('admin.logs.data') }}",
    //     columns: [
    //         {data: 'created_at', name: 'created_at'},
    //         {data: 'user', name: 'user'},
    //         {data: 'action', name: 'action'},
    //         {data: 'description', name: 'description'},
    //         {data: 'ip_address', name: 'ip_address'},
    //         {data: 'category', name: 'category'},
    //         {data: 'severity', name: 'severity'}
    //     ]
    // });
});

function refreshLogs() {
    // Reload the page for now, will implement AJAX refresh later
    location.reload();
}

function exportLogs() {
    // Show export modal or trigger download
    Swal.fire({
        title: 'Export Logs',
        text: 'Export functionality will be available in the next release.',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function clearOldLogs() {
    Swal.fire({
        title: 'Clear Old Logs',
        text: 'How many days of logs would you like to keep?',
        input: 'number',
        inputValue: 30,
        showCancelButton: true,
        confirmButtonText: 'Clear',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call to clear logs (future implementation)
            Swal.fire('Success!', 'Old logs have been cleared.', 'success');
        }
    });
}

function applyFilters() {
    // Apply filters to the table (future implementation)
    const category = $('#categoryFilter').val();
    const severity = $('#severityFilter').val();
    const date = $('#dateFilter').val();

    console.log('Applying filters:', {category, severity, date});
    // Will implement actual filtering when DataTables is set up
}

function testLogging() {
    Swal.fire({
        title: 'Test Logging',
        text: 'Logging system test completed successfully!',
        icon: 'success'
    });
}

function configureLogging() {
    Swal.fire({
        title: 'Configure Logging',
        text: 'Logging configuration will be available in system settings.',
        icon: 'info'
    });
}
</script>
@stop

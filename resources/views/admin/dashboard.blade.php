@extends('adminlte::page')

@section('title', 'Dashboard | Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Total Projects Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_projects'] }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Active Projects Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['active_projects'] }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <a href="{{ route('admin.home.tabs', ['tab' => 'active']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Completed Projects Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['completed_projects'] }}</h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('admin.home.tabs', ['tab' => 'completed']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Consultants Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_consultants'] }}</h3>
                    <p>Consultants</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('admin.consultants.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Projects Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Projects</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Property ID</th>
                                <th>Owner Name</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_projects'] as $project)
                                <tr>
                                    <td>{{ $project->property_id }}</td>
                                    <td>{{ $project->owner_name }}</td>
                                    <td>{{ $project->property_address }}</td>
                                    <td>
                                        <span class="badge badge-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($project->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>{{ $project->created_at?->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No projects found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('admin.hb837.index') }}" class="btn btn-sm btn-info float-left">View All Projects</a>
                    <a href="{{ route('admin.hb837.create') }}" class="btn btn-sm btn-success float-right">Add New Project</a>
                </div>
            </div>
        </div>

        <!-- Recent Backups -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Backups</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($stats['recent_backups'] as $backup)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $backup->name }}</strong><br>
                                        <small class="text-muted">{{ $backup->created_at?->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge badge-{{ $backup->status == 'completed' ? 'success' : ($backup->status == 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($backup->status) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">No backups found</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.services.backup') }}" class="btn btn-sm btn-primary btn-block">Manage Backups</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.hb837.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Add New Project
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.hb837.import') }}" class="btn btn-info btn-block">
                                <i class="fas fa-upload"></i> Import Data
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.services.backup') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-database"></i> Create Backup
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.hb837.report') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-file-pdf"></i> Generate Report
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
        .small-box .icon {
            top: 10px;
        }
        
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        .badge {
            font-size: 0.75em;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('AdminLTE Dashboard loaded successfully!');
        
        // Add any dashboard-specific JavaScript here
        $(document).ready(function() {
            // Initialize any dashboard widgets or charts
            console.log('Dashboard statistics:', @json($stats));
        });
    </script>
@stop

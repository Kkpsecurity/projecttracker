@extends('adminlte::page')

@section('title', $clientName . ' - Macro Client Details')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-building text-primary"></i> 
            {{ $clientName }}
        </h1>
        <div>
            <a href="{{ route('admin.plot-clients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Clients
            </a>
            <a href="{{ route('admin.maps.index') }}?macro_client={{ urlencode($clientName) }}" class="btn btn-success">
                <i class="fas fa-map"></i> View on Map
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Client Statistics --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['plots_count'] }}</h3>
                    <p>Total Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['projects_count'] }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['completed_projects'] }}</h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($stats['total_value'], 0) }}</h3>
                    <p>Total Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects & Plots --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Client Plots
                    </h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="plots-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Plot Name</th>
                                <th>Address</th>
                                <th>Coordinates</th>
                                <th>Project</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plots as $plot)
                                <tr>
                                    <td>
                                        {{ $plot->plot_name ?: 'Unnamed Plot' }}
                                        @if(!$plot->plot_name)
                                            <small class="text-muted d-block">ID: {{ $plot->id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plot->address)
                                            {{ $plot->address->street_address }}<br>
                                            <small class="text-muted">{{ $plot->address->city }}, {{ $plot->address->state }} {{ $plot->address->zip_code }}</small>
                                        @else
                                            <em class="text-muted">No address</em>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                                            <span class="badge badge-info">
                                                {{ number_format($plot->coordinates_latitude, 4) }}, 
                                                {{ number_format($plot->coordinates_longitude, 4) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Not mapped</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plot->hb837)
                                            <a href="{{ route('admin.hb837.show', $plot->hb837->id) }}" class="text-primary">
                                                {{ $plot->hb837->property_name }}
                                            </a>
                                            <br><small class="text-muted">{{ ucfirst($plot->hb837->report_status) }}</small>
                                        @else
                                            <em class="text-muted">No project</em>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plot->hb837)
                                            @php
                                                $statusClasses = [
                                                    'not-started' => 'secondary',
                                                    'in-progress' => 'warning', 
                                                    'underway' => 'info',
                                                    'in-review' => 'primary',
                                                    'completed' => 'success'
                                                ];
                                                $statusClass = $statusClasses[$plot->hb837->report_status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">
                                                {{ ucfirst(str_replace('-', ' ', $plot->hb837->report_status)) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.plots.show', $plot->id) }}" class="btn btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.plots.edit', $plot->id) }}" class="btn btn-warning" title="Edit Plot">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                                                <a href="{{ route('admin.maps.index') }}?plot={{ $plot->id }}" class="btn btn-success" title="View on Map">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Projects Summary --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> Projects
                    </h3>
                </div>
                <div class="card-body">
                    @foreach($client as $project)
                        <div class="project-item border-bottom pb-2 mb-2">
                            <h6>
                                <a href="{{ route('admin.hb837.show', $project->id) }}" class="text-primary">
                                    {{ $project->property_name }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    {{ $project->plots->count() }} plots
                                </small>
                                @php
                                    $statusClasses = [
                                        'not-started' => 'secondary',
                                        'in-progress' => 'warning', 
                                        'underway' => 'info',
                                        'in-review' => 'primary',
                                        'completed' => 'success'
                                    ];
                                    $statusClass = $statusClasses[$project->report_status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusClass }} badge-sm">
                                    {{ ucfirst(str_replace('-', ' ', $project->report_status)) }}
                                </span>
                            </div>
                            @if($project->quoted_price)
                                <small class="text-muted d-block">
                                    Value: ${{ number_format($project->quoted_price, 0) }}
                                </small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.hb837.create') }}?macro_client={{ urlencode($clientName) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus"></i> New Project
                    </a>
                    <a href="{{ route('admin.plots.create') }}?macro_client={{ urlencode($clientName) }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-map-marker-alt"></i> Add Plot
                    </a>
                    <a href="{{ route('admin.maps.index') }}?macro_client={{ urlencode($clientName) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-map"></i> View All on Map
                    </a>
                    <a href="{{ route('admin.plot-groups-management.index') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-layer-group"></i> Assign More Plots
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .project-item:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        .badge-sm {
            font-size: 0.75em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#plots-table').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [5] } // Actions column
                ]
            });
        });
    </script>
@stop

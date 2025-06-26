@extends('layouts.admin')

@section('title', 'HB837 Management')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">HB837 Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">HB837</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    @php
        $active_tab = Request()->segment(4) ?? 'Active';
        $tabs = [
            'Active' => [
                'icon' => 'fas fa-play-circle',
                'name' => 'Active',
                'class' => 'btn-success'
            ],
            'Quoted' => [
                'icon' => 'fas fa-quote-right',
                'name' => 'Quoted',
                'class' => 'btn-warning'
            ],
            'Completed' => [
                'icon' => 'fas fa-check-circle',
                'name' => 'Completed',
                'class' => 'btn-primary'
            ],
            'Closed' => [
                'icon' => 'fas fa-times-circle',
                'name' => 'Closed',
                'class' => 'btn-secondary'
            ],
        ];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="{{ $tabs[$active_tab]['icon'] }}"></i>
                                {{ $tabs[$active_tab]['name'] }} Records
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.hb837.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add New Record
                                </a>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#export_import_modal">
                                    <i class="fas fa-exchange-alt"></i> Import/Export
                                </button>
                                <a href="{{ route('admin.hb837.backup.dashboard') }}" class="btn btn-warning">
                                    <i class="fas fa-database"></i> Backup
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($tabs as $key => $tab)
                                <li class="nav-item">
                                    <a href="{{ route('admin.hb837.tabs', ['tab' => $key]) }}" 
                                       class="nav-link {{ $active_tab == $key ? 'active' : '' }}">
                                        <i class="{{ $tab['icon'] }}"></i>
                                        {{ $tab['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Records Table -->
                    <div class="table-responsive-modern">
                        <table class="table table-modern table-compact" id="hb837-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Property ID</th>
                                    <th>Property Name</th>
                                    <th>Owner Name</th>
                                    <th>Address</th>
                                    <th style="width: 120px;">Consultant</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 100px;">Crime Risk</th>
                                    <th style="width: 120px;">Updated</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collection as $record)
                                    <tr>
                                        <td><span class="table-id">#{{ $record->id }}</span></td>
                                        <td><strong>{{ $record->property_name }}</strong></td>
                                        <td>{{ $record->owner_name }}</td>
                                        <td>
                                            <div class="table-address">
                                                {{ $record->address }}<br>
                                                <small class="text-muted">{{ $record->city }}, {{ $record->state }} {{ $record->zip }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($record->consultant)
                                                <span class="table-badge badge-info">{{ $record->consultant->name }}</span>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="table-badge badge-{{ strtolower($record->report_status) === 'active' ? 'success' : (strtolower($record->report_status) === 'quoted' ? 'warning' : (strtolower($record->report_status) === 'completed' ? 'info' : 'secondary')) }}">
                                                {{ $record->report_status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($record->securitygauge_crime_risk)
                                                <span class="risk-{{ strtolower($record->securitygauge_crime_risk) }}">
                                                    <i class="fas fa-circle"></i>
                                                    {{ ucfirst($record->securitygauge_crime_risk) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $record->updated_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.hb837.edit', $record->id) }}" 
                                                   class="btn btn-info btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.hb837.report', $record->id) }}" 
                                                   class="btn btn-success btn-sm" title="Report">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <form action="{{ route('admin.hb837.destroy', $record->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <br>
                                            No HB837 records found for this status.
                                            <br>
                                            <a href="{{ route('admin.hb837.create') }}" class="btn btn-success btn-sm mt-2">
                                                <i class="fas fa-plus"></i> Add First Record
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(isset($collection) && method_exists($collection, 'hasPages') && $collection->hasPages())
                    <div class="card-footer">
                        {{ $collection->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mt-3">
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Records</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-play-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active</span>
                    <span class="info-box-number">{{ $stats['active'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-quote-right"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quoted</span>
                    <span class="info-box-number">{{ $stats['quoted'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number">{{ $stats['completed'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the existing modals -->
    @include('partials.modals.export_import_modal')
@stop

@section('custom_css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .nav-tabs-custom {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs .nav-link {
            border-radius: 0;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #007bff;
            color: #007bff;
        }
        
        .nav-tabs .nav-link.active {
            background-color: transparent;
            color: #007bff;
            border-bottom-color: #007bff;
            font-weight: bold;
        }
        
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .btn-group-sm > .btn {
            margin: 0 1px;
        }
        
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #28a745; color: white; }
        .status-quoted { background-color: #ffc107; color: #212529; }
        .status-completed { background-color: #007bff; color: white; }
        .status-closed { background-color: #dc3545; color: white; }
        
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
    </style>
@stop

@section('custom_js')
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with basic features
            $('#hb837-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": false, // Disable DataTable pagination since we're using Laravel pagination
                "order": [[ 7, "desc" ]], // Sort by updated column by default
                "columnDefs": [
                    { "orderable": false, "targets": 8 } // Disable ordering on actions column
                ]
            });
            
            // Confirm deletion
            $(document).on('click', '.btn-delete', function(e) {
                if (!confirm('Are you sure you want to delete this HB837 record? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Auto-refresh every 5 minutes
            setInterval(function() {
                if (!$('.modal').hasClass('show')) { // Don't refresh if modal is open
                    window.location.reload();
                }
            }, 300000); // 5 minutes
        });
    </script>
@stop

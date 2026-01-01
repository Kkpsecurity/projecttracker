@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'HB837 Management - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-shield-alt"></i> HB837 Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">HB837 Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Simplified Header Actions -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm">
                <div>
                    <h5 class="mb-0">HB837 Properties</h5>
                    <small class="text-muted">{{ $tabCounts['all'] ?? 0 }} total properties</small>
                </div>
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group btn-group-sm mr-2" role="group">
                        <a href="{{ route('admin.hb837.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> New
                        </a>
                        <a href="{{ route('admin.hb837.smart-import.show') }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import
                        </a>
                        <a href="{{ route('admin.hb837.export') }}" class="btn btn-info">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                    <!-- Status Filter Tabs -->
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn {{ $tab == 'all' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="changeTab('all')">
                            All ({{ $tabCounts['all'] ?? 0 }})
                        </button>
                        <button type="button" class="btn {{ $tab == 'active' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="changeTab('active')">
                            Active ({{ $tabCounts['active'] ?? 0 }})
                        </button>
                        <button type="button" class="btn {{ $tab == 'quoted' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="changeTab('quoted')">
                            Quoted ({{ $tabCounts['quoted'] ?? 0 }})
                        </button>
                        <button type="button" class="btn {{ $tab == 'completed' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="changeTab('completed')">
                            Completed ({{ $tabCounts['completed'] ?? 0 }})
                        </button>
                        <button type="button" class="btn {{ $tab == 'closed' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="changeTab('closed')">
                            Closed ({{ $tabCounts['closed'] ?? 0 }})
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Streamlined Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="hb837-table-{{ $tab }}" class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="2%" class="text-center border-0">
                                        <input type="checkbox" id="select-all-{{ $tab }}" class="form-check-input">
                                    </th>
                                    <th width="25%" class="border-0">Property</th>
                                    <th width="15%" class="border-0">Location</th>
                                    <th width="15%" class="border-0">Client</th>
                                    <th width="12%" class="border-0">Consultant</th>
                                    <th width="10%" class="border-0">Inspection</th>
                                    <th width="8%" class="border-0">Status</th>
                                    <th width="10%" class="border-0">Price</th>
                                    <th width="3%" class="text-center border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bulk Actions</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bulk-actions-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bulk-action">Action</label>
                        <select id="bulk-action" name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="status_update">Update Status</option>
                            <option value="consultant_assign">Assign Consultant</option>
                            <option value="delete">Delete Records</option>
                        </select>
                    </div>

                    <div class="form-group" id="status-group" style="display: none;">
                        <label for="bulk-status">New Status</label>
                        <select id="bulk-status" name="bulk_status" class="form-control">
                            <option value="not-started">Not Started</option>
                            <option value="in-progress">In Progress</option>
                            <option value="in-review">In Review</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group" id="consultant-group" style="display: none;">
                        <label for="bulk-consultant">Consultant</label>
                        <select id="bulk-consultant" name="bulk_consultant_id" class="form-control">
                            <option value="">Unassigned</option>
                            <!-- Populated via AJAX -->
                        </select>
                    </div>

                    <div id="selected-count" class="alert alert-info">
                        No records selected
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Execute Action</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Clean table styling */
.table-sm th {
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.table-sm td {
    padding: 0.5rem;
    vertical-align: middle;
    font-size: 0.875rem;
}

/* Status badges */
.badge-status {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.status-not-started { 
    background-color: #dc3545; 
    color: white; 
}

.status-underway { 
    background-color: #ffc107; 
    color: #212529; 
}

.status-in-review { 
    background-color: #17a2b8; 
    color: white; 
}

.status-completed { 
    background-color: #28a745; 
    color: white; 
}

/* Compact action buttons */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Clean card styling */
.card {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Responsive table improvements */
.table-responsive {
    border: none;
}

@media (max-width: 768px) {
    .btn-toolbar .btn-group {
        margin-bottom: 0.5rem;
    }
    
    .table-sm th,
    .table-sm td {
        padding: 0.25rem;
        font-size: 0.75rem;
    }
}
</style>
@stop

@section('js')
<script>
// Global variables
var table;
var currentTab = '{{ $tab }}';

$(document).ready(function() {
    console.log('Initializing HB837 Dashboard...');
    
    // Initialize DataTable
    initializeDataTable();
    
    // Handle tab changes
    window.changeTab = function(newTab) {
        if (newTab !== currentTab) {
            // Update URL and reload page with new tab
            const url = new URL(window.location);
            url.searchParams.set('tab', newTab);
            window.location.href = url.toString();
        }
    };
    
    // Handle select all checkbox
    $('#select-all-{{ $tab }}').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('tbody input[type="checkbox"]').prop('checked', isChecked);
    });
});

function initializeDataTable() {
    var tableId = '#hb837-table-{{ $tab }}';
    
    table = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.hb837.data') }}",
            data: function(d) {
                d.tab = currentTab;
                return d;
            }
        },
        columns: [
            { 
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" class="form-check-input" value="' + data + '">';
                }
            },
            { 
                data: 'property_name',
                render: function(data, type, row) {
                    var html = '<div class="font-weight-medium">' + (data || 'N/A') + '</div>';
                    if (row.address) {
                        html += '<small class="text-muted">' + row.address + '</small>';
                    }
                    return html;
                }
            },
            { 
                data: 'county',
                render: function(data, type, row) {
                    var location = [];
                    if (data) location.push(data);
                    if (row.state) location.push(row.state);
                    return location.length > 0 ? location.join(', ') : 'N/A';
                }
            },
            { 
                data: 'macro_client',
                render: function(data) {
                    return data ? '<span class="text-truncate" title="' + data + '">' + data + '</span>' : 'N/A';
                }
            },
            { 
                data: 'consultant_name',
                render: function(data) {
                    return data || 'Unassigned';
                }
            },
            { 
                data: 'scheduled_date_of_inspection',
                render: function(data) {
                    if (data) {
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US', { 
                            month: 'short', 
                            day: 'numeric',
                            year: 'numeric'
                        });
                    }
                    return 'Not Set';
                }
            },
            { 
                data: 'report_status',
                render: function(data) {
                    if (!data) return '<span class="badge badge-secondary badge-status">Unknown</span>';
                    
                    var statusClass = 'badge-secondary';
                    var statusText = data.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    
                    switch(data) {
                        case 'not-started':
                            statusClass = 'badge-danger';
                            break;
                        case 'underway':
                            statusClass = 'badge-warning';
                            break;
                        case 'in-review':
                            statusClass = 'badge-info';
                            break;
                        case 'completed':
                            statusClass = 'badge-success';
                            break;
                    }
                    
                    return '<span class="badge ' + statusClass + ' badge-status">' + statusText + '</span>';
                }
            },
            { 
                data: 'quoted_price',
                render: function(data) {
                    if (data && data > 0) {
                        return '$' + parseFloat(data).toLocaleString('en-US', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    }
                    return 'Not Set';
                }
            },
            { 
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<div class="btn-group btn-group-sm">' +
                           '<a href="/admin/hb837/' + data + '" class="btn btn-info btn-sm" title="View">' +
                           '<i class="fas fa-eye"></i></a>' +
                           '<a href="/admin/hb837/' + data + '/edit" class="btn btn-primary btn-sm" title="Edit">' +
                           '<i class="fas fa-edit"></i></a>' +
                           '</div>';
                }
            }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: "Loading properties...",
            emptyTable: "No properties found for this status",
            zeroRecords: "No matching properties found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Update select all checkbox state
            var totalRows = this.api().rows().count();
            var checkedRows = $('tbody input[type="checkbox"]:checked').length;
            
            if (totalRows === 0) {
                $('#select-all-{{ $tab }}').prop('indeterminate', false).prop('checked', false);
            } else if (checkedRows === 0) {
                $('#select-all-{{ $tab }}').prop('indeterminate', false).prop('checked', false);
            } else if (checkedRows === totalRows) {
                $('#select-all-{{ $tab }}').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#select-all-{{ $tab }}').prop('indeterminate', true);
            }
        }
    });
}
</script>
@stop

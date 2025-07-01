@extends('adminlte::page')

@section('title', 'HB837 Property Management')

@section('content_header')
    <x-breadcrumb :items="[
        ['title' => 'HB837 Module', 'url' => route('modules.hb837.index'), 'icon' => 'fas fa-building'],
        ['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt']
    ]" />

    <div class="d-flex justify-content-between align-items-center">
        <h1>HB837 Property Management</h1>
        <div class="btn-group">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Main Dashboard
            </a>
            <a href="{{ route('modules.hb837.import.index') }}" class="btn btn-primary">
                <i class="fas fa-upload"></i> Import Data
            </a>
            <button type="button" class="btn btn-success" onclick="exportData()">
                <i class="fas fa-download"></i> Export Data
            </button>
            <button type="button" class="btn btn-warning" onclick="createBackup()">
                <i class="fas fa-save"></i> Create Backup
            </button>
        </div>
    </div>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $statistics['active'] }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $statistics['completed'] }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $statistics['quoted'] }}</h3>
                    <p>Quoted</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $statistics['overdue'] ?? 0 }}</h3>
                    <p>Overdue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-upload fa-3x text-primary mb-3"></i>
                                    <h5>3-Phase Import</h5>
                                    <p class="text-muted">Upload, map fields, and validate your data in 3 easy steps.</p>
                                    <a href="{{ route('modules.hb837.import.index') }}" class="btn btn-primary">Start Import</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-3x text-success mb-3"></i>
                                    <h5>Export Data</h5>
                                    <p class="text-muted">Export filtered data to Excel, CSV, or PDF formats.</p>
                                    <button type="button" class="btn btn-success" onclick="exportData()">Export Data</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-save fa-3x text-warning mb-3"></i>
                                    <h5>Backup System</h5>
                                    <p class="text-muted">Create and manage backups of your property data.</p>
                                    <button type="button" class="btn btn-warning" onclick="createBackup()">Create Backup</button>
                                </div>
                            </div>
                        </div>

                        <!-- Import History Section -->
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-history fa-3x text-warning mb-3"></i>
                                    <h5>Import History</h5>
                                    <p class="text-muted">View previous imports and their status.</p>
                                    <button type="button" class="btn btn-warning" onclick="showImportHistory()">View History</button>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Tools -->
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-info mb-3"></i>
                                    <h5>Data Validation</h5>
                                    <p class="text-muted">Validate data integrity and check for duplicates.</p>
                                    <button type="button" class="btn btn-info" onclick="runDataValidation()">Run Validation</button>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Operations -->
                        <div class="col-md-4">
                            <div class="card border-dark">
                                <div class="card-body text-center">
                                    <i class="fas fa-cogs fa-3x text-dark mb-3"></i>
                                    <h5>Bulk Operations</h5>
                                    <p class="text-muted">Update multiple records at once.</p>
                                    <button type="button" class="btn btn-dark" onclick="showBulkOperations()">Bulk Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" onclick="refreshStatistics()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="activity-content">
                        <p class="text-center text-muted">Loading recent activity...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Management Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table"></i> HB837 Records Management
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshTable()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showAdvancedFilters()">
                            <i class="fas fa-filter"></i> Advanced Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Advanced Filters Panel (Initially Hidden) -->
                    <div id="advancedFiltersPanel" class="row mb-3" style="display: none;">
                        <div class="col-md-3">
                            <label for="filterStatus">Report Status</label>
                            <select id="filterStatus" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="quoted">Quoted</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterConsultant">Consultant</label>
                            <select id="filterConsultant" class="form-control">
                                <option value="">All Consultants</option>
                                @foreach($consultants as $consultant)
                                    <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterDateFrom">Date From</label>
                            <input type="date" id="filterDateFrom" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="filterDateTo">Date To</label>
                            <input type="date" id="filterDateTo" class="form-control">
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="hb837Table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Property Address</th>
                                    <th>Report Status</th>
                                    <th>Consultant</th>
                                    <th>Date Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Export HB837 Data</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Export Format</label>
                                    <select name="format" class="form-control" required>
                                        <option value="excel">Excel (.xlsx)</option>
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF Report</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <select name="date_range" class="form-control">
                                        <option value="all">All Records</option>
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="quarter">This Quarter</option>
                                        <option value="year">This Year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="customDateRange" class="row" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Filter by Status</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="in-progress" class="form-check-input" checked>
                                        <label class="form-check-label">In Progress</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="completed" class="form-check-input" checked>
                                        <label class="form-check-label">Completed</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="quoted" class="form-check-input" checked>
                                        <label class="form-check-label">Quoted</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="pending" class="form-check-input" checked>
                                        <label class="form-check-label">Pending</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Include Consultant Details</label>
                            <div class="form-check">
                                <input type="checkbox" name="include_consultant" class="form-check-input" checked>
                                <label class="form-check-label">Include consultant information in export</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="executeExport()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
$(document).ready(function() {
    refreshStatistics();
    initializeDataTable();
});

function exportData() {
    // Show export modal
    $('#exportModal').modal('show');
}

function createBackup() {
    if (confirm('Create a backup of all HB837 data? This may take a few minutes.')) {
        $.ajax({
            url: '{{ route("modules.hb837.export.backup") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Backup created successfully!');
                    // Offer download
                    if (response.download_url) {
                        window.open(response.download_url, '_blank');
                    }
                } else {
                    toastr.error('Backup failed: ' + response.error);
                }
            },
            error: function() {
                toastr.error('Backup request failed');
            }
        });
    }
}

function refreshStatistics() {
    $.ajax({
        url: '{{ route("modules.hb837.statistics") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateStatistics(response.statistics);
            }
        },
        error: function() {
            console.log('Failed to refresh statistics');
        }
    });
}

function updateStatistics(stats) {
    // Update cards if needed
    // This would update the statistics in real-time
}

function initializeDataTable() {
    $('#hb837Table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("modules.hb837.data") }}',
            type: 'GET',
            data: function(d) {
                // Add additional parameters for filtering
                d.status = $('#filterStatus').val();
                d.consultant = $('#filterConsultant').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { data: 'property_address', name: 'property_address' },
            { data: 'report_status', name: 'report_status' },
            { data: 'consultant.name', name: 'consultant.name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[3, 'desc']], // Order by date created descending
        responsive: true,
        language: {
            // Custom language settings
        }
    });
}

function refreshTable() {
    $('#hb837Table').DataTable().ajax.reload();
}

function showAdvancedFilters() {
    $('#advancedFiltersPanel').toggle();
}

// Export functionality
function executeExport() {
    const formData = new FormData(document.getElementById('exportForm'));

    $.ajax({
        url: '{{ route("modules.hb837.export.execute") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#exportModal').modal('hide');
                toastr.success('Export completed successfully!');
                if (response.download_url) {
                    window.open(response.download_url, '_blank');
                }
            } else {
                toastr.error('Export failed: ' + response.error);
            }
        },
        error: function() {
            toastr.error('Export request failed');
        }
    });
}
</script>
@stop

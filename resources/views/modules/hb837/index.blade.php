@extends('adminlte::page')

@section('title', 'HB837 Property Management')

@section('content_header')
    <x-breadcrumb :items="[
        ['title' => 'HB837 Module', 'url' => route('admin.hb837.index'), 'icon' => 'fas fa-building'],
        ['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
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
        @include('modules.hb837.partials.statistics', ['statistics' => $statistics])
    </div>

    <!-- 30-Day Overdue Alert -->
    @if (isset($statistics['thirty_day_overdue']) && $statistics['thirty_day_overdue'] > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger thirty-day-overdue-bg">
                    <h5 class="text-shadow-sm">
                        <i class="fas fa-exclamation-circle"></i>
                        Critical: {{ $statistics['thirty_day_overdue'] }} Records Over 30 Days Old
                    </h5>
                    <p class="mb-2">The following records have been incomplete for more than 30 days and require immediate
                        attention:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Impact:</strong>
                            <ul class="mb-0">
                                <li>Delayed project completion</li>
                                <li>Customer satisfaction impact</li>
                                <li>Revenue recognition delays</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Recommended Actions:</strong>
                            <ul class="mb-0">
                                <li>Review consultant assignments</li>
                                <li>Escalate to project managers</li>
                                <li>Contact property owners</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-dark btn-sm" onclick="filterThirtyDayOverdue()">
                            <i class="fas fa-filter"></i> Show 30+ Day Records
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="exportOverdueReport()">
                            <i class="fas fa-file-pdf"></i> Export Overdue Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                                    <a href="{{ route('modules.hb837.import.index') }}" class="btn btn-primary">Start
                                        Import</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-3x text-success mb-3"></i>
                                    <h5>Export Data</h5>
                                    <p class="text-muted">Export filtered data to Excel, CSV, or PDF formats.</p>
                                    <button type="button" class="btn btn-success" onclick="exportData()">Export
                                        Data</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-save fa-3x text-warning mb-3"></i>
                                    <h5>Backup System</h5>
                                    <p class="text-muted">Create and manage backups of your property data.</p>
                                    <button type="button" class="btn btn-warning" onclick="createBackup()">Create
                                        Backup</button>
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
                                    <button type="button" class="btn btn-warning" onclick="showImportHistory()">View
                                        History</button>
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
                                    <button type="button" class="btn btn-info" onclick="runDataValidation()">Run
                                        Validation</button>
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
                                    <button type="button" class="btn btn-dark" onclick="showBulkOperations()">Bulk
                                        Update</button>
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
                                @foreach ($consultants as $consultant)
                                    <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterDateFrom">Date From</label>
                            <input type="date" id="filterDateFrom" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="filterDateTo">Date To</label>
                            <input type="date" id="filterDateTo" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" id="filterThirtyDayOverdue" class="form-check-input">
                                <label class="form-check-label" for="filterThirtyDayOverdue">
                                    <small>Show only 30+ day overdue</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="hb837Table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">
                                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                                    </th>
                                    <th>Status</th>
                                    <th>Property Name</th>
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
                    <h4 class="modal-title text-shadow-sm">Export HB837 Data</h4>
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
                                        <input type="checkbox" name="statuses[]" value="in-progress"
                                            class="form-check-input" checked>
                                        <label class="form-check-label">In Progress</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="completed"
                                            class="form-check-input" checked>
                                        <label class="form-check-label">Completed</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="quoted" class="form-check-input"
                                            checked>
                                        <label class="form-check-label">Quoted</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="statuses[]" value="pending"
                                            class="form-check-input" checked>
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
        // Add event listeners for filter changes
        $(document).ready(function() {
            refreshStatistics();
            initializeDataTable();

            // Refresh table when filters change
            $('#filterStatus, #filterConsultant, #filterDateFrom, #filterDateTo, #filterThirtyDayOverdue').on(
                'change',
                function() {
                    $('#hb837Table').DataTable().ajax.reload();
                });
        });

        function exportData() {
            // Show export modal
            $('#exportModal').modal('show');
        }

        function createBackup() {
            if (confirm('Create a backup of all HB837 data? This may take a few minutes.')) {
                $.ajax({
                    url: '{{ route('admin.hb837.export') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Backup created successfully!');
                            }
                            // Offer download
                            if (response.download_url) {
                                window.open(response.download_url, '_blank');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Backup failed: ' + response.error);
                            }
                        }
                    },
                    error: function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Backup request failed');
                        }
                    }
                });
            }
        }

        function refreshStatistics() {
            $.ajax({
                url: '{{ route('admin.hb837.stats') }}',
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
                    url: '{{ route('admin.hb837.data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Add additional parameters for filtering
                        d.status = $('#filterStatus').val();
                        d.consultant = $('#filterConsultant').val();
                        d.date_from = $('#filterDateFrom').val();
                        d.date_to = $('#filterDateTo').val();
                        d.show_thirty_day_overdue = $('#filterThirtyDayOverdue').is(':checked');
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '30px'
                    },
                    {
                        data: 'overdue_status_badge',
                        name: 'overdue_status_badge',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '100px'
                    },
                    {
                        data: 'property_name',
                        name: 'property_name'
                    },
                    {
                        data: 'report_status',
                        name: 'report_status'
                    },
                    {
                        data: 'assigned_consultant_id',
                        name: 'assigned_consultant_id'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
                ], // Order by date created descending (index 5 now due to checkbox column)
                responsive: true,
                rowCallback: function(row, data) {
                    // Apply 30-day overdue styling to entire row
                    if (data.is_thirty_day_overdue) {
                        $(row).addClass('thirty-day-overdue-row thirty-day-overdue-bg');
                        $(row).find('td:eq(2)').addClass(
                            'thirty-day-overdue'); // Property name (index 2 due to checkbox)
                    } else if (data.is_overdue) {
                        $(row).addClass('table-warning'); // Regular overdue styling
                    }

                    // Add days since created info to property name cell
                    if (data.days_since_created > 30) {
                        const nameCell = $(row).find('td:eq(2)'); // Property name (index 2 due to checkbox)
                        const currentText = nameCell.html();
                        nameCell.html(currentText + '<br><small class="text-muted">(' + data
                            .days_since_created + ' days old)</small>');
                    }
                },
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

        function filterThirtyDayOverdue() {
            // Show advanced filters
            $('#advancedFiltersPanel').show();
            // Check the 30-day overdue filter
            $('#filterThirtyDayOverdue').prop('checked', true);
            // Refresh the table
            $('#hb837Table').DataTable().ajax.reload();
        }

        function exportOverdueReport() {
            // Show export modal with 30-day overdue pre-selected
            $('#exportModal').modal('show');
            // You could pre-configure the export form here
        }

        // Add event listeners for filter changes
        $(document).ready(function() {
            refreshStatistics();
            initializeDataTable();

            // Refresh table when filters change
            $('#filterStatus, #filterConsultant, #filterDateFrom, #filterDateTo, #filterThirtyDayOverdue').on(
                'change',
                function() {
                    $('#hb837Table').DataTable().ajax.reload();
                });
        });

        // Checkbox functionality
        function initializeCheckboxes() {
            // Handle "select all" checkbox
            $('#select-all-checkbox').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.bulk-checkbox').prop('checked', isChecked);
                updateBulkActionButtons();
            });

            // Handle individual checkboxes
            $(document).on('change', '.bulk-checkbox', function() {
                updateSelectAllCheckbox();
                updateBulkActionButtons();
            });

            // Update select-all checkbox state based on individual checkboxes
            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.bulk-checkbox').length;
                const checkedCheckboxes = $('.bulk-checkbox:checked').length;

                $('#select-all-checkbox').prop('checked', totalCheckboxes > 0 && checkedCheckboxes === totalCheckboxes);
                $('#select-all-checkbox').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes <
                    totalCheckboxes);
            }

            // Update bulk action buttons visibility
            function updateBulkActionButtons() {
                const checkedCheckboxes = $('.bulk-checkbox:checked').length;
                $('.bulk-actions').toggle(checkedCheckboxes > 0);
                $('.bulk-action-count').text(checkedCheckboxes);
            }
        }

        // Initialize everything when page loads
        $(document).ready(function() {
            refreshStatistics();
            initializeDataTable();
            initializeCheckboxes();

            // Show/hide custom date range in export modal
            $('select[name="date_range"]').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#customDateRange').show();
                } else {
                    $('#customDateRange').hide();
                }
            });

            // Initialize bulk action buttons (if any)
            updateBulkActionButtons();
        });

        function executeExport() {
            const formData = new FormData(document.getElementById('exportForm'));

            $.ajax({
                url: '{{ route('admin.hb837.export') }}',
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
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Export completed successfully!');
                        }
                        if (response.download_url) {
                            window.open(response.download_url, '_blank');
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Export failed: ' + response.error);
                        }
                    }
                },
                error: function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Export request failed');
                    }
                }
            });
        }
    </script>
@stop

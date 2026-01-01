@extends('adminlte::page')

@section('title', 'Macro Client Plots - Project Tracker')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-building"></i> Macro Client Plots</h1>
        <div>
            <a href="{{ route('admin.plot-groups-management.index') }}" class="btn btn-info">
                <i class="fas fa-layer-group"></i> Group Plots
            </a>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-secondary">
                <i class="fas fa-map"></i> Google Maps
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_macro_clients'] ?? 0 }}</h3>
                    <p>Active Macro Clients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_assigned_plots'] ?? 0 }}</h3>
                    <p>Assigned Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_projects'] ?? 0 }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($stats['total_value'] ?? 0, 0) }}</h3>
                    <p>Total Project Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-building"></i> Assigned Plot Management (Read-Only)
            </h3>
            <div class="card-tools">
                <span class="badge badge-info">View Only</span>
            </div>
        </div>
        <div class="card-body">
            {{-- Client Filter Row --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="client-filter">Filter by Macro Client:</label>
                        <select id="client-filter" class="form-control">
                            <option value="">All Clients</option>
                            @foreach($macroClients ?? [] as $client)
                                <option value="{{ $client->macro_client }}">
                                    {{ $client->macro_client }} ({{ $client->plots_count }} plots)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Read-Only Actions:</label>
                        <div class="btn-group d-block">
                            <button type="button" class="btn btn-outline-info btn-sm" id="export-client-data-btn">
                                <i class="fas fa-download"></i> Export Client Data
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="view-client-details-btn" disabled>
                                <i class="fas fa-eye"></i> View Client Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Navigation --}}
            <ul class="nav nav-tabs" id="macroClientTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="client-table-tab" data-toggle="tab" href="#client-table-view" role="tab">
                        <i class="fas fa-table"></i> Table View
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="client-map-tab" data-toggle="tab" href="#client-map-view" role="tab">
                        <i class="fas fa-map"></i> Map View
                    </a>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content" id="macroClientTabContent">
                {{-- Table View Tab --}}
                <div class="tab-pane fade show active" id="client-table-view" role="tabpanel">
                    <div class="mt-3">
                        <div class="table-responsive">
                            <table id="macro-client-plots-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Plot Name</th>
                                        <th>Address</th>
                                        <th>Macro Client</th>
                                        <th>Project Name</th>
                                        <th>Status</th>
                                        <th>Value</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data will be loaded via AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Map View Tab --}}
                <div class="tab-pane fade" id="client-map-view" role="tabpanel">
                    <div class="mt-3">
                        <div id="macro-client-plots-map" style="height: 600px; width: 100%;"></div>
                        <div class="mt-2">
                            <div class="row">
                                <div class="col-md-8">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Displaying plots assigned to macro clients. Click markers to view details.
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" id="fit-map-btn">
                                            <i class="fas fa-expand-arrows-alt"></i> Fit All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="cluster-toggle-btn">
                                            <i class="fas fa-layer-group"></i> Cluster
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Client Details Modal (Read-Only) --}}
    <div class="modal fade" id="clientDetailsModal" tabindex="-1" role="dialog" aria-labelledby="clientDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientDetailsModalLabel">
                        <i class="fas fa-building"></i> Macro Client Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="client-details-content">
                        {{-- Content will be loaded dynamically --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" id="export-client-plots-btn">
                        <i class="fas fa-download"></i> Export Client Plots
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt"></i> Quick Actions
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.maps.index') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-map"></i><br>
                        <strong>Interactive Map</strong><br>
                        <small>View all assigned plots on map</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.plot-groups-management.index') }}" class="btn btn-outline-warning btn-block">
                        <i class="fas fa-layer-group"></i><br>
                        <strong>Group Plots</strong><br>
                        <small>Manage unassigned plots</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-info btn-block" id="generate-report-btn">
                        <i class="fas fa-chart-bar"></i><br>
                        <strong>Generate Report</strong><br>
                        <small>Client performance report</small>
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-success btn-block" id="export-all-btn">
                        <i class="fas fa-file-excel"></i><br>
                        <strong>Export All Data</strong><br>
                        <small>Download complete dataset</small>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Client Summary Cards --}}
    <div class="row" id="client-summary-cards">
        {{-- Will be populated dynamically based on client filter --}}
    </div>
@stop

@section('css')
    <style>
        .client-selected {
            background-color: #e8f5e8 !important;
        }
        .client-filter-active {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #28a745;
            font-weight: bold;
        }
        #macro-client-plots-map {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        .read-only-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        .client-summary-card {
            transition: all 0.3s ease;
        }
        .client-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let macroClientTable;
            let macroClientMap;
            let currentClientFilter = '';

            // Initialize DataTable
            macroClientTable = $('#macro-client-plots-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.plot-clients.data") }}',
                    data: function(d) {
                        d.client_filter = $('#client-filter').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'plot_name', name: 'plot_name' },
                    { data: 'address', name: 'address' },
                    { data: 'macro_client', name: 'macro_client' },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'status', name: 'status' },
                    { data: 'value', name: 'value' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: 25,
                responsive: true,
                order: [[0, 'desc']],
                initComplete: function() {
                    // Add read-only indicator
                    $('.dataTables_wrapper').prepend(
                        '<div class="alert alert-info alert-sm mb-2">' +
                        '<i class="fas fa-eye"></i> <strong>Read-Only View:</strong> ' +
                        'This screen shows plots assigned to macro clients. To assign new plots, use the Group Plots section.' +
                        '</div>'
                    );
                }
            });

            // Client filter change
            $('#client-filter').change(function() {
                currentClientFilter = $(this).val();
                macroClientTable.draw();
                updateMapView();
                updateClientSummary();
                updateActionButtons();
            });

            // Update action buttons based on client selection
            function updateActionButtons() {
                const hasClientSelected = currentClientFilter !== '';
                $('#view-client-details-btn').prop('disabled', !hasClientSelected);
            }

            // Tab change handling
            $('#macroClientTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#client-map-view') {
                    initializeMap();
                }
            });

            // View client details
            $('#view-client-details-btn').click(function() {
                if (!currentClientFilter) {
                    alert('Please select a macro client first.');
                    return;
                }
                loadClientDetails(currentClientFilter);
            });

            // Load client details
            function loadClientDetails(clientName) {
                $('#client-details-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#clientDetailsModal').modal('show');
                
                $.ajax({
                    url: '{{ route("admin.plot-clients.api.client-details") }}',
                    method: 'GET',
                    data: { client: clientName },
                    success: function(response) {
                        if (response.success) {
                            const client = response.client;
                            const content = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-building"></i> ${client.macro_client}</h5>
                                        <table class="table table-sm">
                                            <tr><td><strong>Total Plots:</strong></td><td>${client.plots_count}</td></tr>
                                            <tr><td><strong>Active Projects:</strong></td><td>${client.projects_count}</td></tr>
                                            <tr><td><strong>Total Value:</strong></td><td>$${number_format(client.total_value)}</td></tr>
                                            <tr><td><strong>Status:</strong></td><td><span class="badge badge-success">Active</span></td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Recent Projects</h6>
                                        <div class="list-group list-group-flush">
                                            ${client.recent_projects.map(project => 
                                                `<div class="list-group-item d-flex justify-content-between align-items-center">
                                                    ${project.property_name}
                                                    <span class="badge badge-primary">${project.plots_count} plots</span>
                                                </div>`
                                            ).join('')}
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#client-details-content').html(content);
                        }
                    },
                    error: function() {
                        $('#client-details-content').html('<div class="alert alert-danger">Failed to load client details.</div>');
                    }
                });
            }

            // Update client summary cards
            function updateClientSummary() {
                if (!currentClientFilter) {
                    $('#client-summary-cards').empty();
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.plot-clients.api.client-summary") }}',
                    method: 'GET',
                    data: { client: currentClientFilter },
                    success: function(response) {
                        if (response.success) {
                            const summary = response.summary;
                            const cards = `
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success client-summary-card">
                                        <div class="inner">
                                            <h3>${summary.total_plots}</h3>
                                            <p>Client Plots</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info client-summary-card">
                                        <div class="inner">
                                            <h3>${summary.active_projects}</h3>
                                            <p>Active Projects</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-project-diagram"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning client-summary-card">
                                        <div class="inner">
                                            <h3>$${number_format(summary.total_value)}</h3>
                                            <p>Total Value</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-primary client-summary-card">
                                        <div class="inner">
                                            <h3>${summary.completion_rate}%</h3>
                                            <p>Completion Rate</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-chart-pie"></i></div>
                                    </div>
                                </div>
                            `;
                            $('#client-summary-cards').html(cards);
                        }
                    }
                });
            }

            // Initialize map (placeholder)
            function initializeMap() {
                if (!macroClientMap) {
                    console.log('Initializing Macro Client Plots Map');
                    // Initialize Google Maps here
                }
            }

            // Update map view based on client filter
            function updateMapView() {
                console.log('Updating map view for client:', currentClientFilter);
                // Update map markers based on client filter
            }

            // Export functionality
            $('#export-client-data-btn').click(function() {
                const clientFilter = $('#client-filter').val();
                const url = '{{ route("admin.plot-clients.export") }}' + (clientFilter ? '?client=' + encodeURIComponent(clientFilter) : '');
                window.open(url, '_blank');
            });

            // Generate report
            $('#generate-report-btn').click(function() {
                const clientFilter = $('#client-filter').val();
                if (!clientFilter) {
                    alert('Please select a specific client to generate a report.');
                    return;
                }
                const url = '{{ route("admin.plot-clients.report") }}?client=' + encodeURIComponent(clientFilter);
                window.open(url, '_blank');
            });

            // Export all data
            $('#export-all-btn').click(function() {
                if (confirm('This will export all macro client data. Continue?')) {
                    window.open('{{ route("admin.plot-clients.export-all") }}', '_blank');
                }
            });

            // Initialize
            updateActionButtons();
        });

        // Helper function for number formatting
        function number_format(number) {
            return new Intl.NumberFormat().format(number);
        }
    </script>
@stop

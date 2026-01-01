@extends('adminlte::page')

@section('title', 'Macro Clients - Project Tracker')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-building"></i> Macro Clients</h1>
        <div>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-secondary">
                <i class="fas fa-map"></i> Google Maps
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Main Content Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-building"></i> Macro Client Properties
            </h3>
        </div>
        <div class="card-body">
            {{-- Client Selector Row --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="macro-client-select">Select Macro Client:</label>
                        <select id="macro-client-select" class="form-control form-control-lg">
                            <option value="">-- Select a Macro Client --</option>
                            <option value="all">All Clients</option>
                            @foreach($macroClients ?? [] as $client)
                                <option value="{{ $client->macro_client }}">
                                    {{ $client->macro_client }} ({{ $client->plots_count }} properties)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status-filter">Status Filter:</label>
                        <select id="status-filter" class="form-control form-control-lg">
                            <option value="all">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="quoted">Quoted</option>
                            <option value="completed">Completed</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="consultant-filter">Consultant Filter:</label>
                        <select id="consultant-filter" class="form-control form-control-lg">
                            <option value="all">All Properties</option>
                            <option value="with_consultant">With Consultant</option>
                            <option value="no_consultant">Without Consultant</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-block">
                            <button type="button" class="btn btn-primary btn-lg btn-block" id="load-client-data-btn" disabled>
                                <i class="fas fa-search"></i> Load Client Properties
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- No Client Selected Message (Shows initially) --}}
            <div id="no-client-selected-message" class="text-center p-5" style="min-height: 500px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; flex-direction: column; border: 2px dashed #dee2e6; border-radius: 0.5rem; margin-top: 2rem;">
                <div class="mb-4">
                    <i class="fas fa-building text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                </div>
                <h3 class="text-muted mb-3">
                    <i class="fas fa-search mr-2"></i>No Client Selected
                </h3>
                <p class="text-muted mb-4 lead" style="max-width: 500px;">
                    Select a macro client from the dropdown above and choose your consultant filter preferences, then click <strong>"Load Client Properties"</strong> to view project details and maps.
                </p>
                <div class="text-center mb-4">
                    <div class="card border-0 shadow-sm d-inline-block" style="background-color: rgba(255,255,255,0.9);">
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <i class="fas fa-building text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <div><small class="text-muted">Select Client</small></div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-arrow-right text-muted mb-2" style="font-size: 1.5rem;"></i>
                                    <div><small class="text-muted">Choose Filter</small></div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-eye text-success mb-2" style="font-size: 1.5rem;"></i>
                                    <div><small class="text-muted">View Data</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-muted">
                    <i class="fas fa-arrow-up mr-2"></i>
                    Use the controls above to get started
                </div>
            </div>

            {{-- Client Summary Section (Hidden initially) --}}
            <div id="client-summary-section" style="display: none;">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Properties</span>
                                <span class="info-box-number" id="summary-total-properties">0</span>
                            </div>
                            <div class="info-box-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <div class="info-box-content">
                                <span class="info-box-text">Active Projects</span>
                                <span class="info-box-number" id="summary-active-projects">0</span>
                            </div>
                            <div class="info-box-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Value</span>
                                <span class="info-box-number" id="summary-total-value">$0</span>
                            </div>
                            <div class="info-box-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-primary">
                            <div class="info-box-content">
                                <span class="info-box-text">Completed</span>
                                <span class="info-box-number" id="summary-completed">0</span>
                            </div>
                            <div class="info-box-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Consultant Statistics Row --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-box bg-cyan">
                            <div class="info-box-content">
                                <span class="info-box-text">With Consultant</span>
                                <span class="info-box-number" id="summary-with-consultant">0</span>
                            </div>
                            <div class="info-box-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-secondary">
                            <div class="info-box-content">
                                <span class="info-box-text">Unassigned</span>
                                <span class="info-box-number" id="summary-without-consultant">0</span>
                            </div>
                            <div class="info-box-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Navigation (Hidden initially) --}}
            <div id="client-data-tabs" style="display: none;">
                <ul class="nav nav-tabs" id="clientTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="table-tab" data-toggle="tab" href="#table-view" role="tab">
                            <i class="fas fa-table"></i> Properties Table
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="map-tab" data-toggle="tab" href="#map-view" role="tab">
                            <i class="fas fa-map"></i> Properties Map
                        </a>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content" id="clientTabContent">
                    {{-- Table View Tab --}}
                    <div class="tab-pane fade show active" id="table-view" role="tabpanel">
                        <div class="mt-3">
                            <div class="table-responsive">
                                <table id="client-properties-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Property Name</th>
                                            <th>Address</th>
                                            <th>Property Type</th>
                                            <th>Status</th>
                                            <th>Consultant</th>
                                            <th>Project Value</th>
                                            <th>Inspection Date</th>
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
                    <div class="tab-pane fade" id="map-view" role="tabpanel">
                        <div class="mt-3">
                            <!-- Default "No Map Selected" Design -->
                            <div id="no-map-selected" class="text-center p-5" style="min-height: 600px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); display: flex; align-items: center; justify-content: center; flex-direction: column; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                <div class="mb-4">
                                    <i class="fas fa-map-marker-alt text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                </div>
                                <h4 class="text-muted mb-3">No Client Selected</h4>
                                <p class="text-muted mb-4 lead">Select a macro client and load properties to view them on the map</p>
                                <div class="text-muted">
                                    <i class="fas fa-arrow-up mr-2"></i>
                                    Choose a <strong>Macro Client</strong> and click <strong>Load Client Properties</strong> to get started
                                </div>
                                <div class="mt-4">
                                    <div class="card border-0 shadow-sm" style="background-color: rgba(255,255,255,0.8);">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-building text-primary mb-2"></i>
                                            <div><small class="text-muted">Client Properties Map View</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actual Google Map (hidden by default) -->
                            <div id="client-properties-map" style="height: 600px; width: 100%; border: 1px solid #dee2e6; border-radius: 0.25rem; display: none;"></div>
                            <div class="mt-2" id="map-controls" style="display: none;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Click on property markers to view details. Red markers indicate completed projects, blue markers are active projects.
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" id="fit-to-bounds-btn">
                                                <i class="fas fa-expand-arrows-alt"></i> Fit All Properties
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="toggle-clustering-btn">
                                                <i class="fas fa-layer-group"></i> Toggle Clustering
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- No Data Message (Hidden initially) --}}
            <div id="no-data-message" style="display: none;">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No Properties Found</h5>
                    <p>The selected macro client has no properties associated with it.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        #macro-client-select {
            font-size: 1.1rem;
            border: 2px solid #007bff;
        }
        
        #load-client-data-btn {
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        #no-client-selected-message {
            transition: all 0.3s ease-in-out;
        }
        
        #no-client-selected-message:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            color: #007bff;
            font-weight: bold;
        }
        
        #client-properties-map {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        
        .info-box {
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-box-icon {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        
        .client-loading {
            position: relative;
        }
        
        .client-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
        }
        
        .client-loading::before {
            content: 'Loading...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1001;
            background: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    <!-- Google Maps API -->
    <script>
        // Error handling for Google Maps API loading
        window.gm_authFailure = function() {
            console.error('Google Maps API authentication failed. Please check your API key.');
            alert('Google Maps API authentication failed. Please check your API key and try again.');
        };
    </script>
    <script async defer 
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY') }}&libraries=geometry&callback=initGoogleMaps&loading=async">
    </script>
    
    <script>
        // Global variables
        let clientPropertiesTable;
        let clientPropertiesMap;
        let currentMarkers = [];
        let currentClient = null;
        let currentConsultantFilter = 'all';  // Track the current consultant filter
        
        // Global callback for Google Maps initialization
        function initGoogleMaps() {
            console.log('Google Maps API loaded successfully');
        }
        
        $(document).ready(function() {
            // Initialize page state
            $('#no-client-selected-message').show();
            
            // Enable/disable load button based on client selection
            $('#macro-client-select').change(function() {
                const selectedClient = $(this).val();
                const loadBtn = $('#load-client-data-btn');
                
                if (selectedClient) {
                    loadBtn.prop('disabled', false);
                    currentClient = selectedClient;
                } else {
                    loadBtn.prop('disabled', true);
                    currentClient = null;
                    currentConsultantFilter = 'all'; // Reset filter when no client selected
                    hideClientData();
                }
            });
            
            // Handle status filter changes
            $('#status-filter').change(function() {
                const statusFilter = $(this).val();
                const consultantFilter = $('#consultant-filter').val();
                
                if (currentClient && currentClient !== '') {
                    loadClientProperties(currentClient, statusFilter, consultantFilter);
                }
            });
            
            // Handle consultant filter changes
            $('#consultant-filter').change(function() {
                const consultantFilter = $(this).val();
                const statusFilter = $('#status-filter').val();
                currentConsultantFilter = consultantFilter; // Update the stored filter
                
                console.log('Consultant filter changed to:', consultantFilter, 'Current client:', currentClient);
                
                if (consultantFilter === 'no_consultant') {
                    // Load all active properties without consultant across all clients
                    console.log('Loading all active properties without consultant across all clients');
                    loadClientProperties('all', statusFilter, consultantFilter);
                } else if (currentClient) {
                    // Normal client-specific or all-client filtering (includes "all" clients)
                    console.log('Loading properties for current client with new consultant filter');
                    loadClientProperties(currentClient, statusFilter, consultantFilter);
                } else {
                    console.log('No current client set, skipping load');
                }
            });
            
            // Load client data button click
            $('#load-client-data-btn').click(function() {
                if (currentClient) {
                    const statusFilter = $('#status-filter').val();
                    const consultantFilter = $('#consultant-filter').val();
                    loadClientProperties(currentClient, statusFilter, consultantFilter);
                }
            });
            
            // Tab change handling - initialize map when map tab is clicked
            $('#map-tab').on('shown.bs.tab', function (e) {
                if (currentClient && typeof google !== 'undefined') {
                    setTimeout(initializeMap, 100);
                }
            });
            
            // Map control buttons
            $('#fit-to-bounds-btn').click(function() {
                if (clientPropertiesMap && currentMarkers.length > 0) {
                    fitMapToBounds();
                }
            });
            
            $('#toggle-clustering-btn').click(function() {
                // Toggle marker clustering (implement if needed)
                alert('Clustering toggle functionality to be implemented');
            });
        });
        
        function loadClientProperties(clientName, statusFilter = 'all', consultantFilter = 'all') {
            console.log('Loading properties for client:', clientName, 'with status filter:', statusFilter, 'consultant filter:', consultantFilter);
            
            // Special handling ONLY for "no consultant" filter - show ALL active properties without consultants
            if (consultantFilter === 'no_consultant') {
                console.log('Loading all active properties without consultant across all clients');
                // Show loading state
                showLoadingState();
                
                // Skip client summary and go straight to table
                loadClientPropertiesTable(null, statusFilter, consultantFilter);
                showClientData();
                showMapArea(); // Show the actual map area
                
                // Update summary to show this is for no consultant
                updateClientSummaryForNoConsultant();
                return;
            }
            
            // For "all clients" or specific clients, load data normally
            if (clientName === 'all' || (clientName && clientName !== '')) {
                console.log('Loading properties for:', clientName === 'all' ? 'all clients' : 'specific client ' + clientName);
                // Show loading state
                showLoadingState();
                
                // Load data normally
                loadClientPropertiesTable(clientName, statusFilter, consultantFilter);
                showClientData();
                showMapArea(); // Show the actual map area
                
                // Update summary for all clients
                if (clientName === 'all') {
                    updateClientSummaryForAllClients();
                    return;
                }
            }
            
            // Normal client-specific loading
            console.log('Loading properties for specific client:', clientName);
            
            // Show loading state
            showLoadingState();
            
            // AJAX call to load client data
            $.ajax({
                url: '{{ route("admin.macro-clients.client-details") }}',
                method: 'GET',
                data: { 
                    client: clientName,
                    status_filter: statusFilter,
                    consultant_filter: consultantFilter
                },
                success: function(response) {
                    console.log('Client data received:', response);
                    
                    if (response.success) {
                        updateClientSummary(response.data);
                        loadClientPropertiesTable(clientName, statusFilter, consultantFilter);
                        showClientData();
                        showMapArea(); // Show the actual map area
                    } else {
                        showNoDataMessage();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading client data:', error);
                    alert('Error loading client properties. Please try again.');
                    hideLoadingState();
                },
                complete: function() {
                    hideLoadingState();
                }
            });
        }
        
        function updateClientSummary(data) {
            $('#summary-total-properties').text(data.plots_count || 0);
            $('#summary-active-projects').text(data.active_projects || 0);
            $('#summary-total-value').text('$' + (data.total_value ? data.total_value.toLocaleString() : '0'));
            $('#summary-completed').text(data.completed_projects || 0);
        }
        
        function updateClientSummaryForNoConsultant() {
            // Clear the summary boxes for "no consultant" filter
            $('#summary-total-properties').text('All Clients');
            $('#summary-active-projects').text('Active Only');
            $('#summary-total-value').text('Unassigned');
            $('#summary-completed').text('No Consultant');
        }
        
        function updateClientSummaryForAllClients() {
            // Clear the summary boxes for "all clients" view
            $('#summary-total-properties').text('All Clients');
            $('#summary-active-projects').text('All Statuses');
            $('#summary-total-value').text('All Projects');
            $('#summary-completed').text('All Filters');
        }
        
        function loadClientPropertiesTable(clientName, statusFilter = 'all', consultantFilter = 'all') {
            // Destroy existing table if it exists
            if (clientPropertiesTable) {
                clientPropertiesTable.destroy();
            }
            
            // Initialize DataTable
            clientPropertiesTable = $('#client-properties-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.macro-clients.properties-data") }}',
                    data: function(d) {
                        d.client_filter = clientName;
                        d.status_filter = statusFilter;
                        d.consultant_filter = consultantFilter;
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'property_name', name: 'property_name' },
                    { data: 'address', name: 'address' },
                    { data: 'property_type', name: 'property_type' },
                    { 
                        data: 'report_status', 
                        name: 'report_status',
                        render: function(data, type, row) {
                            const statusColors = {
                                'completed': 'success',
                                'in-progress': 'primary',
                                'in-review': 'warning',
                                'not-started': 'secondary'
                            };
                            const badgeClass = statusColors[data] || 'secondary';
                            return `<span class="badge badge-${badgeClass}">${data || 'Unknown'}</span>`;
                        }
                    },
                    { 
                        data: 'assigned_consultant', 
                        name: 'assigned_consultant',
                        render: function(data, type, row) {
                            return data ? `<span class="badge badge-info">${data}</span>` : '<span class="text-muted">Unassigned</span>';
                        }
                    },
                    { 
                        data: 'quoted_price', 
                        name: 'quoted_price',
                        render: function(data, type, row) {
                            return data ? '$' + parseFloat(data).toLocaleString() : '-';
                        }
                    },
                    { 
                        data: 'scheduled_date_of_inspection', 
                        name: 'scheduled_date_of_inspection',
                        render: function(data, type, row) {
                            return data ? new Date(data).toLocaleDateString() : '-';
                        }
                    },
                    { 
                        data: 'actions', 
                        name: 'actions', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary view-property" data-id="${row.id}" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success locate-on-map" data-id="${row.id}" title="Locate on Map">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 25,
                responsive: true,
                order: [[0, 'desc']],
                drawCallback: function() {
                    // Bind action button events
                    bindTableActionEvents();
                }
            });
        }
        
        function bindTableActionEvents() {
            $('.view-property').off('click').on('click', function() {
                const propertyId = $(this).data('id');
                alert('View property details for ID: ' + propertyId + ' (to be implemented)');
            });
            
            $('.locate-on-map').off('click').on('click', function() {
                const propertyId = $(this).data('id');
                // Switch to map tab and locate the property
                $('#map-tab').tab('show');
                setTimeout(() => {
                    locatePropertyOnMap(propertyId);
                }, 500);
            });
        }
        
        function initializeMap() {
            if (typeof google === 'undefined') {
                console.warn('Google Maps API not loaded yet');
                return;
            }
            
            if (!clientPropertiesMap) {
                // Initialize map centered on Austin, TX
                clientPropertiesMap = new google.maps.Map(document.getElementById('client-properties-map'), {
                    zoom: 10,
                    center: { lat: 30.2672, lng: -97.7431 }, // Austin, TX
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
            }
            
            // Load properties on map
            if (currentClient) {
                loadPropertiesOnMap(currentClient, currentConsultantFilter);
            }
        }
        
        function loadPropertiesOnMap(clientName, consultantFilter = 'all') {
            // Clear existing markers
            clearMapMarkers();
            
            console.log('Loading properties on map for client:', clientName, 'with consultant filter:', consultantFilter);
            
            // For "no consultant" filter, we don't need to provide macro_client
            let requestData = { consultant_filter: consultantFilter };
            if (consultantFilter !== 'no_consultant') {
                requestData.macro_client = clientName;
            }
            
            $.ajax({
                url: '{{ route("admin.macro-clients.plots") }}',
                method: 'GET',
                data: requestData,
                success: function(response) {
                    console.log('Map data received:', response);
                    
                    if (response.success && response.plots) {
                        console.log(`Found ${response.plots.length} total items for mapping`);
                        console.log('Map stats:', response.stats);
                        
                        // Show debug info about addresses
                        if (response.debug && response.debug.sample_addresses) {
                            console.log('Sample addresses from HB837:', response.debug.sample_addresses);
                        }
                        
                        if (response.plots.length > 0) {
                            addPropertiesToMap(response.plots);
                            
                            if (response.stats.projects_for_geocoding > 0) {
                                console.log(`${response.stats.projects_for_geocoding} addresses will be geocoded in real-time`);
                            }
                        } else {
                            console.warn('No properties found to display on map');
                            alert('No properties with addresses found for this client.');
                        }
                    } else {
                        console.warn('No plots data received for map');
                        alert('No properties found for mapping. Please check if addresses are available.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading properties for map:', error, xhr.responseText);
                    alert('Error loading properties for map. Please check the console for details.');
                }
            });
        }
        
        function addPropertiesToMap(properties) {
            const bounds = new google.maps.LatLngBounds();
            const geocoder = new google.maps.Geocoder();
            let pendingGeocodes = 0;
            
            properties.forEach(function(property) {
                if (property.coordinates_latitude && property.coordinates_longitude) {
                    // Property has coordinates, add marker directly
                    addMarkerToMap(property, bounds);
                } else if (property.address && property.address.street_address && property.needs_geocoding) {
                    // Property needs geocoding
                    pendingGeocodes++;
                    geocodeAddress(geocoder, property, bounds, function() {
                        pendingGeocodes--;
                        if (pendingGeocodes === 0) {
                            fitMapToBounds();
                        }
                    });
                }
            });
            
            // If no geocoding needed, fit bounds immediately
            if (pendingGeocodes === 0) {
                fitMapToBounds();
            }
        }
        
        function addMarkerToMap(property, bounds) {
            const position = {
                lat: parseFloat(property.coordinates_latitude),
                lng: parseFloat(property.coordinates_longitude)
            };
            
            // Determine marker color based on status
            let markerColor = 'blue'; // Default
            if (property.hb837 && property.hb837.report_status === 'completed') {
                markerColor = 'green';
            } else if (property.hb837 && property.hb837.report_status === 'in-progress') {
                markerColor = 'orange';
            }
            
            const marker = new google.maps.Marker({
                position: position,
                map: clientPropertiesMap,
                title: property.plot_name || property.hb837?.property_name || 'Property',
                icon: {
                    url: `https://maps.google.com/mapfiles/ms/icons/${markerColor}-dot.png`,
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            // Info window content
            const infoWindow = new google.maps.InfoWindow({
                content: createInfoWindowContent(property)
            });
            
            marker.addListener('click', function() {
                infoWindow.open(clientPropertiesMap, marker);
            });
            
            currentMarkers.push(marker);
            bounds.extend(position);
        }
        
        function geocodeAddress(geocoder, property, bounds, callback) {
            const address = property.address.street_address;
            
            geocoder.geocode({ address: address }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    
                    // Update property with coordinates
                    property.coordinates_latitude = location.lat();
                    property.coordinates_longitude = location.lng();
                    
                    // Add marker to map
                    addMarkerToMap(property, bounds);
                    
                    console.log(`Geocoded address: ${address} -> ${location.lat()}, ${location.lng()}`);
                } else {
                    console.warn(`Geocoding failed for address: ${address}. Status: ${status}`);
                    
                    // Create a marker at a default location with special styling
                    const defaultPosition = { lat: 30.2672, lng: -97.7431 }; // Austin, TX
                    const marker = new google.maps.Marker({
                        position: defaultPosition,
                        map: clientPropertiesMap,
                        title: `${property.plot_name || property.hb837?.property_name} (Address not found)`,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                            scaledSize: new google.maps.Size(32, 32)
                        }
                    });
                    
                    const infoWindow = new google.maps.InfoWindow({
                        content: createInfoWindowContent(property, true)
                    });
                    
                    marker.addListener('click', function() {
                        infoWindow.open(clientPropertiesMap, marker);
                    });
                    
                    currentMarkers.push(marker);
                }
                
                if (callback) callback();
            });
        }
        
        function createInfoWindowContent(property, geocodingFailed = false) {
            const hb837 = property.hb837 || {};
            const address = property.address || {};
            
            let addressDisplay = address.street_address || 'No address available';
            if (geocodingFailed) {
                addressDisplay += ' <span class="text-danger">(Address not found)</span>';
            }
            
            return `
                <div class="p-2" style="min-width: 250px;">
                    <h6><strong>${hb837.property_name || property.plot_name || 'Unnamed Property'}</strong></h6>
                    <p class="mb-1"><i class="fas fa-map-marker-alt text-danger"></i> ${addressDisplay}</p>
                    <p class="mb-1"><i class="fas fa-building text-info"></i> Type: ${hb837.property_type || 'Unknown'}</p>
                    <p class="mb-1"><i class="fas fa-tasks text-warning"></i> Status: 
                        <span class="badge badge-${getStatusBadgeClass(hb837.report_status)}">${hb837.report_status || 'Unknown'}</span>
                    </p>
                    ${hb837.quoted_price ? `<p class="mb-1"><i class="fas fa-dollar-sign text-success"></i> Value: $${parseFloat(hb837.quoted_price).toLocaleString()}</p>` : ''}
                    ${geocodingFailed ? '<p class="mb-1 text-warning"><i class="fas fa-exclamation-triangle"></i> Address could not be located on map</p>' : ''}
                    <div class="mt-2">
                        <button class="btn btn-sm btn-primary" onclick="viewPropertyDetails(${property.id})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            `;
        }
        
        function getStatusBadgeClass(status) {
            const statusColors = {
                'completed': 'success',
                'in-progress': 'primary',
                'in-review': 'warning',
                'not-started': 'secondary'
            };
            return statusColors[status] || 'secondary';
        }
        
        function viewPropertyDetails(propertyId) {
            alert('View property details for ID: ' + propertyId + ' (to be implemented)');
        }
        
        function locatePropertyOnMap(propertyId) {
            // Find the marker for this property and open its info window
            alert('Locate property ' + propertyId + ' on map (to be implemented)');
        }
        
        function clearMapMarkers() {
            currentMarkers.forEach(marker => marker.setMap(null));
            currentMarkers = [];
        }
        
        function fitMapToBounds() {
            if (currentMarkers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                currentMarkers.forEach(marker => {
                    bounds.extend(marker.getPosition());
                });
                clientPropertiesMap.fitBounds(bounds);
                
                // If there's only one marker, zoom to a reasonable level
                if (currentMarkers.length === 1) {
                    clientPropertiesMap.setZoom(15);
                }
            }
        }
        
        function showClientData() {
            $('#no-client-selected-message').fadeOut(300);  // Hide the initial "no client selected" message with animation
            $('#client-summary-section').fadeIn();
            $('#client-data-tabs').fadeIn();
            $('#no-data-message').hide();
        }

        function showMapArea() {
            $('#no-map-selected').hide();
            $('#client-properties-map').show();
            $('#map-controls').show();
        }

        function showNoMapSelected() {
            $('#client-properties-map').hide();
            $('#map-controls').hide();
            $('#no-map-selected').show();
        }

        function hideClientData() {
            $('#client-summary-section').hide();
            $('#client-data-tabs').hide();
            $('#no-data-message').hide();
            $('#no-client-selected-message').fadeIn(400);  // Show the initial "no client selected" message with animation
            $('#status-filter').val('all'); // Reset status filter dropdown
            $('#consultant-filter').val('all'); // Reset consultant filter dropdown
            showNoMapSelected(); // Return to no map selected state
            
            // Destroy table if it exists
            if (clientPropertiesTable) {
                clientPropertiesTable.destroy();
                clientPropertiesTable = null;
            }
            
            // Clear map markers
            clearMapMarkers();
        }
        
        function showNoDataMessage() {
            $('#client-summary-section').hide();
            $('#client-data-tabs').hide();
            $('#no-data-message').fadeIn();
        }
        
        function showLoadingState() {
            $('#load-client-data-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        }
        
        function hideLoadingState() {
            $('#load-client-data-btn').prop('disabled', false).html('<i class="fas fa-search"></i> Load Client Properties');
        }
    </script>
@stop

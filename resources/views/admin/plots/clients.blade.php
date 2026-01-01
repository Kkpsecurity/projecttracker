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
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="macro-client-select">Select Macro Client:</label>
                        <select id="macro-client-select" class="form-control form-control-lg">
                            <option value="">-- Select a Macro Client --</option>
                            @foreach($macroClients as $client)
                                <option value="{{ $client['name'] }}">
                                    {{ $client['name'] }} ({{ $client['plots_count'] }} properties)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-block">
                            <button type="button" class="btn btn-primary btn-lg btn-block" id="load-client-data-btn" disabled>
                                <i class="fas fa-search"></i> Load Properties
                            </button>
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
                                            <th>Project Value</th>
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
                            <div id="client-properties-map" style="height: 600px; width: 100%; border: 1px solid #dee2e6; border-radius: 0.25rem;"></div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Click on property markers to view details. Different colors represent different project statuses.
                                </small>
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
        
        // Global callback for Google Maps initialization
        function initGoogleMaps() {
            console.log('Google Maps API loaded successfully');
        }
        
        $(document).ready(function() {
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
                    hideClientData();
                }
            });
            
            // Load client data button click
            $('#load-client-data-btn').click(function() {
                if (currentClient) {
                    loadClientProperties(currentClient);
                }
            });
            
            // Tab change handling - initialize map when map tab is clicked
            $('#map-tab').on('shown.bs.tab', function (e) {
                if (currentClient && typeof google !== 'undefined') {
                    setTimeout(initializeMap, 100);
                }
            });
        });
        
        function loadClientProperties(clientName) {
            console.log('Loading properties for client:', clientName);
            
            // Show loading state
            showLoadingState();
            
            // Load both table and map data
            loadClientPropertiesTable(clientName);
            showClientData();
        }
        
        function loadClientPropertiesTable(clientName) {
            // Destroy existing table if it exists
            if (clientPropertiesTable) {
                clientPropertiesTable.destroy();
            }
            
            // Initialize DataTable
            clientPropertiesTable = $('#client-properties-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.plot-clients.data") }}',
                    data: function(d) {
                        d.client_filter = clientName;
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
                        data: 'quoted_price', 
                        name: 'quoted_price',
                        render: function(data, type, row) {
                            return data ? '$' + parseFloat(data).toLocaleString() : '-';
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
                    hideLoadingState();
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
                loadPropertiesOnMap(currentClient);
            }
        }
        
        function loadPropertiesOnMap(clientName) {
            // Clear existing markers
            clearMapMarkers();
            
            $.ajax({
                url: '{{ route("admin.maps.macro-client.plots") }}',
                method: 'GET',
                data: { macro_client: clientName },
                success: function(response) {
                    if (response.success && response.plots) {
                        addPropertiesToMap(response.plots);
                        fitMapToBounds();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading properties for map:', error);
                }
            });
        }
        
        function addPropertiesToMap(properties) {
            const bounds = new google.maps.LatLngBounds();
            
            properties.forEach(function(property) {
                if (property.coordinates_latitude && property.coordinates_longitude) {
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
            });
            
            // Fit map to show all markers
            if (currentMarkers.length > 0) {
                clientPropertiesMap.fitBounds(bounds);
                if (currentMarkers.length === 1) {
                    clientPropertiesMap.setZoom(15);
                }
            }
        }
        
        function createInfoWindowContent(property) {
            const hb837 = property.hb837 || {};
            const address = property.address || {};
            
            return `
                <div class="p-2" style="min-width: 250px;">
                    <h6><strong>${hb837.property_name || property.plot_name || 'Unnamed Property'}</strong></h6>
                    <p class="mb-1"><i class="fas fa-map-marker-alt text-danger"></i> ${address.street_address || 'No address available'}</p>
                    <p class="mb-1"><i class="fas fa-building text-info"></i> Type: ${hb837.property_type || 'Unknown'}</p>
                    <p class="mb-1"><i class="fas fa-tasks text-warning"></i> Status: 
                        <span class="badge badge-${getStatusBadgeClass(hb837.report_status)}">${hb837.report_status || 'Unknown'}</span>
                    </p>
                    ${hb837.quoted_price ? `<p class="mb-1"><i class="fas fa-dollar-sign text-success"></i> Value: $${parseFloat(hb837.quoted_price).toLocaleString()}</p>` : ''}
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
            $('#client-data-tabs').fadeIn();
            $('#no-data-message').hide();
        }
        
        function hideClientData() {
            $('#client-data-tabs').hide();
            $('#no-data-message').hide();
            
            // Destroy table if it exists
            if (clientPropertiesTable) {
                clientPropertiesTable.destroy();
                clientPropertiesTable = null;
            }
            
            // Clear map markers
            clearMapMarkers();
        }
        
        function showNoDataMessage() {
            $('#client-data-tabs').hide();
            $('#no-data-message').fadeIn();
        }
        
        function showLoadingState() {
            $('#load-client-data-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        }
        
        function hideLoadingState() {
            $('#load-client-data-btn').prop('disabled', false).html('<i class="fas fa-search"></i> Load Properties');
        }
    </script>
@stop

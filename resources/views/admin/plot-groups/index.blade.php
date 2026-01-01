@extends('adminlte::page')

@section('title', 'Plot Groups Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Plot Groups Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Plot Groups</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Macro Client Section (Top - Gray Background) -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="background-color: #f8f9fa;">
                <div class="card-header" style="background-color: #e9ecef; border-bottom: 2px solid #dee2e6;">
                    <h3 class="card-title">
                        <i class="fas fa-building text-primary"></i> Macro Client Plots
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Client-Based Plot Management</span>
                    </div>
                </div>
                <div class="card-body" style="background-color: #f8f9fa;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="macro-client-select">Select Macro Client:</label>
                                <div class="input-group">
                                    <select id="macro-client-select" class="form-control">
                                        <option value="">Choose a macro client...</option>
                                        @foreach($macroClients as $client)
                                            <option value="{{ $client }}">{{ $client }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-info" type="button" id="load-client-plots">
                                            <i class="fas fa-search"></i> Load Plots
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="consultant-filter">Consultant Filter:</label>
                                <select id="consultant-filter" class="form-control">
                                    <option value="all">All Properties</option>
                                    <option value="with_consultant">With Consultant Assigned</option>
                                    <option value="no_consultant">No Consultant Assigned</option>
                                </select>
                                <small class="form-text text-muted">Filter properties by consultant assignment status</small>
                            </div>
                        </div>
                    </div>

                    <div id="client-plots-info" style="display: none;">
                        <div class="alert alert-info">
                            <h5 id="client-name"></h5>
                            <div class="row">
                                <div class="col-4">
                                    <strong>Projects:</strong> <span id="projects-count">0</span>
                                </div>
                                <div class="col-4">
                                    <strong>Plots:</strong> <span id="plots-count">0</span>
                                </div>
                                <div class="col-4">
                                    <strong>Addresses:</strong> <span id="addresses-count">0</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-success" id="create-group-from-client">
                                    <i class="fas fa-plus"></i> Create Group from Client
                                </button>
                                <button class="btn btn-sm btn-primary" id="view-client-on-map">
                                    <i class="fas fa-map"></i> View on Map
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Section (Bottom - White Background) -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card" style="background-color: #ffffff;">
                <div class="card-header" style="background-color: #ffffff; border-bottom: 2px solid #007bff;">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group text-success"></i> Plot Groups Management
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-group-modal">
                            <i class="fas fa-plus"></i> Create Group
                        </button>
                        <span class="badge badge-success ml-2">Independent Groups</span>
                    </div>
                </div>
                <div class="card-body" style="background-color: #ffffff;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Plots Count</th>
                                    <th>Color</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="plot-groups-table">
                                @foreach($plotGroups as $group)
                                    <tr data-group-id="{{ $group->id }}">
                                        <td>
                                            <strong>{{ $group->name }}</strong>
                                            @if($group->description)
                                                <br><small class="text-muted">{{ Str::limit($group->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $group->plots_count }}</span>
                                        </td>
                                        <td>
                                            <span class="color-indicator" style="background-color: {{ $group->color }}"></span>
                                            {{ $group->color }}
                                        </td>
                                        <td>
                                            @if($group->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info view-group-plots" data-id="{{ $group->id }}" title="View on Map">
                                                    <i class="fas fa-map"></i>
                                                </button>
                                                <button class="btn btn-warning edit-group" data-id="{{ $group->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger delete-group" data-id="{{ $group->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
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

    <!-- Map Display -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-map-marked-alt"></i> Plot Map
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-secondary" id="clear-map">
                            <i class="fas fa-times"></i> Clear Map
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Default "No Map Selected" Design -->
                    <div id="no-map-selected" class="text-center p-5" style="min-height: 500px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <div class="mb-4">
                            <i class="fas fa-map-marker-alt text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Client Selected</h4>
                        <p class="text-muted mb-4 lead">Select a macro client above to view plots on the map</p>
                        <div class="text-muted">
                            <i class="fas fa-arrow-up mr-2"></i>
                            Choose from the <strong>Macro Client</strong> dropdown or select a <strong>Plot Group</strong> to get started
                        </div>
                        <div class="mt-4">
                            <div class="row justify-content-center">
                                <div class="col-auto">
                                    <div class="card border-0 shadow-sm" style="background-color: rgba(255,255,255,0.8);">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-building text-primary mb-2"></i>
                                            <div><small class="text-muted">Macro Client Plots</small></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="card border-0 shadow-sm" style="background-color: rgba(255,255,255,0.8);">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-layer-group text-success mb-2"></i>
                                            <div><small class="text-muted">Plot Groups</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actual Google Map (hidden by default) -->
                    <div id="map" style="height: 500px; display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
   @include('admin.plot-groups.partials.create-group-modal')
@stop

@section('css')
    <style>
        .color-indicator {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            border: 1px solid #ddd;
        }

        #map {
            width: 100%;
        }

        .plot-info-window {
            max-width: 250px;
        }

        .plot-info-window h6 {
            margin-bottom: 5px;
            color: #333;
        }

        .btn-group-sm .btn {
            font-size: 0.75rem;
        }
    </style>
@stop

@section('js')
    <script>
        let map;
        let markers = [];
        let infoWindow;
        let currentClientPlots = [];

        $(document).ready(function() {
            // initMap will be called by Google Maps API callback
            initEventHandlers();
        });

        // Global function that will be called by Google Maps API
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: { lat: 30.2672, lng: -97.7431 }, // Austin, TX
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            infoWindow = new google.maps.InfoWindow();
        }

        function initEventHandlers() {
            // Load macro client plots
            $('#load-client-plots').click(function() {
                const macroClient = $('#macro-client-select').val();
                const consultantFilter = $('#consultant-filter').val();
                
                if (!macroClient) {
                    alert('Please select a macro client');
                    return;
                }

                // Check if Google Maps is available
                if (typeof google === 'undefined') {
                    alert('Google Maps is still loading. Please wait a moment and try again.');
                    return;
                }

                loadMacroClientPlots(macroClient, consultantFilter);
            });

            // Create group form
            $('#create-group-form').submit(function(e) {
                e.preventDefault();
                createPlotGroup();
            });

            // View group plots on map
            $(document).on('click', '.view-group-plots', function() {
                const groupId = $(this).data('id');
                loadGroupPlots(groupId);
            });

            // Create group from client
            $('#create-group-from-client').click(function() {
                const clientName = $('#macro-client-select').val();
                if (!clientName) return;

                $('#group-name').val(clientName + ' Plots');
                $('#group-description').val('Plot group for ' + clientName + ' properties');
                $('#create-group-modal').modal('show');
            });

            // Clear map
            $('#clear-map').click(function() {
                clearMarkers();
                $('#client-plots-info').hide();
                showNoMapSelected();
            });
        }

        function loadMacroClientPlots(macroClient, consultantFilter = 'all') {
            $.ajax({
                url: '{{ route("admin.plot-groups.api.macro-client-plots") }}',
                data: { 
                    macro_client: macroClient,
                    consultant_filter: consultantFilter
                },
                success: function(response) {
                    if (response.success) {
                        currentClientPlots = response.plots;
                        showMap(); // Show the map and hide no-map-selected
                        displayPlotsOnMap(response.plots, response.project_addresses);
                        updateClientInfo(response);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error loading plots for this client');
                }
            });
        }

        function loadGroupPlots(groupId) {
            $.ajax({
                url: `/admin/plot-groups/${groupId}/plots`,
                success: function(response) {
                    if (response.success) {
                        showMap(); // Show the map and hide no-map-selected
                        displayGroupPlotsOnMap(response.plots, response.plot_group);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error loading group plots');
                }
            });
        }

        function displayPlotsOnMap(plots, projectAddresses = []) {
            // Check if Google Maps is available
            if (typeof google === 'undefined') {
                console.error('Google Maps API not loaded yet');
                return;
            }

            clearMarkers();

            // Add plot markers (red)
            plots.forEach(function(plot) {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(plot.coordinates_latitude), lng: parseFloat(plot.coordinates_longitude) },
                    map: map,
                    title: plot.plot_name || 'Unnamed Plot',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                const infoContent = `
                    <div class="plot-info-window">
                        <h6>${plot.plot_name || 'Unnamed Plot'}</h6>
                        <p><strong>Type:</strong> Plot</p>
                        ${plot.address ? `<p><strong>Address:</strong> ${plot.address.full_address}</p>` : ''}
                        ${plot.hb837 ? `<p><strong>Project:</strong> ${plot.hb837.property_name}</p>` : ''}
                    </div>
                `;

                marker.addListener('click', function() {
                    infoWindow.setContent(infoContent);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });

            // Add project address markers (yellow)
            if (projectAddresses) {
                projectAddresses.forEach(function(addr) {
                    // This would require geocoding the address
                    // For now, we'll skip this part
                });
            }

            // Fit map to show all markers
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }

        function displayGroupPlotsOnMap(plots, plotGroup) {
            clearMarkers();

            plots.forEach(function(plot) {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(plot.coordinates_latitude), lng: parseFloat(plot.coordinates_longitude) },
                    map: map,
                    title: plot.plot_name || 'Unnamed Plot',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                const infoContent = `
                    <div class="plot-info-window">
                        <h6>${plot.plot_name || 'Unnamed Plot'}</h6>
                        <p><strong>Group:</strong> ${plotGroup.name}</p>
                        ${plot.address ? `<p><strong>Address:</strong> ${plot.address.full_address}</p>` : ''}
                        ${plot.hb837 ? `<p><strong>Project:</strong> ${plot.hb837.property_name}</p>` : ''}
                    </div>
                `;

                marker.addListener('click', function() {
                    infoWindow.setContent(infoContent);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });

            // Fit map to show all markers
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }

        function updateClientInfo(response) {
            $('#client-name').text(response.macro_client);
            $('#projects-count').text(response.stats.total_projects);
            $('#plots-count').text(response.stats.plots_found);
            $('#addresses-count').text(response.stats.project_addresses);
            $('#client-plots-info').show();
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        function showMap() {
            $('#no-map-selected').hide();
            $('#map').show();
        }

        function showNoMapSelected() {
            $('#map').hide();
            $('#no-map-selected').show();
        }

        function createPlotGroup() {
            const formData = new FormData(document.getElementById('create-group-form'));

            $.ajax({
                url: '{{ route("admin.plot-groups.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#create-group-modal').modal('hide');
                        location.reload(); // Refresh to show new group
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error creating plot group');
                }
            });
        }
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY_HERE') }}&callback=initMap">
    </script>
@stop

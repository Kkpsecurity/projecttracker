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
    <div class="row">
        <!-- Plot Groups Management -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group"></i> Plot Groups
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-group-modal">
                            <i class="fas fa-plus"></i> Create Group
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
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

        <!-- Macro Client Plots -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Macro Client Plots
                    </h3>
                </div>
                <div class="card-body">
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
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div class="modal fade" id="create-group-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Plot Group</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="create-group-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="group-name">Group Name *</label>
                            <input type="text" class="form-control" id="group-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="group-description">Description</label>
                            <textarea class="form-control" id="group-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="group-color">Marker Color</label>
                            <input type="color" class="form-control" id="group-color" name="color" value="#3498db">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            initMap();
            initEventHandlers();
        });

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
                if (!macroClient) {
                    alert('Please select a macro client');
                    return;
                }
                loadMacroClientPlots(macroClient);
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
            });
        }

        function loadMacroClientPlots(macroClient) {
            $.ajax({
                url: '{{ route("admin.plot-groups.macro-client-plots") }}',
                data: { macro_client: macroClient },
                success: function(response) {
                    if (response.success) {
                        currentClientPlots = response.plots;
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
@stop

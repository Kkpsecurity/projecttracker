@extends('adminlte::page')

@section('title', 'Google Maps - Project Tracker')

@section('plugins.Datatables', true)

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-map"></i> Google Maps</h1>
        <div>
            <a href="{{ route('admin.plots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Plot
            </a>
            <a href="{{ route('admin.maps.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export Data
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_plots'] }}</h3>
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
                    <h3>{{ $stats['mapped_plots'] }}</h3>
                    <p>Mapped Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_projects'] }}</h3>
                    <p>HB837 Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_addresses'] }}</h3>
                    <p>Addresses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-home"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe"></i> Interactive Map
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 600px; width: 100%;"></div>

                    <!-- Map Controls -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="add-plot-address">Add Plot from Address:</label>
                                    <div class="input-group">
                                        <input type="text" id="add-plot-address" class="form-control"
                                               placeholder="Enter address to create plot...">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button" id="add-plot-btn">
                                                <i class="fas fa-plus"></i> Add Plot
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Click on map after geocoding to place plot</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="macro-client-filter">Load Macro Client Plots:</label>
                                    <div class="input-group">
                                        <select id="macro-client-filter" class="form-control">
                                            <option value="">Select Macro Client...</option>
                                            @foreach($macroClients as $client)
                                                <option value="{{ $client }}">{{ $client }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="button" id="load-client-plots-btn">
                                                <i class="fas fa-map-marked-alt"></i> Load Plots
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">View all addresses for selected client</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Map Options:</label>
                                    <div class="btn-group d-block">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="toggle-plots">
                                            <i class="fas fa-eye"></i> Toggle Plots
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" id="center-map">
                                            <i class="fas fa-crosshairs"></i> Center Map
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" id="refresh-plots">
                                            <i class="fas fa-sync"></i> Refresh
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" id="clear-filters">
                                            <i class="fas fa-times"></i> Clear Filters
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

    <!-- Plot Information Panel -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Plot Information
                    </h3>
                </div>
                <div class="card-body">
                    <div id="plot-info-panel">
                        <p class="text-muted">Click on a plot marker to view detailed information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .map-marker {
            cursor: pointer;
        }

        .plot-info-card {
            max-width: 300px;
        }

        .info-window {
            font-family: 'Source Sans Pro', sans-serif;
        }

        .info-window h6 {
            margin-bottom: 10px;
            color: #007bff;
        }

        .info-window .btn {
            font-size: 12px;
            padding: 4px 8px;
        }

        #map {
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .map-controls {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    <script>
        let map;
        let markers = [];
        let infoWindow;
        let geocoder;
        let plotsData = @json($plots);
        let pendingPlotAddress = null;

        // Initialize Google Maps
        function initMap() {
            // Default center (adjust as needed)
            const defaultCenter = { lat: 39.8283, lng: -98.5795 }; // Center of USA

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: defaultCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            infoWindow = new google.maps.InfoWindow();
            geocoder = new google.maps.Geocoder();

            // Load plots on map
            loadPlotsOnMap();

            // Add click listener for creating new plots
            map.addListener('click', function(event) {
                if (confirm('Create a new plot at this location?')) {
                    createPlotAtLocation(event.latLng);
                }
            });

            // Initialize event listeners after map is ready
            setTimeout(initEventListeners, 100);
        }

        // Load plots on map
        function loadPlotsOnMap() {
            clearMarkers();

            plotsData.forEach(function(plot) {
                if (plot.coordinates_latitude && plot.coordinates_longitude) {
                    addMarker(plot);
                }
            });

            // Fit map to show all markers
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }

        // Add marker for plot
        function addMarker(plot) {
            const position = {
                lat: parseFloat(plot.coordinates_latitude),
                lng: parseFloat(plot.coordinates_longitude)
            };

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: plot.plot_name || `Plot ${plot.id}`,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });

            // Info window content
            const infoContent = `
                <div class="info-window">
                    <h6>${plot.plot_name || 'Unnamed Plot'}</h6>
                    <p><strong>ID:</strong> ${plot.id}</p>
                    ${plot.plot_address ? `<p><strong>Address:</strong> ${plot.plot_address.address_line_1}</p>` : ''}
                    ${plot.hb837 ? `<p><strong>Project:</strong> ${plot.hb837.property_name}</p>` : ''}
                    <div class="mt-2">
                        <a href="/admin/plots/${plot.id}" class="btn btn-primary btn-sm">View Details</a>
                        <a href="/admin/plots/${plot.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                    </div>
                </div>
            `;

            marker.addListener('click', function() {
                infoWindow.setContent(infoContent);
                infoWindow.open(map, marker);

                // Update plot info panel
                updatePlotInfoPanel(plot);
            });

            markers.push(marker);
        }

        // Clear all markers
        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        // Update plot info panel
        function updatePlotInfoPanel(plot) {
            const panel = document.getElementById('plot-info-panel');
            panel.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>${plot.plot_name || 'Unnamed Plot'}</h5>
                        <p><strong>Plot ID:</strong> ${plot.id}</p>
                        <p><strong>Coordinates:</strong> ${plot.coordinates_latitude}, ${plot.coordinates_longitude}</p>
                        ${plot.plot_address ? `<p><strong>Address:</strong> ${plot.plot_address.address_line_1}, ${plot.plot_address.city}, ${plot.plot_address.state}</p>` : ''}
                    </div>
                    <div class="col-md-6">
                        ${plot.hb837 ? `<p><strong>Project:</strong> ${plot.hb837.property_name}</p>` : ''}
                        ${plot.plot_address ? `<p><strong>ZIP:</strong> ${plot.plot_address.zip_code}</p>` : ''}
                        <div class="mt-2">
                            <a href="/admin/plots/${plot.id}" class="btn btn-primary">View Full Details</a>
                            <a href="/admin/plots/${plot.id}/edit" class="btn btn-warning">Edit Plot</a>
                        </div>
                    </div>
                </div>
            `;
        }

        // Create plot at clicked location
        function createPlotAtLocation(latLng) {
            const lat = latLng.lat();
            const lng = latLng.lng();

            // Redirect to create form with coordinates
            window.location.href = `/admin/plots/create?lat=${lat}&lng=${lng}`;
        }

        // Initialize event listeners after map loads
        function initEventListeners() {
            // Add plot from address functionality
            const addPlotBtn = document.getElementById('add-plot-btn');
            if (addPlotBtn) {
                addPlotBtn.addEventListener('click', function() {
                    const address = document.getElementById('add-plot-address').value.trim();
                    if (!address) {
                        alert('Please enter an address');
                        return;
                    }

                    // First, try to geocode the address
                    geocoder.geocode({ address: address }, function(results, status) {
                        if (status === 'OK') {
                            const location = results[0].geometry.location;
                            map.setCenter(location);
                            map.setZoom(15);

                            // Store the address for plot creation
                            pendingPlotAddress = address;

                            // Add a temporary marker
                            const tempMarker = new google.maps.Marker({
                                position: location,
                                map: map,
                                title: 'Click to create plot here',
                                icon: {
                                    url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                                    scaledSize: new google.maps.Size(32, 32)
                                }
                            });

                            // Show instructions
                            alert('Address found! Click on the map to create the plot at the exact location.');

                            // Add click listener to map for plot creation
                            const listener = map.addListener('click', function(event) {
                                createPlotFromAddress(pendingPlotAddress, event.latLng.lat(), event.latLng.lng());
                                tempMarker.setMap(null);
                                google.maps.event.removeListener(listener);
                                pendingPlotAddress = null;
                            });

                            // Remove temp marker after 30 seconds
                            setTimeout(() => {
                                tempMarker.setMap(null);
                                if (listener) google.maps.event.removeListener(listener);
                                pendingPlotAddress = null;
                            }, 30000);

                        } else {
                            alert('Address not found. Please try a different address.');
                        }
                    });
                });
            }

            // Load macro client plots functionality
            const loadClientPlotsBtn = document.getElementById('load-client-plots-btn');
            if (loadClientPlotsBtn) {
                loadClientPlotsBtn.addEventListener('click', function() {
                    const macroClient = document.getElementById('macro-client-filter').value;
                    if (!macroClient) {
                        alert('Please select a macro client');
                        return;
                    }

                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    this.disabled = true;

                    fetch(`/admin/maps/macro-client/plots?macro_client=${encodeURIComponent(macroClient)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Clear existing markers
                                clearMarkers();

                                // Add plots
                                if (data.plots && data.plots.length > 0) {
                                    data.plots.forEach(plot => addMarker(plot));

                                    // Fit map to show all plots
                                    if (markers.length > 0) {
                                        const bounds = new google.maps.LatLngBounds();
                                        markers.forEach(marker => bounds.extend(marker.getPosition()));
                                        map.fitBounds(bounds);
                                    }
                                }

                                // Show project addresses that don't have plots yet
                                if (data.project_addresses && data.project_addresses.length > 0) {
                                    data.project_addresses.forEach(projectAddr => {
                                        if (projectAddr.address) {
                                            // Try to geocode project addresses
                                            geocoder.geocode({ address: projectAddr.address }, function(results, status) {
                                                if (status === 'OK') {
                                                    const location = results[0].geometry.location;
                                                    const marker = new google.maps.Marker({
                                                        position: location,
                                                        map: map,
                                                        title: projectAddr.project_name,
                                                        icon: {
                                                            url: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                                                            scaledSize: new google.maps.Size(32, 32)
                                                        }
                                                    });

                                                    const infoContent = `
                                                        <div class="info-window">
                                                            <h6>${projectAddr.project_name}</h6>
                                                            <p><strong>Type:</strong> HB837 Project Address</p>
                                                            <p><strong>Address:</strong> ${projectAddr.address}</p>
                                                            <p><strong>Macro Client:</strong> ${projectAddr.macro_client}</p>
                                                            <div class="mt-2">
                                                                <small class="text-muted">Yellow markers are project addresses without plots</small>
                                                            </div>
                                                        </div>
                                                    `;

                                                    marker.addListener('click', function() {
                                                        infoWindow.setContent(infoContent);
                                                        infoWindow.open(map, marker);
                                                    });

                                                    markers.push(marker);
                                                }
                                            });
                                        }
                                    });
                                }

                                // Update plot info panel
                                document.getElementById('plot-info-panel').innerHTML = `
                                    <h5>Macro Client: ${data.macro_client}</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box bg-info">
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Projects</span>
                                                    <span class="info-box-number">${data.stats.total_projects}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-success">
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Plots Found</span>
                                                    <span class="info-box-number">${data.stats.plots_found}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-warning">
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Project Addresses</span>
                                                    <span class="info-box-number">${data.stats.project_addresses}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2">
                                        Red markers: Plots with coordinates<br>
                                        Yellow markers: Project addresses without plots
                                    </p>
                                `;

                            } else {
                                alert('Error loading macro client plots: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error loading macro client plots. Please try again.');
                        })
                        .finally(() => {
                            this.innerHTML = '<i class="fas fa-map-marked-alt"></i> Load Plots';
                            this.disabled = false;
                        });
                });
            }

            // Clear filters functionality
            const clearFiltersBtn = document.getElementById('clear-filters');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Reset dropdowns and inputs
                    const macroClientFilter = document.getElementById('macro-client-filter');
                    const addPlotAddress = document.getElementById('add-plot-address');
                    if (macroClientFilter) macroClientFilter.value = '';
                    if (addPlotAddress) addPlotAddress.value = '';

                    // Clear markers and reload all plots
                    clearMarkers();
                    plotsData.forEach(plot => addMarker(plot));

                    // Reset plot info panel
                    const plotInfoPanel = document.getElementById('plot-info-panel');
                    if (plotInfoPanel) {
                        plotInfoPanel.innerHTML = '<p class="text-muted">Click on a plot marker to view detailed information.</p>';
                    }

                    // Fit map to show all plots
                    if (markers.length > 0) {
                        const bounds = new google.maps.LatLngBounds();
                        markers.forEach(marker => bounds.extend(marker.getPosition()));
                        map.fitBounds(bounds);
                    }
                });
            }

            // Control buttons
            const togglePlotsBtn = document.getElementById('toggle-plots');
            if (togglePlotsBtn) {
                togglePlotsBtn.addEventListener('click', function() {
                    const visible = markers[0] && markers[0].getMap();
                    markers.forEach(marker => {
                        marker.setMap(visible ? null : map);
                    });
                    this.innerHTML = visible ?
                        '<i class="fas fa-eye-slash"></i> Show Plots' :
                        '<i class="fas fa-eye"></i> Hide Plots';
                });
            }

            const centerMapBtn = document.getElementById('center-map');
            if (centerMapBtn) {
                centerMapBtn.addEventListener('click', function() {
                    if (markers.length > 0) {
                        const bounds = new google.maps.LatLngBounds();
                        markers.forEach(marker => bounds.extend(marker.getPosition()));
                        map.fitBounds(bounds);
                    }
                });
            }

            const refreshPlotsBtn = document.getElementById('refresh-plots');
            if (refreshPlotsBtn) {
                refreshPlotsBtn.addEventListener('click', function() {
                    // Reload plots data
                    window.location.reload();
                });
            }
        }

        // Create plot from address
        function createPlotFromAddress(address, lat, lng) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }

            const data = {
                address: address,
                latitude: lat,
                longitude: lng,
                _token: csrfToken.getAttribute('content')
            };

            fetch('/admin/maps/plot/from-address', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Plot created successfully!');
                    // Add the new plot to the map
                    addMarker(data.plot);
                    // Clear the input
                    const addressInput = document.getElementById('add-plot-address');
                    if (addressInput) addressInput.value = '';
                } else {
                    alert('Error creating plot: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating plot. Please try again.');
            });
        }

    </script>

    <!-- Google Maps API -->
    @if(config('services.google_maps.api_key') && config('services.google_maps.api_key') !== 'YOUR_ACTUAL_GOOGLE_MAPS_API_KEY_HERE')
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=geometry&callback=initMap">
        </script>
    @endif

    <!-- Error handling and fallback -->
    <script>
        // Global error handler for Google Maps API
        window.gm_authFailure = function() {
            console.error('Google Maps API authentication failed. Invalid API key.');
            showMapError('Google Maps API authentication failed. Please check your API key configuration.');
        };

        // Handle API key not configured
        @if(!config('services.google_maps.api_key') || config('services.google_maps.api_key') === 'YOUR_ACTUAL_GOOGLE_MAPS_API_KEY_HERE')
            console.warn('Google Maps API key is not configured properly.');
            window.initMap = function() {
                showMapError('Google Maps API key is not configured. Please contact the administrator to set up the API key.');
            };
            // Call initMap immediately since the API script won't load
            setTimeout(initMap, 100);
        @endif

        // Show error message on map container
        function showMapError(message) {
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; border: 2px dashed #dee2e6; color: #6c757d; text-align: center; padding: 40px;">
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #ffc107;"></i>
                            <h4>Google Maps Configuration Required</h4>
                            <p style="margin-bottom: 20px;">${message}</p>
                            <div style="background: #e9ecef; padding: 15px; border-radius: 5px; text-align: left; max-width: 500px;">
                                <strong>To fix this:</strong><br>
                                1. Get a Google Maps API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a><br>
                                2. Enable Maps JavaScript API and Geocoding API<br>
                                3. Add the key to your .env file: <code>GOOGLE_MAPS_API_KEY=your_key_here</code><br>
                                4. Clear cache: <code>php artisan config:clear</code>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // Handle other Google Maps errors
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('Google Maps')) {
                console.error('Google Maps error:', e.message);
            }
        });
    </script>
@stop

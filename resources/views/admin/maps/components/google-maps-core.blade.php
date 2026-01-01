{{-- Google Maps Core JavaScript --}}
<script>
    // Global variables
    window.GoogleMapsCore = {
        map: null,
        markers: [],
        infoWindow: null,
        geocoder: null,
        plotsData: @json($plots),
        pendingPlotAddress: null,
        selectedPlaceCoordinates: null,
        selectedPlaceMarker: null,
        selectedPlot: null,
        selectedMarker: null,

        // Initialize Google Maps
        init() {
            const defaultCenter = { lat: 39.8283, lng: -98.5795 }; // Center of USA

            this.map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: defaultCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            this.infoWindow = new google.maps.InfoWindow();
            this.geocoder = new google.maps.Geocoder();

            // Load plots on map
            this.loadPlotsOnMap();

            // Add click listener for creating new plots
            this.map.addListener('click', (event) => {
                if (confirm('Create a new plot at this location?')) {
                    this.createPlotAtLocation(event.latLng);
                }
            });

            // Initialize event listeners after map is ready
            setTimeout(() => window.MapControls.init(), 100);
        },

        // Load plots on map
        loadPlotsOnMap() {
            this.clearMarkers();

            this.plotsData.forEach(plot => {
                if (plot.coordinates_latitude && plot.coordinates_longitude) {
                    this.addMarker(plot);
                }
            });

            // Fit map to show all markers
            if (this.markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                this.markers.forEach(marker => bounds.extend(marker.getPosition()));
                this.map.fitBounds(bounds);
            }
        },

        // Add marker for plot
        addMarker(plot) {
            const position = {
                lat: parseFloat(plot.coordinates_latitude),
                lng: parseFloat(plot.coordinates_longitude)
            };

            const marker = new google.maps.Marker({
                position: position,
                map: this.map,
                title: plot.plot_name || `Plot ${plot.id}`,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });

            // Info window content
            const infoContent = this.buildInfoWindowContent(plot);

            marker.addListener('click', () => {
                this.selectPlot(plot, marker);
                this.infoWindow.setContent(infoContent);
                this.infoWindow.open(this.map, marker);
                this.updatePlotInfoPanel(plot);
            });

            this.markers.push(marker);
            return marker;
        },

        // Build info window content
        buildInfoWindowContent(plot) {
            return `
                <div class="info-window">
                    <h6>${plot.plot_name || 'Unnamed Plot'}</h6>
                    <p><strong>ID:</strong> ${plot.id}</p>
                    ${plot.address ? `<p><strong>Address:</strong> ${plot.address.street_address}</p>` : ''}
                    ${plot.hb837 ? `<p><strong>Project:</strong> ${plot.hb837.property_name}</p>` : ''}
                    <div class="mt-2">
                        <a href="/admin/plots/${plot.id}" class="btn btn-primary btn-sm">View Details</a>
                        <a href="/admin/plots/${plot.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                    </div>
                </div>
            `;
        },

        // Clear all markers
        clearMarkers() {
            this.markers.forEach(marker => marker.setMap(null));
            this.markers = [];
        },

        // Update plot info panel
        updatePlotInfoPanel(plot) {
            const panel = document.getElementById('plot-info-panel');
            panel.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>${plot.plot_name || 'Unnamed Plot'}</h5>
                        <p><strong>Plot ID:</strong> ${plot.id}</p>
                        <p><strong>Coordinates:</strong> ${plot.coordinates_latitude}, ${plot.coordinates_longitude}</p>
                        ${plot.address ? `<p><strong>Address:</strong> ${plot.address.street_address}, ${plot.address.city}, ${plot.address.state}</p>` : ''}
                    </div>
                    <div class="col-md-6">
                        ${plot.hb837 ? `<p><strong>Project:</strong> ${plot.hb837.property_name}</p>` : ''}
                        ${plot.address ? `<p><strong>ZIP:</strong> ${plot.address.zip_code}</p>` : ''}
                        <div class="mt-2">
                            <a href="/admin/plots/${plot.id}" class="btn btn-primary">View Full Details</a>
                            <a href="/admin/plots/${plot.id}/edit" class="btn btn-warning">Edit Plot</a>
                        </div>
                    </div>
                </div>
            `;
        },

        // Create plot at clicked location
        createPlotAtLocation(latLng) {
            const lat = latLng.lat();
            const lng = latLng.lng();
            window.location.href = `/admin/plots/create?lat=${lat}&lng=${lng}`;
        },

        // Utility methods
        resetPlotInfoPanel() {
            const panel = document.getElementById('plot-info-panel');
            if (panel) {
                panel.innerHTML = '<p class="text-muted">Click on a plot marker to view detailed information.</p>';
            }
        },

        // Plot selection functionality
        selectPlot(plot, marker) {
            // Clear previous selection
            this.clearSelection();
            
            // Set new selection
            this.selectedPlot = plot;
            this.selectedMarker = marker;
            
            // Change marker appearance to show selection
            marker.setIcon({
                url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                scaledSize: new google.maps.Size(40, 40)
            });
            
            // Update UI to show selection
            this.updateSelectionUI();
        },

        clearSelection() {
            if (this.selectedMarker) {
                // Reset marker to default appearance
                this.selectedMarker.setIcon({
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                });
            }
            
            this.selectedPlot = null;
            this.selectedMarker = null;
            this.updateSelectionUI();
        },

        updateSelectionUI() {
            // Update the plot group controls
            const addToGroupBtn = document.getElementById('add-to-group-btn');
            const addToGroupSelect = document.getElementById('add-to-group-filter');
            
            if (this.selectedPlot && addToGroupSelect.value) {
                addToGroupBtn.disabled = false;
            } else {
                addToGroupBtn.disabled = true;
            }
            
            // Show selection info
            const selectionInfo = document.querySelector('.plot-selection-info');
            if (selectionInfo) {
                if (this.selectedPlot) {
                    selectionInfo.innerHTML = `
                        <div class="alert alert-success alert-sm mb-2">
                            <i class="fas fa-check-circle"></i> Selected: ${this.selectedPlot.plot_name || 'Plot ' + this.selectedPlot.id}
                            <button type="button" class="close ml-2" onclick="window.GoogleMapsCore.clearSelection()">
                                <span>&times;</span>
                            </button>
                        </div>
                    `;
                } else {
                    selectionInfo.innerHTML = '<small class="text-muted">Click on a plot marker to select it</small>';
                }
            }
        },

        fitMapToBounds() {
            if (this.markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                this.markers.forEach(marker => bounds.extend(marker.getPosition()));
                this.map.fitBounds(bounds);
            }
        }
    };

    // Initialize map when Google Maps API loads
    function initMap() {
        try {
            window.GoogleMapsCore.init();
        } catch (error) {
            console.error('Failed to initialize Google Maps:', error);
            window.MapErrorHandler.showMapError('Failed to initialize Google Maps. Please refresh the page.');
        }
    }
</script>

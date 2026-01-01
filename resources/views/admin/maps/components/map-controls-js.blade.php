{{-- Map Controls JavaScript --}}
<script>
    window.MapControls = {
        init() {
            this.initAddressAutocomplete();
            this.initEventListeners();
        },

        // Initialize Google Places Autocomplete
        initAddressAutocomplete() {
            const addressInput = document.getElementById('add-plot-address');
            if (addressInput && google.maps.places) {
                const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                    types: ['address'],
                    componentRestrictions: { country: 'us' }
                });

                autocomplete.addListener('place_changed', () => {
                    this.handlePlaceSelection(autocomplete.getPlace());
                });
            }
        },

        // Handle place selection from autocomplete
        handlePlaceSelection(place) {
            if (!place.geometry) {
                console.warn('No geometry found for place:', place.name);
                return;
            }

            // Zoom to selected place
            if (place.geometry.viewport) {
                window.GoogleMapsCore.map.fitBounds(place.geometry.viewport);
            } else {
                window.GoogleMapsCore.map.setCenter(place.geometry.location);
                window.GoogleMapsCore.map.setZoom(17);
            }

            // Add temporary marker
            this.addTemporaryMarker(place);

            // Store place data
            window.GoogleMapsCore.pendingPlotAddress = place.formatted_address;
            window.GoogleMapsCore.selectedPlaceCoordinates = {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng()
            };

            this.updateUIForSelectedPlace(place);
        },

        // Add temporary marker for selected place
        addTemporaryMarker(place) {
            const tempMarker = new google.maps.Marker({
                position: place.geometry.location,
                map: window.GoogleMapsCore.map,
                title: 'Selected: ' + (place.name || place.formatted_address),
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });

            window.GoogleMapsCore.selectedPlaceMarker = tempMarker;
        },

        // Update UI for selected place
        updateUIForSelectedPlace(place) {
            const plotInfoPanel = document.getElementById('plot-info-panel');
            if (plotInfoPanel) {
                plotInfoPanel.innerHTML = `
                    <div class="alert alert-info">
                        <h5><i class="fas fa-map-marker-alt text-primary"></i> Address Selected</h5>
                        <p><strong>Address:</strong> ${place.formatted_address}</p>
                        <p><strong>Coordinates:</strong> ${place.geometry.location.lat().toFixed(6)}, ${place.geometry.location.lng().toFixed(6)}</p>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="MapActions.createPlotFromSelectedPlace()">
                                <i class="fas fa-plus"></i> Create Plot Here
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="MapActions.clearSelectedPlace()">
                                <i class="fas fa-times"></i> Clear Selection
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i> You can also click "Add Plot" button or click directly on the map to create the plot.
                        </small>
                    </div>
                `;
            }

            // Update Add Plot button
            const addPlotBtn = document.getElementById('add-plot-btn');
            if (addPlotBtn) {
                addPlotBtn.classList.add('btn-warning');
                addPlotBtn.classList.remove('btn-success');
                addPlotBtn.innerHTML = '<i class="fas fa-plus"></i> Add Plot (Address Ready)';
            }
        },

        // Initialize all event listeners
        initEventListeners() {
            this.initAddPlotButton();
            this.initMacroClientControls();
            this.initMapOptionButtons();
            this.initPlotGroupControls();
            this.initQuickActions();
        },

        // Initialize add plot button
        initAddPlotButton() {
            const addPlotBtn = document.getElementById('add-plot-btn');
            if (addPlotBtn) {
                addPlotBtn.addEventListener('click', () => {
                    if (window.GoogleMapsCore.selectedPlaceCoordinates && window.GoogleMapsCore.pendingPlotAddress) {
                        window.MapActions.createPlotFromAddress(
                            window.GoogleMapsCore.pendingPlotAddress,
                            window.GoogleMapsCore.selectedPlaceCoordinates.lat,
                            window.GoogleMapsCore.selectedPlaceCoordinates.lng
                        );
                        return;
                    }

                    const address = document.getElementById('add-plot-address').value.trim();
                    if (!address) {
                        alert('Please enter an address or select one from the autocomplete suggestions');
                        return;
                    }

                    window.MapActions.geocodeAndCreatePlot(address);
                });
            }
        },

        // Initialize macro client controls
        initMacroClientControls() {
            const loadClientPlotsBtn = document.getElementById('load-client-plots-btn');
            if (loadClientPlotsBtn) {
                loadClientPlotsBtn.addEventListener('click', () => {
                    const macroClient = document.getElementById('macro-client-filter').value;
                    if (!macroClient) {
                        alert('Please select a macro client');
                        return;
                    }
                    window.MapActions.loadMacroClientPlots(macroClient, loadClientPlotsBtn);
                });
            }
        },

        // Initialize map option buttons
        initMapOptionButtons() {
            // Toggle plots button
            const togglePlotsBtn = document.getElementById('toggle-plots');
            if (togglePlotsBtn) {
                togglePlotsBtn.addEventListener('click', () => {
                    window.MapActions.togglePlots(togglePlotsBtn);
                });
            }

            // Center map button
            const centerMapBtn = document.getElementById('center-map');
            if (centerMapBtn) {
                centerMapBtn.addEventListener('click', () => {
                    window.GoogleMapsCore.fitMapToBounds();
                });
            }

            // Refresh plots button
            const refreshPlotsBtn = document.getElementById('refresh-plots');
            if (refreshPlotsBtn) {
                refreshPlotsBtn.addEventListener('click', () => {
                    window.location.reload();
                });
            }

            // Clear filters button
            const clearFiltersBtn = document.getElementById('clear-filters');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', () => {
                    window.MapActions.clearFilters();
                });
            }
        },

        // Initialize plot group controls
        initPlotGroupControls() {
            // Load group plots
            const loadGroupPlotsBtn = document.getElementById('load-group-plots-btn');
            if (loadGroupPlotsBtn) {
                loadGroupPlotsBtn.addEventListener('click', () => {
                    const plotGroupId = document.getElementById('plot-group-filter').value;
                    if (!plotGroupId) {
                        alert('Please select a plot group');
                        return;
                    }
                    window.MapActions.loadPlotGroup(plotGroupId, loadGroupPlotsBtn);
                });
            }

            // Add to group controls
            this.initAddToGroupControls();

            // Clear group selection
            const clearGroupBtn = document.getElementById('clear-group-selection');
            if (clearGroupBtn) {
                clearGroupBtn.addEventListener('click', () => {
                    window.MapActions.clearGroupSelection();
                });
            }
        },

        // Initialize add to group controls
        initAddToGroupControls() {
            const addToGroupBtn = document.getElementById('add-to-group-btn');
            const addressInput = document.getElementById('add-plot-address');
            const groupSelect = document.getElementById('add-to-group-filter');

            if (addToGroupBtn && addressInput && groupSelect) {
                const toggleButton = () => {
                    const hasAddress = addressInput.value.trim() || 
                        (window.GoogleMapsCore.selectedPlaceCoordinates && window.GoogleMapsCore.pendingPlotAddress);
                    const hasGroup = groupSelect.value;
                    addToGroupBtn.disabled = !(hasAddress && hasGroup);
                };

                addressInput.addEventListener('input', toggleButton);
                groupSelect.addEventListener('change', toggleButton);

                addToGroupBtn.addEventListener('click', () => {
                    const groupId = groupSelect.value;
                    if (!groupId) {
                        alert('Please select a plot group');
                        return;
                    }
                    window.MapActions.addPlotToGroup(groupId);
                });
            }
        },

        // Initialize quick actions
        initQuickActions() {
            // Additional quick action handlers can be added here
        }
    };
</script>

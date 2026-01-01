{{-- Map Actions JavaScript --}}
<script>
    window.MapActions = {
        // Create plot from address
        createPlotFromAddress(address, lat, lng) {
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
                    window.GoogleMapsCore.addMarker(data.plot);
                    this.clearSelectedPlace();
                } else {
                    alert('Error creating plot: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating plot. Please try again.');
            });
        },

        // Geocode address and create plot
        geocodeAndCreatePlot(address) {
            window.GoogleMapsCore.geocoder.geocode({ address: address }, (results, status) => {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    window.GoogleMapsCore.map.setCenter(location);
                    window.GoogleMapsCore.map.setZoom(15);

                    window.GoogleMapsCore.pendingPlotAddress = address;

                    const tempMarker = new google.maps.Marker({
                        position: location,
                        map: window.GoogleMapsCore.map,
                        title: 'Click to create plot here',
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(32, 32)
                        }
                    });

                    alert('Address found! Click on the map to create the plot at the exact location.');

                    const listener = window.GoogleMapsCore.map.addListener('click', (event) => {
                        this.createPlotFromAddress(window.GoogleMapsCore.pendingPlotAddress, event.latLng.lat(), event.latLng.lng());
                        tempMarker.setMap(null);
                        google.maps.event.removeListener(listener);
                        window.GoogleMapsCore.pendingPlotAddress = null;
                    });

                    setTimeout(() => {
                        tempMarker.setMap(null);
                        if (listener) google.maps.event.removeListener(listener);
                        window.GoogleMapsCore.pendingPlotAddress = null;
                    }, 30000);

                } else {
                    alert('Address not found. Please try a different address.');
                }
            });
        },

        // Load macro client plots
        loadMacroClientPlots(macroClient, button) {
            this.setButtonLoading(button, true);

            fetch(`/admin/maps/macro-client/plots?macro_client=${encodeURIComponent(macroClient)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.GoogleMapsCore.clearMarkers();

                        if (data.plots && data.plots.length > 0) {
                            data.plots.forEach(plot => window.GoogleMapsCore.addMarker(plot));
                            window.GoogleMapsCore.fitMapToBounds();
                        }

                        this.displayMacroClientInfo(data);
                        this.addProjectAddressMarkers(data.project_addresses);
                    } else {
                        alert('Error loading macro client plots: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading macro client plots. Please try again.');
                })
                .finally(() => {
                    this.setButtonLoading(button, false);
                });
        },

        // Display macro client information
        displayMacroClientInfo(data) {
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
        },

        // Add project address markers
        addProjectAddressMarkers(projectAddresses) {
            if (!projectAddresses || projectAddresses.length === 0) return;

            projectAddresses.forEach(projectAddr => {
                if (projectAddr.address) {
                    window.GoogleMapsCore.geocoder.geocode({ address: projectAddr.address }, (results, status) => {
                        if (status === 'OK') {
                            const location = results[0].geometry.location;
                            const marker = new google.maps.Marker({
                                position: location,
                                map: window.GoogleMapsCore.map,
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

                            marker.addListener('click', () => {
                                window.GoogleMapsCore.infoWindow.setContent(infoContent);
                                window.GoogleMapsCore.infoWindow.open(window.GoogleMapsCore.map, marker);
                            });

                            window.GoogleMapsCore.markers.push(marker);
                        }
                    });
                }
            });
        },

        // Load plot group
        loadPlotGroup(plotGroupId, button) {
            this.setButtonLoading(button, true);

            fetch(`/admin/maps/plot-group/plots?plot_group_id=${plotGroupId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.GoogleMapsCore.clearMarkers();

                        if (data.plots && data.plots.length > 0) {
                            data.plots.forEach(plot => {
                                const marker = window.GoogleMapsCore.addMarker(plot);
                                if (data.plot_group.color && marker) {
                                    marker.setIcon({
                                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                                        scaledSize: new google.maps.Size(32, 32)
                                    });
                                }
                            });

                            window.GoogleMapsCore.fitMapToBounds();
                        }

                        this.displayPlotGroupInfo(data);
                    } else {
                        alert('Error loading plot group: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading plot group. Please try again.');
                })
                .finally(() => {
                    this.setButtonLoading(button, false);
                });
        },

        // Display plot group information
        displayPlotGroupInfo(data) {
            document.getElementById('plot-info-panel').innerHTML = `
                <h5><i class="fas fa-layer-group" style="color: ${data.plot_group.color || '#007bff'}"></i> Plot Group: ${data.plot_group.name}</h5>
                ${data.plot_group.description ? `<p class="text-muted">${data.plot_group.description}</p>` : ''}
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-info">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Plots</span>
                                <span class="info-box-number">${data.stats.total_plots}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-success">
                            <div class="info-box-content">
                                <span class="info-box-text">Mapped Plots</span>
                                <span class="info-box-number">${data.stats.mapped_plots}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-2">
                    Showing plots from the "${data.plot_group.name}" group
                </p>
            `;
        },

        // Add plot to group
        addPlotToGroup(groupId) {
            if (window.GoogleMapsCore.selectedPlaceCoordinates && window.GoogleMapsCore.pendingPlotAddress) {
                this.createPlotInGroup(
                    groupId, 
                    window.GoogleMapsCore.pendingPlotAddress, 
                    window.GoogleMapsCore.selectedPlaceCoordinates.lat, 
                    window.GoogleMapsCore.selectedPlaceCoordinates.lng
                );
            } else {
                const address = document.getElementById('add-plot-address').value.trim();
                if (!address) {
                    alert('Please enter an address');
                    return;
                }

                window.GoogleMapsCore.geocoder.geocode({ address: address }, (results, status) => {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;
                        this.createPlotInGroup(groupId, address, location.lat(), location.lng());
                    } else {
                        alert('Address not found. Please try a different address.');
                    }
                });
            }
        },

        // Create plot in group
        createPlotInGroup(groupId, address, lat, lng, plotName = null) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }

            const data = {
                plot_group_id: groupId,
                address: address,
                latitude: lat,
                longitude: lng,
                plot_name: plotName,
                _token: csrfToken.getAttribute('content')
            };

            fetch('/admin/maps/plot-group/create-plot', {
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
                    alert(`Plot created successfully and added to group "${data.plot_group.name}"!`);
                    window.GoogleMapsCore.addMarker(data.plot);
                    this.clearSelectedPlace();
                    
                    const addToGroupFilter = document.getElementById('add-to-group-filter');
                    if (addToGroupFilter) addToGroupFilter.value = '';
                    
                    const addToGroupBtn = document.getElementById('add-to-group-btn');
                    if (addToGroupBtn) addToGroupBtn.disabled = true;
                } else {
                    alert('Error creating plot: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating plot. Please try again.');
            });
        },

        // Toggle plots visibility
        togglePlots(button) {
            const visible = window.GoogleMapsCore.markers[0] && window.GoogleMapsCore.markers[0].getMap();
            window.GoogleMapsCore.markers.forEach(marker => {
                marker.setMap(visible ? null : window.GoogleMapsCore.map);
            });
            button.innerHTML = visible ?
                '<i class="fas fa-eye-slash"></i> Show Plots' :
                '<i class="fas fa-eye"></i> Hide Plots';
        },

        // Clear all filters
        clearFilters() {
            const macroClientFilter = document.getElementById('macro-client-filter');
            const addPlotAddress = document.getElementById('add-plot-address');
            
            if (macroClientFilter) macroClientFilter.value = '';
            if (addPlotAddress) addPlotAddress.value = '';

            window.GoogleMapsCore.clearMarkers();
            window.GoogleMapsCore.plotsData.forEach(plot => window.GoogleMapsCore.addMarker(plot));
            window.GoogleMapsCore.resetPlotInfoPanel();
            window.GoogleMapsCore.fitMapToBounds();
        },

        // Clear group selection
        clearGroupSelection() {
            const plotGroupFilter = document.getElementById('plot-group-filter');
            const addToGroupFilter = document.getElementById('add-to-group-filter');
            
            if (plotGroupFilter) plotGroupFilter.value = '';
            if (addToGroupFilter) addToGroupFilter.value = '';

            window.GoogleMapsCore.clearMarkers();
            window.GoogleMapsCore.plotsData.forEach(plot => window.GoogleMapsCore.addMarker(plot));
            window.GoogleMapsCore.resetPlotInfoPanel();
            window.GoogleMapsCore.fitMapToBounds();
        },

        // Clear selected place
        clearSelectedPlace() {
            if (window.GoogleMapsCore.selectedPlaceMarker) {
                window.GoogleMapsCore.selectedPlaceMarker.setMap(null);
                window.GoogleMapsCore.selectedPlaceMarker = null;
            }

            window.GoogleMapsCore.pendingPlotAddress = null;
            window.GoogleMapsCore.selectedPlaceCoordinates = null;

            const addressInput = document.getElementById('add-plot-address');
            if (addressInput) addressInput.value = '';

            const addPlotBtn = document.getElementById('add-plot-btn');
            if (addPlotBtn) {
                addPlotBtn.classList.remove('btn-warning');
                addPlotBtn.classList.add('btn-success');
                addPlotBtn.innerHTML = '<i class="fas fa-plus"></i> Add Plot';
            }

            window.GoogleMapsCore.resetPlotInfoPanel();
        },

        // Create plot from selected place
        createPlotFromSelectedPlace() {
            if (window.GoogleMapsCore.selectedPlaceCoordinates && window.GoogleMapsCore.pendingPlotAddress) {
                this.createPlotFromAddress(
                    window.GoogleMapsCore.pendingPlotAddress,
                    window.GoogleMapsCore.selectedPlaceCoordinates.lat,
                    window.GoogleMapsCore.selectedPlaceCoordinates.lng
                );
            } else {
                alert('No address selected. Please select an address first.');
            }
        },

        // Utility: Set button loading state
        setButtonLoading(button, loading) {
            if (loading) {
                button.originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                button.disabled = true;
            } else {
                button.innerHTML = button.originalHTML || button.innerHTML.replace('<i class="fas fa-spinner fa-spin"></i> Loading...', '');
                button.disabled = false;
            }
        }
    };
</script>

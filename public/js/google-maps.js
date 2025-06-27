class GooglePlotManager {
    constructor(config) {
        this.config = config;
        this.defaultCenter = { lat: 27.95, lng: -82.457 };
        this.defaultZoom = 12;
        this.map = null;
        this.markers = [];
        this.geocoder = new google.maps.Geocoder();

        // Store references to key form elements.
        this.formElements = {
            filterForm: document.getElementById("filter-form"),
            addressInput: document.getElementById("address-input"),
            addAddressBtn: document.getElementById("add-address-btn"),
            plotSelect: document.getElementById("plot-select"),
            macroClientSelect: document.getElementById("macro-client-select"),
        };

        // Initialize the manager.
        this.init();
    }

    // Main initialization function.
    async init() {
        this.initMapPage();
        this.initEnhancedFeatures(); // Initialize our new features

        try {
            const data = await this.getMapData();
            if (!data) {
                this.initMapView();
                console.log("No valid selection provided; skipping request.");
                return;
            }
            console.log("DataFetched:", data);
            this.initMapView(data);
            this.initSidebarView(data);
            // Optionally update markers if not done in initMapView:
            // this.updateMapWithData(data);
        } catch (error) {
            console.error("Error initializing map data:", error);
            this.initMapView();
        }
    }

    getMapData() {
        let url = "/admin/mapplots/load_addresses"; // adjust to your API endpoint

        console.log("formElements:", this.formElements);
        if (
            this.formElements.plotSelect &&
            this.formElements.plotSelect.value
        ) {
            const plotId = this.formElements.plotSelect.value;
            url += `?selectedPlotId=${plotId}`;
            console.log("Fetching data for plot:", plotId);
        } else if (
            this.formElements.macroClientSelect &&
            this.formElements.macroClientSelect.value
        ) {
            const macroClient = this.formElements.macroClientSelect.value;
            url += `?selectedMacroClient=${encodeURIComponent(macroClient)}`;
            console.log("Fetching data for macro client:", macroClient);
        } else {
            console.warn("No valid selection provided");
            return Promise.resolve(null);
        }

        return fetch(url)
            .then((response) => {
                if (!response.ok) throw new Error("Network error");
                return response.json();
            })
            .then((data) => {
                console.log("Received addresses:", data.addresses);
                return data;
            })
            .catch((error) => {
                console.error("Error fetching addresses:", error);
                throw error;
            });
    }

    // Set up the main page layout, reset filters, and attach filter events.
    initMapPage() {
        if (this.formElements.filterForm) {
            this.formElements.filterForm.reset();
        }

        const addAddressBtn = document.getElementById("add-address-btn");

        if (addAddressBtn) {
            addAddressBtn.addEventListener("click", () => {
                plotManager.addAddressToPlot();
            });
        }

        // Attach reset button listeners.
        const resetBtn = document.getElementById("reset-zoom");
        if (resetBtn) {
            resetBtn.addEventListener("click", () => this.resetZoom());
        }

        const resetBtnSidebar = document.getElementById("reset-zoom-sidebar");
        if (resetBtnSidebar) {
            resetBtnSidebar.addEventListener("click", () => this.resetZoom());
        }

        // Attach your filter events, autocomplete, etc.
        this.setupFilterEvent(
            this.formElements.plotSelect,
            "selectedPlotId",
            this.formElements.macroClientSelect,
            "macro_client"
        );

        this.setupFilterEvent(
            this.formElements.macroClientSelect,
            "macro_client",
            this.formElements.plotSelect,
            "selectedPlotId"
        );

        this.setupAutocomplete();
        console.log("Map page initialized and filters reset.");

        // Enhanced plot management features
        this.initEnhancedFeatures();
    }

    // Helper: Attach event listener to a dropdown.
    setupFilterEvent(dropdown, paramKey, oppositeDropdown, oppositeParamKey) {
        if (dropdown) {
            dropdown.addEventListener("change", (e) => {
                const value = e.target.value;
                // Clear the opposite dropdown.
                if (oppositeDropdown) {
                    oppositeDropdown.value = "";
                }
                const params = new URLSearchParams(window.location.search);
                if (value) {
                    params.set(paramKey, value);
                } else {
                    params.delete(paramKey);
                }
                // Remove the opposite filter.
                params.delete(oppositeParamKey);
                // Refresh the page with new parameters.
                window.location.search = params.toString();
            });
        }
    }

    // Helper: Initialize the sidebar view with or without data.
    initSidebarView(data) {
        // Initialize the sidebar with loader or default view.
        const sidebarElement = document.getElementById("address-list");
        sidebarElement.innerHTML = "";

        console.log("Initializing sidebar with data:", data);
        // Delegate to updateSidebarWithData for consistency.
        this.updateSidebarWithData(data);
    }

    // Helper: Initialize the map view with or without data.
    initMapView(data = null) {
        this.map = new google.maps.Map(document.getElementById("map"), {
            center: this.defaultCenter,
            zoom: this.defaultZoom,
        });

        if (data && data.addresses && data.addresses.length > 0) {
            console.log("Address data found. Processing addresses...");
            // Process addresses, e.g., add markers.
            this.updateMapWithData(data);
        } else {
            console.log("No address data available. Showing default view.");
            // Optionally, add default UI elements or instructions.
        }
        console.log("Map view initialized.");
    }

    // Standardize addresses and geocode if necessary.
    async standardizeAddresses(addresses) {
        const geocodeAddress = (address) => {
            return new Promise((resolve, reject) => {
                if (address.latitude && address.longitude) {
                    return resolve(address); // Already has coordinates, skip geocoding
                }

                setTimeout(() => {
                    // Delay to prevent hitting API limits
                    this.geocoder.geocode(
                        { address: address.address },
                        (results, status) => {
                            if (
                                status === google.maps.GeocoderStatus.OK &&
                                results.length
                            ) {
                                address.latitude =
                                    results[0].geometry.location.lat();
                                address.longitude =
                                    results[0].geometry.location.lng();
                                address.location_name =
                                    address.location_name ||
                                    results[0].formatted_address;
                                resolve(address);
                            } else {
                                console.warn(
                                    "Geocoding failed for",
                                    address.address,
                                    "Status:",
                                    status
                                );
                                resolve(address); // Continue processing even if geocode fails
                            }
                        }
                    );
                }, 300); // 300ms delay per request
            });
        };

        // **Throttling: Process addresses in batches of 5**
        const results = [];
        for (let i = 0; i < addresses.length; i += 5) {
            const batch = addresses.slice(i, i + 5); // Take 5 addresses at a time
            const batchResults = await Promise.all(batch.map(geocodeAddress));
            results.push(...batchResults);
        }

        return results;
    }

    // Then update your updateMapWithData method:
    updateMapWithData(data) {
        let addresses = [];
        if (Array.isArray(data)) {
            addresses = data;
        } else if (data && Array.isArray(data.addresses)) {
            addresses = data.addresses;
        } else {
            console.error("Invalid data format", data);
            return;
        }

        const bounds = new google.maps.LatLngBounds();

        addresses.forEach((address) => {
            // Check if valid coordinates exist.
            if (
                typeof address.latitude === "number" &&
                typeof address.longitude === "number"
            ) {
                // Coordinates are available—add marker directly.
                const marker = new google.maps.Marker({
                    position: { lat: address.latitude, lng: address.longitude },
                    map: this.map,
                    title: address.location_name,
                });
                this.markers.push(marker);
                bounds.extend(marker.getPosition());
            } else {
                // Missing coordinates: geocode the address.
                this.geocoder.geocode(
                    { address: address.address },
                    (results, status) => {
                        if (status === "OK" && results.length > 0) {
                            address.latitude =
                                results[0].geometry.location.lat();
                            address.longitude =
                                results[0].geometry.location.lng();
                            const marker = new google.maps.Marker({
                                position: {
                                    lat: address.latitude,
                                    lng: address.longitude,
                                },
                                map: this.map,
                                title: address.location_name,
                            });
                            this.markers.push(marker);
                            bounds.extend(marker.getPosition());
                            // Optionally update the sidebar or cache the coordinates.
                        } else {
                            console.error(
                                "Geocoding failed for",
                                address.address,
                                status
                            );
                        }
                    }
                );
            }
        });

        // Adjust map bounds after a slight delay to account for async geocoding.
        setTimeout(() => {
            if (!bounds.isEmpty()) {
                this.map.fitBounds(bounds);
            }
        }, 1000);
    }

    // Call the API endpoint, then update map markers and sidebar.
    callApiEndpoint() {
        console.log("API endpoint call initiated.");
        // Show loaders for both map and sidebar.
        const mapContainer = document.getElementById("map");
        const sidebarContainer = document.getElementById("address-list");
        this.showLoader(mapContainer, "map-loader");
        this.showLoader(sidebarContainer, "sidebar-loader");

        fetch("/admin/mapplots/load_addresses")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                this.updateMapWithData(data);
                this.updateSidebarWithData(data);
            })
            .catch((error) => console.error("Error fetching data:", error));
    }

    /**
     * Set up autocomplete for address input.
     * When a place is selected, a temporary marker is added to the map.
     */
    setupAutocomplete() {
        const addressInput = document.getElementById("address-input");
        if (!addressInput) return;
        addressInput.value = "";

        const autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ["geocode"],
        });

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();

            if (!place.geometry || !place.geometry.location) {
                console.log(`No details available for: '${place.name}'`);
                this.selectedPlace = null;
                return;
            }

            const location = place.geometry.location;
            let lat, lng;
            if (
                typeof location.lat === "function" &&
                typeof location.lng === "function"
            ) {
                lat = location.lat();
                lng = location.lng();
            } else if (
                typeof location.lat === "number" &&
                typeof location.lng === "number"
            ) {
                lat = location.lat;
                lng = location.lng;
            }

            if (
                typeof lat === "number" &&
                !isNaN(lat) &&
                typeof lng === "number" &&
                !isNaN(lng)
            ) {
                this.selectedPlace = place;
                this.clearTempMarker();
                this.addTempMarker({ lat, lng });
                this.map.setCenter({ lat, lng });
                this.map.setZoom(15);
            } else {
                console.warn(
                    "Invalid coordinates for selected place:",
                    location
                );
            }
        });
    }

    /**
     * Show the selected place on the map.
     */
    showPlotOnMap(address) {
        console.log("ClickedAddress:", address);
        // Optionally, zoom to the marker or open an info window.
        if (
            this.map &&
            typeof address.latitude === "number" &&
            isFinite(address.latitude) &&
            typeof address.longitude === "number" &&
            isFinite(address.longitude)
        ) {
            this.map.setCenter({
                lat: address.latitude,
                lng: address.longitude,
            });
            this.map.setZoom(15);
        } else {
            console.warn("Invalid coordinates for address:", address);
            alert(
                "Cannot show this address on the map due to invalid coordinates."
            );
        }
    }

    /**
     * Delete address from the map.
     */
    deleteAddress(addressId) {
        if (!confirm("Are you sure you want to delete this address?")) {
            return;
        }

        const deleteUrl = this.config.deleteAddressUrl.replace(
            ":id",
            addressId
        );

        fetch(deleteUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.config.csrfToken,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.showNotification(
                        "Address deleted successfully!",
                        "success"
                    );
                    // Remove the address from the list
                    const addressElement = document.querySelector(
                        `[data-address-id="${addressId}"]`
                    );
                    if (addressElement) {
                        addressElement.remove();
                    }
                    // Remove marker from map
                    const markerIndex = this.markers.findIndex(
                        (marker) => marker.addressData?.id === addressId
                    );
                    if (markerIndex > -1) {
                        this.markers[markerIndex].setMap(null);
                        this.markers.splice(markerIndex, 1);
                    }
                    // Update counter
                    this.updateMarkerCount(this.markers.length);
                } else {
                    this.showNotification(
                        "Failed to delete address: " +
                            (data.message || "Unknown error"),
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Delete error:", error);
                this.showNotification(
                    "Failed to delete address: " + error.message,
                    "error"
                );
            });
    }

    /**
     * Clears the temporary marker if it exists.
     */
    clearTempMarker() {
        if (this.tempMarker) {
            this.tempMarker.setMap(null);
            this.tempMarker = null;
        }
    }

    /**
     * Adds a marker to the map at the specified location.
     */
    addMarkerToMap(address) {
        console.log("Adding marker for:", address);
        const marker = new google.maps.Marker({
            position: { lat: address.latitude, lng: address.longitude },
            map: this.map,
            title: address.location_name,
        });

        this.markers.push(marker);
    }

    /**
     * Adds a temporary marker (blue) to the map at the specified location.
     */
    addTempMarker(location) {
        this.tempMarker = new google.maps.Marker({
            map: this.map,
            position: location,
            icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png", // Blue marker for autocomplete
        });
    }

    addAddressToPlot() {
        const addressInput = document.getElementById("address-input");
        const plotSelect = document.getElementById("plot-select");

        if (!addressInput || !plotSelect) {
            console.error("Address input or plot select not found.");
            alert("Please select a plot and enter an address.");
            return;
        }

        const address = addressInput.value.trim();
        const plotId = plotSelect.value;

        if (!address || !plotId) {
            alert("Both address and plot must be selected.");
            return;
        }

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address }, (results, status) => {
            if (status !== "OK" || !results.length) {
                alert("Geocoding failed. Please enter a valid address.");
                return;
            }

            const location = results[0].geometry.location;
            const lat = location.lat();
            const lng = location.lng();

            fetch(window.MapPlotConfig.addAddressUrl, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": window.MapPlotConfig.csrfToken,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    plot_id: plotId,
                    address: address,
                    lat: lat,
                    lng: lng,
                }),
            })
                .then((response) => {
                    if (!response.ok) throw new Error("Failed to add address.");
                    return response.json();
                })
                .then((data) => {
                    window.location.reload();
                })
                .catch((error) => {
                    console.error("Error adding address:", error);
                    alert("Failed to add address. Please try again.");
                });
        });
    }

    updateSidebarWithData(data) {
        this.hideLoader("sidebar-loader");
        const sidebarElement = document.getElementById("address-list");
        sidebarElement.innerHTML = "";

        const addresses = Array.isArray(data) ? data : data?.addresses || [];

        if (addresses.length === 0) {
            document.getElementById("empty-state").style.display = "block";
            return;
        } else {
            document.getElementById("empty-state").style.display = "none";
        }

        // Update marker count
        this.updateMarkerCount(addresses.length);

        addresses.forEach((item) => {
            console.log("Processing item:", item);
            const li = document.createElement("li");

            li.className = "list-group-item address-item";
            li.style.cursor = "pointer";
            li.dataset.addressId = item.id;
            li.dataset.type = item.type || "unknown"; // For filtering

            const coordinates =
                typeof item.latitude === "number" &&
                typeof item.longitude === "number"
                    ? `${item.latitude.toFixed(4)}, ${item.longitude.toFixed(
                          4
                      )}`
                    : "Coordinates pending";

            // Determine if this is a custom plot address (can be deleted)
            const isCustomPlotAddress = item.type === "plot";

            // Get type badge
            const typeBadge = this.getTypeBadge(item.type, item.property_type);

            // Get status indicator
            const statusIndicator = this.getStatusIndicator(item.status);

            li.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 text-dark">
                            ${item.location_name || "Address"}
                            ${typeBadge}
                        </h6>
                        <p class="mb-1 text-muted small">${item.address}</p>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <small class="text-secondary">
                                <i class="fa fa-map-marker-alt"></i> ${coordinates}
                            </small>
                            ${statusIndicator}
                            ${
                                item.property_value
                                    ? `<small class="text-success"><i class="fa fa-dollar-sign"></i> ${item.property_value}</small>`
                                    : ""
                            }
                        </div>
                        ${
                            item.description
                                ? `<small class="text-info mt-1 d-block"><i class="fa fa-info-circle"></i> ${item.description}</small>`
                                : ""
                        }
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <button class="btn btn-sm btn-outline-primary show-map-btn" title="Center on map">
                            <i class="fa fa-crosshairs"></i>
                        </button>
                        ${
                            isCustomPlotAddress
                                ? `
                            <button class="btn btn-sm btn-outline-danger delete-btn" title="Delete address">
                                <i class="fa fa-trash"></i>
                            </button>
                        `
                                : ""
                        }
                    </div>
                </div>
            `;

            // Attach event handlers
            li.querySelector(".show-map-btn")?.addEventListener(
                "click",
                (e) => {
                    e.stopPropagation();
                    this.showPlotOnMap(item);
                }
            );

            if (isCustomPlotAddress) {
                li.querySelector(".delete-btn")?.addEventListener(
                    "click",
                    (e) => {
                        e.stopPropagation();
                        this.deleteAddress(item.id);
                    }
                );
            }

            // Click on item to center on map
            li.addEventListener("click", () => {
                this.showPlotOnMap(item);
                // Add selected class
                document
                    .querySelectorAll(".address-item")
                    .forEach((el) => el.classList.remove("selected"));
                li.classList.add("selected");
            });

            sidebarElement.appendChild(li);
        });
    }

    getTypeBadge(type, propertyType) {
        let badgeClass = "secondary";
        let badgeText = "Unknown";

        switch (type) {
            case "plot":
                badgeClass = "success";
                badgeText = "Custom";
                break;
            case "macro":
                badgeClass = "info";
                badgeText = "Existing";
                break;
        }

        if (propertyType) {
            badgeText += ` (${propertyType})`;
        }

        return `<span class="badge bg-${badgeClass} ms-2">${badgeText}</span>`;
    }

    getStatusIndicator(status) {
        if (!status) return "";

        let iconClass = "fa-circle";
        let textClass = "text-secondary";

        switch (status) {
            case "active":
                iconClass = "fa-check-circle";
                textClass = "text-success";
                break;
            case "pending":
                iconClass = "fa-clock";
                textClass = "text-warning";
                break;
            case "sold":
                iconClass = "fa-handshake";
                textClass = "text-info";
                break;
            case "inactive":
                iconClass = "fa-times-circle";
                textClass = "text-danger";
                break;
        }

        return `<small class="${textClass}"><i class="fa ${iconClass}"></i> ${
            status.charAt(0).toUpperCase() + status.slice(1)
        }</small>`;
    }

    /**
     * Zoom to an address
     */
    zoomToAddress(lat, lng) {
        const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
        this.map.setCenter(position);
        this.map.setZoom(15);
    }

    // Method to reset the map view to show all markers (properties)
    resetZoom() {
        if (this.markers.length === 0) {
            // Fallback to default view if no markers are present.
            this.map.setCenter(this.defaultCenter);
            this.map.setZoom(this.defaultZoom);
            return;
        }
        const bounds = new google.maps.LatLngBounds();
        this.markers.forEach((marker) => {
            bounds.extend(marker.getPosition());
        });
        this.map.fitBounds(bounds);
        console.log("Map view reset to show all properties.");
    }

    // Helper: Show a loader element within a container.
    showLoader(targetElement, loaderId) {
        if (!targetElement) return;
        // Create a loader element.
        const loader = document.createElement("div");
        loader.id = loaderId;
        loader.className = "d-flex justify-content-center align-items-center";
        loader.style.height = "400px";
        loader.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        `;
        targetElement.appendChild(loader);
        console.log(`Loader with id ${loaderId} added to the DOM.`); // Debugging
    }

    // Helper: Hide the loader element.
    hideLoader(loaderId) {
        const loader = document.getElementById(loaderId);
        if (loader) {
            loader.remove();
            console.log(`Loader with id ${loaderId} removed from the DOM.`);
        } else {
            console.warn(
                `Loader with id ${loaderId} not found in the DOM. Skipping removal.`
            );
        }
    }

    // Enhanced plot management features
    initEnhancedFeatures() {
        // Address type filtering
        const filterButtons = document.querySelectorAll(
            'input[name="address-filter"]'
        );
        filterButtons.forEach((button) => {
            button.addEventListener("change", (e) => {
                this.filterAddressesByType(e.target.value);
            });
        });

        // Export functionality (removed from sidebar, can be added elsewhere if needed)
        const exportBtn = document.getElementById("export-data");
        if (exportBtn) {
            exportBtn.addEventListener("click", () => {
                this.exportPlotData();
            });
        }

        // Map control buttons
        const toggleSatelliteBtn = document.getElementById("toggle-satellite");
        if (toggleSatelliteBtn) {
            toggleSatelliteBtn.addEventListener("click", () => {
                this.toggleMapType();
            });
        }

        const clusterBtn = document.getElementById("cluster-markers");
        if (clusterBtn) {
            clusterBtn.addEventListener("click", () => {
                this.toggleMarkerClustering();
            });
        }

        // Edit plot functionality
        const editPlotBtn = document.getElementById("edit-plot-btn");
        if (editPlotBtn) {
            editPlotBtn.addEventListener("click", () => {
                this.openEditPlotModal();
            });
        }

        // Convert to client functionality
        const convertBtn = document.getElementById("convert-to-client-btn");
        if (convertBtn) {
            convertBtn.addEventListener("click", () => {
                this.openConvertToClientModal();
            });
        }

        // Load statistics
        this.loadPlotStatistics();
    }

    filterAddressesByType(type) {
        const addressItems = document.querySelectorAll(
            "#address-list .list-group-item"
        );
        let visibleCount = 0;

        addressItems.forEach((item) => {
            const addressType = item.dataset.type;
            const shouldShow = type === "all" || addressType === type;

            item.style.display = shouldShow ? "block" : "none";
            if (shouldShow) visibleCount++;
        });

        // Update marker visibility on map
        this.markers.forEach((marker) => {
            const markerType = marker.addressData?.type;
            const shouldShow = type === "all" || markerType === type;
            marker.setVisible(shouldShow);
        });

        // Update counter
        this.updateMarkerCount(visibleCount);
    }

    updateMarkerCount(count) {
        const counterElement = document.getElementById("marker-count");
        if (counterElement) {
            counterElement.textContent = count;
        }
    }

    exportPlotData() {
        const selectedPlotId = this.getSelectedPlotId();
        if (!selectedPlotId) {
            this.showNotification("Please select a plot to export", "warning");
            return;
        }

        fetch(`/admin/mapplots/${selectedPlotId}/export`)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Download the data as JSON
                    const blob = new Blob(
                        [JSON.stringify(data.data, null, 2)],
                        {
                            type: "application/json",
                        }
                    );
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = data.filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);

                    this.showNotification(
                        "Plot data exported successfully!",
                        "success"
                    );
                } else {
                    this.showNotification(
                        "Failed to export plot data",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Export error:", error);
                this.showNotification(
                    "Export failed: " + error.message,
                    "error"
                );
            });
    }

    toggleMapType() {
        if (!this.map) return;

        const currentType = this.map.getMapTypeId();
        const newType = currentType === "satellite" ? "roadmap" : "satellite";
        this.map.setMapTypeId(newType);

        const btn = document.getElementById("toggle-satellite");
        if (btn) {
            btn.innerHTML =
                newType === "satellite"
                    ? '<i class="fa fa-road"></i> Road View'
                    : '<i class="fa fa-satellite"></i> Satellite';
        }
    }

    toggleMarkerClustering() {
        // Implement marker clustering toggle
        if (this.markerCluster) {
            this.markerCluster.clearMarkers();
            this.markerCluster = null;
            this.markers.forEach((marker) => marker.setMap(this.map));
        } else {
            this.enableMarkerClustering();
        }
    }

    enableMarkerClustering() {
        // Note: This requires the MarkerClusterer library
        if (typeof MarkerClusterer !== "undefined" && this.markers.length > 0) {
            this.markerCluster = new MarkerClusterer(this.map, this.markers, {
                imagePath:
                    "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
            });
        }
    }

    loadPlotStatistics() {
        fetch("/admin/mapplots/statistics")
            .then((response) => response.json())
            .then((stats) => {
                this.updateStatisticElements(stats);
            })
            .catch((error) => {
                console.error("Failed to load statistics:", error);
            });
    }

    updateStatisticElements(stats) {
        const elements = {
            "stat-total-plots": stats.total_plots,
            "stat-custom-plots": stats.plots_by_type.custom,
            "stat-prospect-plots": stats.plots_by_type.prospect,
            "stat-client-plots": stats.plots_by_type.client,
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || 0;
            }
        });
    }

    showNotification(message, type = "info") {
        // Create a simple notification system
        const notification = document.createElement("div");
        notification.className = `alert alert-${
            type === "error" ? "danger" : type
        } alert-dismissible fade show position-fixed`;
        notification.style.cssText =
            "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    getSelectedPlotId() {
        return this.formElements.plotSelect
            ? this.formElements.plotSelect.value
            : null;
    }

    // ...existing code...
}

// Helper to initialize the manager after Google Maps API is loaded.
function initializePlotManager() {
    try {
        const config = window.MapPlotConfig || {};
        window.plotManager = new GooglePlotManager(config);
    } catch (e) {
        console.error("Error initializing map:", e);
        showMapError(
            "Initialization Error",
            "Failed to initialize the map system: " + e.message
        );
    }
}

// Global callback function for Google Maps API
window.initializeGoogleMaps = function () {
    console.log("Google Maps API loaded successfully");
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializePlotManager);
    } else {
        initializePlotManager();
    }
};

// Wait for both DOM and Google Maps API to be ready (fallback method).
function waitForGoogleMapsAndInit() {
    if (typeof google !== "undefined" && google.maps) {
        initializePlotManager();
    } else {
        setTimeout(waitForGoogleMapsAndInit, 400);
    }
}

// Use the callback method if available, otherwise use polling
if (!window.MapPlotConfig || window.MapPlotConfig.mapsEnabled !== false) {
    document.addEventListener("DOMContentLoaded", function () {
        // If Google Maps hasn't loaded in 10 seconds, show error
        setTimeout(function () {
            if (typeof google === "undefined" || !google.maps) {
                showMapError(
                    "Loading Timeout",
                    "Google Maps API failed to load within the expected time."
                );
            }
        }, 10000);
    });
} else {
    // Maps are disabled, show appropriate message
    document.addEventListener("DOMContentLoaded", function () {
        showMapError(
            "Maps Disabled",
            "Google Maps is currently disabled in the configuration."
        );
    });
}

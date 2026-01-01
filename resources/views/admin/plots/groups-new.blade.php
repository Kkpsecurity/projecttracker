@extends('adminlte::page')

@section('title', 'Group Plots - Project Tracker')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-layer-group"></i> Group Plots</h1>
        <div>
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
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_groups'] ?? 0 }}</h3>
                    <p>Unassigned Groups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_unassigned_plots'] ?? 0 }}</h3>
                    <p>Unassigned Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['orphaned_plots'] ?? 0 }}</h3>
                    <p>Orphaned Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['ready_for_grouping'] ?? 0 }}</h3>
                    <p>Ready for Grouping</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            </h3>
        </div>
        <div class="card-body">
            {{-- Group Filter Row --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="group-filter">Filter by Group:</label>
                        <select id="group-filter" class="form-control">
                            <option value="">All Groups</option>
                            @foreach($plotGroups ?? [] as $group)
                                <option value="{{ $group->id }}" data-color="{{ $group->color ?? '#3498db' }}">
                                    {{ $group->name }} ({{ $group->plots_count ?? 0 }} plots)
                                </option>
                            @endforeach
                            <option value="unassigned">Unassigned Plots Only</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Group Actions:</label>
                        <div class="btn-group d-block">
                            <button type="button" class="btn btn-primary btn-sm" id="create-new-group-btn"  data-toggle="modal" data-target="#createGroupModal">
                                <i class="fas fa-plus"></i> Create Group
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="add-to-group-btn" disabled data-toggle="modal" data-target="#addToGroupModal">
                                <i class="fas fa-plus-circle"></i> Add to Group
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="remove-from-group-btn" disabled>
                                <i class="fas fa-minus-circle"></i> Remove from Group
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="delete-group-btn" disabled>
                                <i class="fas fa-trash"></i> Delete Group
                            </button>
                        </div>
                    </div>
                </div>
            </div>
                
            {{-- Tab Navigation --}}
            <ul class="nav nav-tabs" id="groupPlotsTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="table-tab" data-toggle="tab" href="#table-view" role="tab">
                        <i class="fas fa-table"></i> Table View
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="map-tab" data-toggle="tab" href="#map-view" role="tab">
                        <i class="fas fa-map"></i> Map View
                    </a>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content" id="groupPlotsTabContent">
                {{-- Table View Tab --}}
                <div class="tab-pane fade show active" id="table-view" role="tabpanel">
                    <div class="mt-3">
                        <div class="table-responsive">
                            <table id="group-plots-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="40px">
                                            <input type="checkbox" id="select-all-plots">
                                        </th>
                                        <th>ID</th>
                                        <th>Plot Name</th>
                                        <th>Address</th>
                                        <th>Group</th>
                                        <th>Status</th>
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
                        <div id="group-plots-map" style="height: 600px; width: 100%;"></div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Click on plot markers to select them. Selected plots will be highlighted.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create New Group Modal --}}
    <div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="createGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">
                        <i class="fas fa-plus-circle"></i> Create New Plot Group
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="create-group-form">
                        @csrf
                        <div class="form-group">
                            <label for="group-name">Group Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="group-name" name="name" required 
                                   placeholder="Enter group name...">
                            <small class="form-text text-muted">Choose a descriptive name for this plot group</small>
                        </div>
                        <div class="form-group">
                            <label for="group-description">Description</label>
                            <textarea class="form-control" id="group-description" name="description" rows="3" 
                                      placeholder="Optional description..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="group-color">Group Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control" id="group-color" name="color" value="#3498db">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="random-color-btn">
                                        <i class="fas fa-dice"></i> Random
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-group-btn">
                        <i class="fas fa-save"></i> Create Group
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add to Group Modal --}}
    <div class="modal fade" id="addToGroupModal" tabindex="-1" role="dialog" aria-labelledby="addToGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addToGroupModalLabel">
                        <i class="fas fa-plus-circle"></i> Add Plots to Group
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="google-address-search">Add New Address</label>
                        <div class="input-group">
                            <input type="text" id="google-address-search" class="form-control" 
                                   placeholder="Start typing an address...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-success" id="add-new-address-btn" disabled>
                                    <i class="fas fa-plus"></i> Add Plot
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Google Places will auto-complete as you type
                            <br><small style="color: #666;">
                                <strong>Debug:</strong> 
                                <a href="#" onclick="testAutocomplete(); return false;">Test Autocomplete</a> | 
                                <a href="#" onclick="console.log('Selected data:', window.selectedPlaceData); return false;">Check Data</a>
                            </small>
                        </small>
                    </div>
                    
                    <!-- Map Preview Section -->
                    <div class="form-group" id="address-preview-section" style="display: none;">
                        <label>Location Preview</label>
                        <div class="card">
                            <div class="card-body p-2">
                                <div id="address-preview-map" style="height: 250px; width: 100%; border-radius: 4px;"></div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt text-danger"></i> 
                                        <span id="selected-address-display">No address selected</span>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-crosshairs"></i> 
                                        Coordinates: <span id="selected-coordinates-display">-</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .plot-selected {
            background-color: #e3f2fd !important;
        }
        .group-filter-active {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #007bff;
            font-weight: bold;
        }
        #group-plots-map {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        .selected-counter {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 200px;
        }
        
        /* Google Places Autocomplete styling fixes */
        .pac-container {
            z-index: 10001 !important;
            position: absolute !important;
        }
        
        /* Ensure input field is positioned correctly */
        #google-address-search {
            position: relative !important;
            z-index: 9999 !important;
        }
        
        /* Modal z-index adjustments */
        .modal {
            z-index: 10000 !important;
        }
        
        .modal-backdrop {
            z-index: 9999 !important;
        }
        
        /* Address Preview Map Styling */
        #address-preview-section {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        #address-preview-map {
            border: 2px solid #e9ecef;
            transition: border-color 0.2s ease;
        }
        
        #address-preview-map:hover {
            border-color: #007bff;
        }
        
        #address-preview-section .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: box-shadow 0.2s ease;
        }
        
        #address-preview-section .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        }
        .selected-counter .alert {
            margin-bottom: 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .plot-checkbox:checked {
            transform: scale(1.1);
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #007bff;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <!-- Google Maps API -->
    <script>
        // Add error handling for Google Maps API loading
        window.gm_authFailure = function() {
            console.error('Google Maps API authentication failed. Please check your API key.');
            alert('Google Maps API authentication failed. Please check your API key and try again.');
        };
        
        // Fallback if callback doesn't fire
        setTimeout(function() {
            if (typeof google === 'undefined') {
                console.error('Google Maps API failed to load after 10 seconds');
            }
        }, 10000);
    </script>
    <script async defer 
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY') }}&libraries=places&callback=initGoogleMaps&loading=async">
    </script>
    
    <script>
        // Global test function for debugging autocomplete
        function testAutocomplete() {
            console.log('=== AUTOCOMPLETE DEBUG TEST ===');
            console.log('Google object exists:', typeof google !== 'undefined');
            console.log('Google Maps exists:', typeof google !== 'undefined' && !!google.maps);
            console.log('Google Places exists:', typeof google !== 'undefined' && !!google.maps && !!google.maps.places);
            console.log('Autocomplete instance:', window.googleAutocomplete);
            console.log('Input element:', document.getElementById('google-address-search'));
            console.log('Selected place data:', window.selectedPlaceData);
            console.log('Add button disabled:', $('#add-new-address-btn').prop('disabled'));
            
            // Check z-index issues
            const input = document.getElementById('google-address-search');
            const modal = input ? input.closest('.modal') : null;
            const pacContainer = document.querySelector('.pac-container');
            
            console.log('Input z-index:', input ? window.getComputedStyle(input).zIndex : 'N/A');
            console.log('Modal z-index:', modal ? window.getComputedStyle(modal).zIndex : 'N/A');
            console.log('PAC container z-index:', pacContainer ? window.getComputedStyle(pacContainer).zIndex : 'N/A');
            console.log('PAC container exists:', !!pacContainer);
            
            // Test input focus and interaction
            if (input) {
                console.log('Testing input focus...');
                input.focus();
                input.value = 'test address';
                input.dispatchEvent(new Event('input'));
                
                setTimeout(() => {
                    const newPacContainer = document.querySelector('.pac-container');
                    console.log('After typing - PAC container exists:', !!newPacContainer);
                    if (newPacContainer) {
                        console.log('PAC container visible:', newPacContainer.style.display !== 'none');
                        console.log('PAC container children:', newPacContainer.children.length);
                    }
                }, 1000);
            }
            
            // Try to set test data
            window.selectedPlaceData = {
                formatted_address: '1600 Amphitheatre Parkway, Mountain View, CA, USA',
                latitude: 37.4224764,
                longitude: -122.0842499,
                place_id: 'ChIJ2eUgeAK6j4ARbn5u_wAGqWA',
                test_data: true
            };
            updateAddNewAddressButton();
            showAddressPreview(window.selectedPlaceData);
            console.log('Test data set. Button should now be enabled and map preview should show.');
        }
        
        // Global callback for Google Maps initialization
        function initGoogleMaps() {
            console.log('Google Maps API loaded successfully');
            console.log('Google object:', google);
            console.log('Google Maps:', google.maps);
            console.log('Google Places:', google.maps.places);
            
            // Initialize Google Places Autocomplete
            initGooglePlacesAutocomplete();
            
            // Initialize map if we're on the map tab
            if ($('#map-view').hasClass('show active')) {
                setTimeout(initializeMap, 100);
            }
        }
        
        // Initialize Google Places Autocomplete
        function initGooglePlacesAutocomplete() {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                console.log('Initializing Google Places Autocomplete...');
                
                const inputElement = document.getElementById('google-address-search');
                if (!inputElement) {
                    console.error('Google address search input not found');
                    return;
                }
                
                console.log('Input element found:', inputElement);
                console.log('Input element styles:', window.getComputedStyle(inputElement));
                
                // Set explicit z-index on input and its container
                inputElement.style.position = 'relative';
                inputElement.style.zIndex = '9999';
                
                // Check if input is in a modal and set modal z-index higher
                const modal = inputElement.closest('.modal');
                if (modal) {
                    console.log('Input is in modal, adjusting z-index');
                    modal.style.zIndex = '10000';
                }
                
                const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                    types: ['address'],
                    componentRestrictions: { country: 'us' },
                    fields: ['place_id', 'geometry', 'formatted_address', 'name', 'address_components']
                });
                
                console.log('Autocomplete object created:', autocomplete);
                
                // Set z-index for autocomplete dropdown
                setTimeout(() => {
                    const pacContainer = document.querySelector('.pac-container');
                    if (pacContainer) {
                        console.log('Setting z-index for .pac-container');
                        pacContainer.style.zIndex = '10001';
                    }
                }, 100);
                
                // Variables to store the selected place data
                let selectedPlaceData = null;
                
                autocomplete.addListener('place_changed', function() {
                    console.log('Place changed event fired');
                    const place = autocomplete.getPlace();
                    console.log('Selected place:', place);
                    
                    if (!place.geometry || !place.geometry.location) {
                        console.warn('No geometry data available for selected place');
                        alert('No location details available for the selected address. Please try a different address.');
                        selectedPlaceData = null;
                        window.selectedPlaceData = null;
                        updateAddNewAddressButton();
                        hideAddressPreview();
                        return;
                    }
                    
                    // Store all the place data in a variable
                    selectedPlaceData = {
                        formatted_address: place.formatted_address || place.name || inputElement.value,
                        latitude: place.geometry.location.lat(),
                        longitude: place.geometry.location.lng(),
                        place_id: place.place_id,
                        address_components: place.address_components || []
                    };
                    
                    // Extract address components for better data quality
                    let city = '';
                    let state = '';
                    let zipCode = '';
                    
                    if (place.address_components) {
                        place.address_components.forEach(component => {
                            const types = component.types;
                            if (types.includes('locality')) {
                                city = component.long_name;
                            } else if (types.includes('administrative_area_level_1')) {
                                state = component.short_name; // Use short name for states (e.g., "FL" instead of "Florida")
                            } else if (types.includes('postal_code')) {
                                zipCode = component.long_name;
                            }
                        });
                    }
                    
                    // Add parsed components to the data
                    selectedPlaceData.city = city;
                    selectedPlaceData.state = state;
                    selectedPlaceData.zip_code = zipCode;
                    
                    console.log('Stored place data:', selectedPlaceData);
                    
                    // Make the data available globally
                    window.selectedPlaceData = selectedPlaceData;
                    
                    // Update the add button state and text
                    updateAddNewAddressButton();
                    
                    // Show and update the address preview map
                    showAddressPreview(selectedPlaceData);
                });
                
                // Monitor for dropdown appearance
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && node.classList && node.classList.contains('pac-container')) {
                                console.log('Autocomplete dropdown appeared, setting z-index');
                                node.style.zIndex = '10001';
                                node.style.position = 'absolute';
                            }
                        });
                    });
                });
                
                observer.observe(document.body, { childList: true, subtree: true });
                
                // Also store the autocomplete instance globally for debugging
                window.googleAutocomplete = autocomplete;
                
            } else {
                console.error('Google Maps Places API not available');
                setTimeout(initGooglePlacesAutocomplete, 1000); // Retry after 1 second
            }
        }
        
        // Address Preview Map Functions
        let addressPreviewMap = null;
        let addressPreviewMarker = null;
        
        function showAddressPreview(placeData) {
            console.log('Showing address preview for:', placeData);
            
            // Show the preview section
            $('#address-preview-section').show();
            
            // Update address display
            $('#selected-address-display').text(placeData.formatted_address);
            $('#selected-coordinates-display').text(`${placeData.latitude.toFixed(6)}, ${placeData.longitude.toFixed(6)}`);
            
            // Initialize or update the map
            setTimeout(() => {
                initAddressPreviewMap(placeData);
            }, 100); // Small delay to ensure the div is visible
        }
        
        function hideAddressPreview() {
            $('#address-preview-section').hide();
            $('#selected-address-display').text('No address selected');
            $('#selected-coordinates-display').text('-');
            
            // Clear the map
            if (addressPreviewMarker) {
                addressPreviewMarker.setMap(null);
                addressPreviewMarker = null;
            }
            addressPreviewMap = null;
        }
        
        function initAddressPreviewMap(placeData) {
            if (typeof google === 'undefined' || !google.maps) {
                console.warn('Google Maps not available for preview');
                return;
            }
            
            const mapContainer = document.getElementById('address-preview-map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }
            
            const location = {
                lat: placeData.latitude,
                lng: placeData.longitude
            };
            
            // Initialize the map
            addressPreviewMap = new google.maps.Map(mapContainer, {
                zoom: 16,
                center: location,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                gestureHandling: 'cooperative',
                zoomControl: true,
                mapTypeControl: false,
                scaleControl: true,
                streetViewControl: false,
                rotateControl: false,
                fullscreenControl: false
            });
            
            // Add marker
            addressPreviewMarker = new google.maps.Marker({
                position: location,
                map: addressPreviewMap,
                title: placeData.formatted_address,
                animation: google.maps.Animation.DROP,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            // Add info window
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div class="p-2">
                        <h6><strong>New Plot Location</strong></h6>
                        <p class="mb-1">${placeData.formatted_address}</p>
                        <small class="text-muted">
                            Coordinates: ${placeData.latitude.toFixed(6)}, ${placeData.longitude.toFixed(6)}
                        </small>
                    </div>
                `
            });
            
            addressPreviewMarker.addListener('click', function() {
                infoWindow.open(addressPreviewMap, addressPreviewMarker);
            });
            
            // Auto-open info window for preview
            setTimeout(() => {
                infoWindow.open(addressPreviewMap, addressPreviewMarker);
            }, 500);
        }
        
        // Function to update the add new address button state (moved outside document ready for global access)
        function updateAddNewAddressButton() {
            const hasValidAddress = window.selectedPlaceData && window.selectedPlaceData.latitude;
            
            console.log('Updating add address button. Has valid address:', hasValidAddress);
            console.log('Selected place data:', window.selectedPlaceData);
            
            // Enable button only when valid address is selected
            $('#add-new-address-btn').prop('disabled', !hasValidAddress);
            
            // Update button text based on address selection
            if (!hasValidAddress) {
                $('#add-new-address-btn').html('<i class="fas fa-plus"></i> Add Plot');
            } else {
                $('#add-new-address-btn').html('<i class="fas fa-plus"></i> Add Plot');
            }
            
            console.log('Button disabled state:', $('#add-new-address-btn').prop('disabled'));
        }
        
        $(document).ready(function() {
            let selectedPlots = [];
            let groupPlotsTable;
            let groupPlotsMap;

            // Initialize DataTable
            groupPlotsTable = $('#group-plots-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route("admin.plot-groups-management.data") }}',
                    data: function(d) {
                        d.group_filter = $('#group-filter').val();
                        console.log('DataTable request - group_filter:', d.group_filter);
                    },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                    { data: 'id', name: 'id' },
                    { data: 'plot_name', name: 'plot_name' },
                    { data: 'address', name: 'address' },
                    { 
                        data: 'group_name', 
                        name: 'group_name',
                        render: function(data, type, row) {
                            if (data) {
                                return `<span class="badge badge-info">${data}</span>`;
                            }
                            return '<span class="badge badge-warning">Unassigned</span>';
                        }
                    },
                    { 
                        data: 'status', 
                        name: 'status',
                        render: function(data, type, row) {
                            const badgeClass = data === 'Grouped' ? 'badge-success' : 'badge-secondary';
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: 25,
                responsive: true,
                order: [[1, 'desc']],
                drawCallback: function() {
                    // Re-bind checkbox events after table redraw
                    bindCheckboxEvents();
                }
            });

            // Group filter change - AUTO SUBMIT
            $('#group-filter').change(function() {
                const selectedGroupName = $(this).find('option:selected').text();
                const selectedValue = $(this).val();
                
                console.log('Group filter changed to:', selectedValue, '(' + selectedGroupName + ')');
                
                // Reload table with new filter
                groupPlotsTable.ajax.reload(function(json) {
                    console.log('Table reloaded with', json.data.length, 'plots');
                    
                    // Show message if no data found for selected group
                    if (json.data.length === 0 && selectedValue && selectedValue !== 'unassigned') {
                        // Show info message for empty groups
                        const groupName = selectedGroupName.split(' (')[0]; // Remove plot count
                        const infoMessage = `
                            <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                                <i class="fas fa-info-circle"></i> The group "${groupName}" currently has no plots assigned. 
                                Use the checkboxes to select plots and add them to this group.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('#table-view .table-responsive').before(infoMessage);
                        
                        // Auto-hide after 8 seconds
                        setTimeout(() => {
                            $('.alert-info').fadeOut();
                        }, 8000);
                    } else {
                        // Remove any existing info messages
                        $('.alert-info').remove();
                    }
                });
                
                // Also update map
                updateMapView();
                // Update action buttons when group dropdown changes
                updateActionButtons();
            });

            // Tab change handling
            $('#groupPlotsTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#map-view') {
                    initializeMap();
                } else if ($(e.target).attr('href') === '#table-view') {
                    // Refresh table when switching to table view
                    groupPlotsTable.ajax.reload();
                }
            });

            // Bind checkbox events
            function bindCheckboxEvents() {
                $('.plot-checkbox').off('change').on('change', function() {
                    const plotId = $(this).val();
                    if ($(this).is(':checked')) {
                        if (!selectedPlots.includes(plotId)) {
                            selectedPlots.push(plotId);
                        }
                    } else {
                        selectedPlots = selectedPlots.filter(id => id !== plotId);
                    }
                    updateActionButtons();
                    updateSelectedCounter();
                });
            }

            // Plot selection handling
            bindCheckboxEvents();

            // Select all plots
            $('#select-all-plots').change(function() {
                const isChecked = $(this).is(':checked');
                $('.plot-checkbox').prop('checked', isChecked);
                
                if (isChecked) {
                    // Add all visible plot IDs to selection
                    $('.plot-checkbox').each(function() {
                        const plotId = $(this).val();
                        if (!selectedPlots.includes(plotId)) {
                            selectedPlots.push(plotId);
                        }
                    });
                } else {
                    // Remove all visible plot IDs from selection
                    $('.plot-checkbox').each(function() {
                        const plotId = $(this).val();
                        selectedPlots = selectedPlots.filter(id => id !== plotId);
                    });
                }
                updateActionButtons();
                updateSelectedCounter();
            });

            // Update selected counter
            function updateSelectedCounter() {
                if (selectedPlots.length > 0) {
                    if (!$('.selected-counter').length) {
                        $('body').append(`
                            <div class="selected-counter">
                                <div class="alert alert-info">
                                    <i class="fas fa-check-circle"></i> 
                                    <span class="counter-text">${selectedPlots.length} plots selected</span>
                                </div>
                            </div>
                        `);
                    } else {
                        $('.counter-text').text(`${selectedPlots.length} plots selected`);
                    }
                } else {
                    $('.selected-counter').remove();
                }
            }

            // Update action buttons based on selection
            function updateActionButtons() {
                const hasSelection = selectedPlots.length > 0;
                const groupSelected = $('#group-filter').val() && $('#group-filter').val() !== '' && $('#group-filter').val() !== 'unassigned';
                // Enable/disable group action buttons based on selection or group dropdown
                $('#add-to-group-btn').prop('disabled', !(hasSelection || groupSelected));
                $('#remove-from-group-btn').prop('disabled', !(hasSelection || groupSelected));
                $('#delete-group-btn').prop('disabled', !(hasSelection || groupSelected));
                // Update selected plots in modal
                updateSelectedPlotsModal();
            }

            // Update selected plots display in modal
            function updateSelectedPlotsModal() {
                const count = selectedPlots.length;
                $('#selected-plots-count').text(count);
                
                if (count === 0) {
                    $('#selected-plots-list').html('<small class="text-muted">No plots selected</small>');
                    $('#assign-to-group-btn').prop('disabled', true);
                } else {
                    let plotsList = '';
                    // Get selected plot details from table
                    $('.plot-checkbox:checked').each(function() {
                        const row = $(this).closest('tr');
                        const plotName = row.find('td:nth-child(3)').text() || 'Plot ' + $(this).val();
                        const address = row.find('td:nth-child(4)').text() || 'No address';
                        plotsList += `
                            <div class="mb-1 p-1 bg-light rounded">
                                <strong>${plotName}</strong><br>
                                <small class="text-muted">${address}</small>
                            </div>
                        `;
                    });
                    $('#selected-plots-list').html(plotsList);
                    
                    // Enable assign button if group is selected
                    const hasGroup = $('#target-group').val();
                    $('#assign-to-group-btn').prop('disabled', !hasGroup);
                }
            }

            // Handle target group selection
            $('#target-group').change(function() {
                const hasGroup = $(this).val();
                const hasPlots = selectedPlots.length > 0;
                $('#assign-to-group-btn').prop('disabled', !(hasGroup && hasPlots));
                
                // Enable/disable the add new address button based on group selection and address validity
                updateAddNewAddressButton();
            });

            // Plot Organization Actions
            $('#add-to-group-btn').click(function() {
                if (selectedPlots.length === 0) {
                    alert('Please select plots first.');
                    return;
                }
                // Modal will open automatically due to data-target="#addToGroupModal"
                updateSelectedPlotsModal();
            });

            // Assign plots to group
            $('#assign-to-group-btn').click(function() {
                const groupId = $('#target-group').val();
                const notes = $('#assignment-notes').val();
                
                if (!groupId) {
                    alert('Please select a group.');
                    return;
                }
                
                if (selectedPlots.length === 0) {
                    alert('No plots selected.');
                    return;
                }
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Assigning...');
                
                $.ajax({
                    url: '{{ route("admin.plot-groups-management.bulk-assign") }}',
                    method: 'POST',
                    data: {
                        plot_ids: selectedPlots,
                        group_id: groupId,
                        notes: notes,
                        _token: $('[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            const groupName = $('#target-group option:selected').text().split(' (')[0];
                            const successMessage = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                                    <i class="fas fa-check-circle"></i> ${selectedPlots.length} plots assigned to "${groupName}" successfully!
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('body').append(successMessage);
                            setTimeout(() => $('.alert-success').fadeOut(), 5000);
                            
                            // Close modal and refresh
                            $('#addToGroupModal').modal('hide');
                            selectedPlots = [];
                            groupPlotsTable.ajax.reload();
                            updateMapView();
                            $('.selected-counter').remove();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to assign plots'));
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'Failed to assign plots to group';
                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        $('#assign-to-group-btn').prop('disabled', false).html('<i class="fas fa-check"></i> Assign Selected Plots');
                    }
                });
            });

            $('#remove-from-group-btn').click(function() {
                if (selectedPlots.length === 0) {
                    alert('Please select plots first.');
                    return;
                }
                // Remove selected plots from their current groups
                if (confirm('Are you sure you want to remove selected plots from their groups?')) {
                    alert('Remove from group functionality will be implemented here.');
                }
            });

            // Group Management Actions
            $('#manage-group-membership-btn').click(function() {
                if (selectedPlots.length === 0) {
                    alert('Please select plots first.');
                    return;
                }
                // Implement group membership management
                alert('Group membership management functionality will be implemented here.');
            });

            $('#delete-group-btn').click(function() {
                if (selectedPlots.length === 0) {
                    alert('Please select plots first.');
                    return;
                }
                // Implement group deletion
                if (confirm('Are you sure you want to delete the selected groups?')) {
                    alert('Group deletion functionality will be implemented here.');
                }
            });

            // Initialize Google Maps
            function initializeMap() {
                if (!groupPlotsMap && typeof google !== 'undefined') {
                    console.log('Initializing Group Plots Map');
                    
                    // Initialize map centered on Austin, TX
                    groupPlotsMap = new google.maps.Map(document.getElementById('group-plots-map'), {
                        zoom: 10,
                        center: { lat: 30.2672, lng: -97.7431 }, // Austin, TX
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                    // Load initial plots on map
                    loadPlotsOnMap();
                } else if (typeof google === 'undefined') {
                    console.warn('Google Maps API not loaded yet');
                    // Retry after a short delay
                    setTimeout(initializeMap, 1000);
                }
            }

            // Load plots on map based on current filter
            function loadPlotsOnMap() {
                if (!groupPlotsMap) return;

                const groupFilter = $('#group-filter').val();
                
                $.ajax({
                    url: '{{ route("admin.plot-groups-management.data") }}',
                    method: 'GET',
                    data: { 
                        group_filter: groupFilter,
                        length: -1 // Get all records for map
                    },
                    success: function(response) {
                        if (response.data) {
                            clearMapMarkers();
                            addPlotsToMap(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load plots for map:', xhr.responseText);
                    }
                });
            }

            // Add plots to map
            function addPlotsToMap(plots) {
                const bounds = new google.maps.LatLngBounds();
                let markersAdded = 0;

                plots.forEach(function(plot) {
                    if (plot.coordinates_latitude && plot.coordinates_longitude) {
                        const position = {
                            lat: parseFloat(plot.coordinates_latitude),
                            lng: parseFloat(plot.coordinates_longitude)
                        };

                        // Determine marker color based on group
                        let markerColor = '#ff6b6b'; // Default red for unassigned
                        if (plot.group_name) {
                            markerColor = plot.group_color || '#3498db'; // Group color or blue
                        }

                        const marker = new google.maps.Marker({
                            position: position,
                            map: groupPlotsMap,
                            title: plot.plot_name || `Plot ${plot.id}`,
                            icon: {
                                url: `https://maps.google.com/mapfiles/ms/icons/${getMarkerColor(markerColor)}-dot.png`,
                                scaledSize: new google.maps.Size(32, 32)
                            }
                        });

                        // Info window content
                        const infoWindow = new google.maps.InfoWindow({
                            content: `
                                <div class="p-2">
                                    <h6><strong>${plot.plot_name || 'Plot ' + plot.id}</strong></h6>
                                    <p class="mb-1"><strong>Address:</strong> ${plot.address || 'No address'}</p>
                                    <p class="mb-1"><strong>Group:</strong> ${plot.group_name || 'No group assigned'}</p>
                                    <p class="mb-1"><strong>Status:</strong> ${plot.status || 'Unknown'}</p>
                                    <div class="mt-2">
                                        <input type="checkbox" class="plot-checkbox" value="${plot.id}" 
                                               ${selectedPlots.includes(plot.id.toString()) ? 'checked' : ''}>
                                        <label class="ml-1">Select this plot</label>
                                    </div>
                                </div>
                            `
                        });

                        marker.addListener('click', function() {
                            infoWindow.open(groupPlotsMap, marker);
                        });

                        bounds.extend(position);
                        markersAdded++;
                    }
                });

                // Fit map to show all markers
                if (markersAdded > 0) {
                    groupPlotsMap.fitBounds(bounds);
                    if (markersAdded === 1) {
                        groupPlotsMap.setZoom(15); // Zoom in for single marker
                    }
                }
            }

            // Clear existing markers
            function clearMapMarkers() {
                if (window.groupMapMarkers) {
                    window.groupMapMarkers.forEach(marker => marker.setMap(null));
                }
                window.groupMapMarkers = [];
            }

            // Get marker color name for Google Maps
            function getMarkerColor(hexColor) {
                const colorMap = {
                    '#ff6b6b': 'red',
                    '#3498db': 'blue',
                    '#2ecc71': 'green',
                    '#f39c12': 'orange',
                    '#9b59b6': 'purple',
                    '#1abc9c': 'ltblue',
                    '#e74c3c': 'red',
                    '#f1c40f': 'yellow'
                };
                return colorMap[hexColor] || 'blue';
            }

            // Update map view based on group filter
            function updateMapView() {
                const groupFilter = $('#group-filter').val();
                console.log('Updating map view for group:', groupFilter);
                
                if (groupPlotsMap) {
                    loadPlotsOnMap();
                } else if ($('#map-view').hasClass('active')) {
                    initializeMap();
                }
            }

            // Random color generator for group creation
            $('#random-color-btn').click(function() {
                const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22'];
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                $('#group-color').val(randomColor);
            });

            // Create group form submission
            $('#save-group-btn').click(function() {
                const form = $('#create-group-form');
                const formData = {
                    name: $('#group-name').val().trim(),
                    description: $('#group-description').val().trim(),
                    color: $('#group-color').val(),
                    _token: form.find('[name="_token"]').val()
                };

                if (!formData.name) {
                    // Show validation error
                    $('#group-name').addClass('is-invalid');
                    if (!$('#group-name').next('.invalid-feedback').length) {
                        $('#group-name').after('<div class="invalid-feedback">Please enter a group name.</div>');
                    }
                    $('#group-name').focus();
                    return;
                } else {
                    // Remove validation error
                    $('#group-name').removeClass('is-invalid');
                    $('#group-name').next('.invalid-feedback').remove();
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

                $.ajax({
                    url: '{{ route("admin.plot-groups-management.create-group") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Show success message with group name
                            const successMessage = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                                    <i class="fas fa-check-circle"></i> Group "${response.group.name}" created successfully! 
                                    Select plots and assign them to this group.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('body').append(successMessage);
                            
                            // Auto-hide after 8 seconds
                            setTimeout(() => {
                                $('.alert-success').fadeOut();
                            }, 8000);
                            
                            // Hide modal
                            $('#createGroupModal').modal('hide');
                            
                            // Add new group to dropdown
                            const newGroup = response.group;
                            const groupOption = `<option value="${newGroup.id}" data-color="${newGroup.color || '#3498db'}">
                                ${newGroup.name} (0 plots)
                            </option>`;
                            
                            // Insert before "Unassigned Plots Only" option
                            $('#group-filter option[value="unassigned"]').before(groupOption);
                            
                            // Don't automatically switch to the new group since it's empty
                            // Just refresh the current view
                            if (groupPlotsTable) {
                                groupPlotsTable.ajax.reload();
                            }
                            updateMapView();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to create group'));
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to create group. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#group-name').addClass('is-invalid');
                                $('#group-name').after(`<div class="invalid-feedback">${errors.name[0]}</div>`);
                            }
                            errorMessage = 'Please check the form for errors.';
                        }
                        
                        // Show error message
                        const errorAlert = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                                <i class="fas fa-exclamation-triangle"></i> ${errorMessage}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('body').append(errorAlert);
                        
                        // Auto-hide after 7 seconds
                        setTimeout(() => {
                            $('.alert-danger').fadeOut();
                        }, 7000);
                    },
                    complete: function() {
                        $('#save-group-btn').prop('disabled', false).html('<i class="fas fa-save"></i> Create Group');
                    }
                });
            });

            // Add new address functionality
            $('#add-new-address-btn').click(function() {
                const placeData = window.selectedPlaceData;
                
                if (!placeData || !placeData.latitude) {
                    alert('Please select a valid address using Google Places search above.');
                    return;
                }
                
                // Get the currently filtered group (if any)
                const currentGroupFilter = $('#group-filter').val();
                let groupId = null;
                
                // Only assign to group if a specific group is selected (not "All Groups" or "Unassigned")
                if (currentGroupFilter && currentGroupFilter !== 'unassigned') {
                    groupId = currentGroupFilter;
                }
                
                // Extract city and state from Google Places data or fallback to parsing
                let city = placeData.city || 'Unknown City';
                let state = placeData.state || 'Unknown State';
                let zipCode = placeData.zip_code || '';
                
                // If Google Places didn't provide components, try parsing the formatted address
                if (!city || city === 'Unknown City' || !state || state === 'Unknown State') {
                    const addressParts = placeData.formatted_address.split(', ');
                    
                    if (addressParts.length >= 3) {
                        if (!city || city === 'Unknown City') {
                            city = addressParts[addressParts.length - 3] || 'Unknown City';
                        }
                        if (!state || state === 'Unknown State' || !zipCode) {
                            const stateZip = addressParts[addressParts.length - 2] || '';
                            const stateZipMatch = stateZip.match(/^([A-Za-z\s]+)\s+(\d{5}(-\d{4})?)$/);
                            if (stateZipMatch) {
                                if (!state || state === 'Unknown State') {
                                    state = stateZipMatch[1].trim();
                                }
                                if (!zipCode) {
                                    zipCode = stateZipMatch[2];
                                }
                            } else {
                                if (!state || state === 'Unknown State') {
                                    state = stateZip || 'Unknown State';
                                }
                            }
                        }
                    }
                }
                
                console.log('Final parsed address:', { 
                    city, 
                    state, 
                    zipCode, 
                    original: placeData.formatted_address,
                    fromGoogleComponents: !!placeData.city 
                });
                
                const formData = {
                    street_address: placeData.formatted_address,
                    city: city,
                    state: state,
                    zip_code: zipCode,
                    country: 'United States',
                    plot_name: `Plot at ${placeData.formatted_address}`,
                    coordinates_latitude: placeData.latitude,
                    coordinates_longitude: placeData.longitude,
                    group_id: groupId,
                    _token: $('[name="_token"]').val()
                };
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
                
                $.ajax({
                    url: '{{ route("admin.plot-groups-management.create-plot-and-assign") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            let message = 'New plot created successfully!';
                            if (groupId) {
                                const groupName = $('#group-filter option:selected').text().split(' (')[0];
                                message = `New plot created and assigned to "${groupName}" successfully!`;
                            }
                            
                            const successMessage = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                                    <i class="fas fa-check-circle"></i> ${message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('body').append(successMessage);
                            setTimeout(() => $('.alert-success').fadeOut(), 5000);
                            
                            // Close modal and refresh
                            $('#addToGroupModal').modal('hide');
                            groupPlotsTable.ajax.reload();
                            updateMapView();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to create plot'));
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to create plot';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorDetails = [];
                            
                            Object.keys(errors).forEach(field => {
                                errorDetails.push(`${field}: ${errors[field][0]}`);
                            });
                            
                            errorMessage = `Validation errors: ${errorDetails.join(', ')}`;
                        }
                        
                        console.error('Plot creation error:', xhr.responseJSON);
                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        $('#add-new-address-btn').prop('disabled', false).html('<i class="fas fa-plus"></i> Add Plot');
                    }
                });
            });

            // Reset modals when hidden
            $('#addToGroupModal').on('hidden.bs.modal', function() {
                $('#google-address-search').val('');
                window.selectedPlaceData = null;
                $('#add-new-address-btn').prop('disabled', true).html('<i class="fas fa-plus"></i> Add Plot');
                hideAddressPreview();
            });
            
            // Update button states when modal is shown
            $('#addToGroupModal').on('shown.bs.modal', function() {
                updateAddNewAddressButton();
            });
            
            // Also update button when Google address search is cleared or typed into
            $('#google-address-search').on('input', function() {
                const inputValue = $(this).val().trim();
                console.log('Address input changed:', inputValue);
                
                if (!inputValue) {
                    // Clear the stored place data when search is cleared
                    window.selectedPlaceData = null;
                    updateAddNewAddressButton();
                    hideAddressPreview();
                } else if (inputValue.length > 10) {
                    // For debugging: if user types a long address, enable the button as fallback
                    // This is a temporary workaround if autocomplete fails
                    console.log('Long address typed, checking if we should enable fallback');
                    
                    // Only enable fallback if autocomplete hasn't set data and address looks valid
                    if (!window.selectedPlaceData && inputValue.includes(',')) {
                        console.log('Enabling manual address fallback');
                        window.selectedPlaceData = {
                            formatted_address: inputValue,
                            latitude: null, // Will need geocoding on backend
                            longitude: null,
                            place_id: null,
                            manual_entry: true
                        };
                        updateAddNewAddressButton();
                        // Don't show map preview for manual entries without coordinates
                    }
                }
            });

            // Reset forms when modals are hidden
            $('#createGroupModal').on('hidden.bs.modal', function() {
                $('#create-group-form')[0].reset();
                $('#group-color').val('#3498db');
                // Clear validation errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });
        });
    </script>
@stop

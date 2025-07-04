@extends('adminlte::page')

@section('title', 'Create Plot - Project Tracker')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus-circle"></i> Create New Plot</h1>
        <div>
            <a href="{{ route('admin.plots.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Plots
            </a>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-info">
                <i class="fas fa-map"></i> View Map
            </a>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.plots.store') }}" method="POST" id="create-plot-form">
        @csrf

        <div class="row">
            <!-- Plot Information -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Plot Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plot_name">Plot Name</label>
                                    <input type="text" class="form-control @error('plot_name') is-invalid @enderror"
                                           id="plot_name" name="plot_name" value="{{ old('plot_name') }}"
                                           placeholder="Enter plot name">
                                    @error('plot_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hb837_id">HB837 Project</label>
                                    <select class="form-control @error('hb837_id') is-invalid @enderror"
                                            id="hb837_id" name="hb837_id">
                                        <option value="">Select a project...</option>
                                        @foreach($hb837Projects as $project)
                                            <option value="{{ $project->id }}"
                                                    {{ old('hb837_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->property_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hb837_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plot_description">Description</label>
                                    <textarea class="form-control @error('plot_description') is-invalid @enderror"
                                              id="plot_description" name="plot_description" rows="3"
                                              placeholder="Enter plot description">{{ old('plot_description') }}</textarea>
                                    @error('plot_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Address Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address_line_1">Address Line 1</label>
                                    <input type="text" class="form-control @error('address_line_1') is-invalid @enderror"
                                           id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}"
                                           placeholder="Street address">
                                    @error('address_line_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_line_2">Address Line 2</label>
                                    <input type="text" class="form-control @error('address_line_2') is-invalid @enderror"
                                           id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}"
                                           placeholder="Apartment, suite, etc.">
                                    @error('address_line_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" value="{{ old('city') }}"
                                           placeholder="City">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                           id="state" name="state" value="{{ old('state') }}"
                                           placeholder="State">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zip_code">ZIP Code</label>
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                                           id="zip_code" name="zip_code" value="{{ old('zip_code') }}"
                                           placeholder="ZIP code">
                                    @error('zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                                           id="country" name="country" value="{{ old('country', 'USA') }}"
                                           placeholder="Country">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map and Coordinates -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Location & Coordinates</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="coordinates_latitude">Latitude</label>
                            <input type="number" step="any" class="form-control @error('coordinates_latitude') is-invalid @enderror"
                                   id="coordinates_latitude" name="coordinates_latitude"
                                   value="{{ old('coordinates_latitude', request('lat')) }}"
                                   placeholder="e.g., 40.7128">
                            @error('coordinates_latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="coordinates_longitude">Longitude</label>
                            <input type="number" step="any" class="form-control @error('coordinates_longitude') is-invalid @enderror"
                                   id="coordinates_longitude" name="coordinates_longitude"
                                   value="{{ old('coordinates_longitude', request('lng')) }}"
                                   placeholder="e.g., -74.0060">
                            @error('coordinates_longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-info btn-block" id="geocode-address">
                                <i class="fas fa-search-location"></i> Get Coordinates from Address
                            </button>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-success btn-block" id="get-current-location">
                                <i class="fas fa-crosshairs"></i> Use Current Location
                            </button>
                        </div>

                        <!-- Mini Map -->
                        <div class="form-group">
                            <label>Preview Location</label>
                            <div id="mini-map" style="height: 200px; width: 100%; border: 1px solid #ddd; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Create Plot
                        </button>
                        <a href="{{ route('admin.plots.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        #mini-map {
            min-height: 200px;
        }

        .coordinate-input {
            font-family: monospace;
        }

        .location-buttons .btn {
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    <script>
        let miniMap;
        let marker;

        // Initialize mini map
        function initMiniMap() {
            const defaultCenter = { lat: 39.8283, lng: -98.5795 };

            miniMap = new google.maps.Map(document.getElementById("mini-map"), {
                zoom: 10,
                center: defaultCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            marker = new google.maps.Marker({
                map: miniMap,
                draggable: true
            });

            // Update coordinates when marker is dragged
            marker.addListener('dragend', function() {
                const position = marker.getPosition();
                updateCoordinateFields(position.lat(), position.lng());
            });

            // Initialize marker position if coordinates are provided
            updateMapFromCoordinates();
        }

        // Update map from coordinate inputs
        function updateMapFromCoordinates() {
            const lat = parseFloat(document.getElementById('coordinates_latitude').value);
            const lng = parseFloat(document.getElementById('coordinates_longitude').value);

            if (!isNaN(lat) && !isNaN(lng)) {
                const position = { lat: lat, lng: lng };
                miniMap.setCenter(position);
                marker.setPosition(position);
                marker.setVisible(true);
            } else {
                marker.setVisible(false);
            }
        }

        // Update coordinate fields
        function updateCoordinateFields(lat, lng) {
            document.getElementById('coordinates_latitude').value = lat.toFixed(6);
            document.getElementById('coordinates_longitude').value = lng.toFixed(6);
        }

        // Geocode address to get coordinates
        document.getElementById('geocode-address').addEventListener('click', function() {
            const address = buildAddressString();

            if (!address.trim()) {
                alert('Please enter an address first.');
                return;
            }

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, function(results, status) {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    updateCoordinateFields(location.lat(), location.lng());
                    updateMapFromCoordinates();

                    // Show success message
                    alert('Coordinates found successfully!');
                } else {
                    alert('Geocoding failed: ' + status);
                }
            });
        });

        // Build address string from form fields
        function buildAddressString() {
            const parts = [];
            const address1 = document.getElementById('address_line_1').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value.trim();
            const zip = document.getElementById('zip_code').value.trim();

            if (address1) parts.push(address1);
            if (city) parts.push(city);
            if (state) parts.push(state);
            if (zip) parts.push(zip);

            return parts.join(', ');
        }

        // Get current location
        document.getElementById('get-current-location').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    updateCoordinateFields(position.coords.latitude, position.coords.longitude);
                    updateMapFromCoordinates();
                    alert('Current location detected successfully!');
                }, function(error) {
                    alert('Error getting current location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });

        // Listen for coordinate input changes
        document.getElementById('coordinates_latitude').addEventListener('input', updateMapFromCoordinates);
        document.getElementById('coordinates_longitude').addEventListener('input', updateMapFromCoordinates);

        // Form validation
        document.getElementById('create-plot-form').addEventListener('submit', function(e) {
            const plotName = document.getElementById('plot_name').value.trim();
            const lat = document.getElementById('coordinates_latitude').value;
            const lng = document.getElementById('coordinates_longitude').value;

            if (!plotName) {
                alert('Please enter a plot name.');
                e.preventDefault();
                return;
            }

            if (lat && lng) {
                if (isNaN(parseFloat(lat)) || isNaN(parseFloat(lng))) {
                    alert('Please enter valid coordinates.');
                    e.preventDefault();
                    return;
                }
            }
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Map will be initialized by Google Maps callback
        });
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY_HERE') }}&callback=initMiniMap">
    </script>
@stop

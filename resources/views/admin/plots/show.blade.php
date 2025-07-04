@extends('adminlte::page')

@section('title', 'Plot Details - Project Tracker')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-map-marker-alt"></i> Plot Details</h1>
        <div>
            <a href="{{ route('admin.plots.edit', $plot) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Plot
            </a>
            <a href="{{ route('admin.plots.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Plots
            </a>
            @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                <a href="{{ route('admin.maps.index') }}?plot={{ $plot->id }}" class="btn btn-success">
                    <i class="fas fa-map"></i> View on Map
                </a>
            @endif
        </div>
    </div>
@stop

@section('content')
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
                            <strong>Plot Name:</strong>
                            <p>{{ $plot->plot_name ?: 'Unnamed Plot' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p>
                                <span class="badge badge-{{ $plot->status === 'active' ? 'success' : ($plot->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($plot->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <strong>Description:</strong>
                            <p>{{ $plot->plot_description ?: 'No description provided.' }}</p>
                        </div>
                    </div>

                    @if($plot->hb837)
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Associated HB837 Project:</strong>
                                <p>
                                    <a href="{{ route('admin.hb837.show', $plot->hb837) }}" class="text-primary">
                                        {{ $plot->hb837->property_name }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Created:</strong>
                            <p>{{ $plot->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong>
                            <p>{{ $plot->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            @if($plot->address)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Address Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Address:</strong>
                                <p>
                                    {{ $plot->address->address_line_1 }}<br>
                                    @if($plot->address->address_line_2)
                                        {{ $plot->address->address_line_2 }}<br>
                                    @endif
                                    {{ $plot->address->city }}, {{ $plot->address->state }} {{ $plot->address->zip_code }}<br>
                                    {{ $plot->address->country }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Map and Actions -->
        <div class="col-md-4">
            @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Location Map</h3>
                    </div>
                    <div class="card-body">
                        <div id="plot-map" style="height: 300px; width: 100%;"></div>

                        <div class="mt-3">
                            <strong>Coordinates:</strong>
                            <p class="text-monospace">
                                {{ floatval($plot->coordinates_latitude) }},
                                {{ floatval($plot->coordinates_longitude) }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Location</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">No coordinates available for this plot.</p>
                        <a href="{{ route('admin.plots.edit', $plot) }}" class="btn btn-primary">
                            <i class="fas fa-map-marker-alt"></i> Add Coordinates
                        </a>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.plots.edit', $plot) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Plot
                        </a>

                        @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                            <a href="{{ route('admin.maps.index') }}?plot={{ $plot->id }}" class="btn btn-success btn-block">
                                <i class="fas fa-map"></i> View on Map
                            </a>
                        @endif

                        @if($plot->hb837)
                            <a href="{{ route('admin.hb837.show', $plot->hb837) }}" class="btn btn-info btn-block">
                                <i class="fas fa-project-diagram"></i> View Project
                            </a>
                        @endif

                        <button type="button" class="btn btn-danger btn-block" onclick="deletePlot()">
                            <i class="fas fa-trash"></i> Delete Plot
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('admin.plots.destroy', $plot) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@stop

@section('css')
    <style>
        #plot-map {
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .text-monospace {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }

        .d-grid .btn {
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    @if($plot->coordinates_latitude && $plot->coordinates_longitude)
        <script>
            function initPlotMap() {
                const plotLocation = {
                    lat: {{ $plot->coordinates_latitude }},
                    lng: {{ $plot->coordinates_longitude }}
                };

                const map = new google.maps.Map(document.getElementById("plot-map"), {
                    zoom: 15,
                    center: plotLocation,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                });

                const marker = new google.maps.Marker({
                    position: plotLocation,
                    map: map,
                    title: "{{ $plot->plot_name ?: 'Plot ' . $plot->id }}",
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div>
                            <h6>{{ $plot->plot_name ?: 'Unnamed Plot' }}</h6>
                            <p><strong>ID:</strong> {{ $plot->id }}</p>
                            @if($plot->address)
                                <p><strong>Address:</strong> {{ $plot->address->address_line_1 }}</p>
                            @endif
                            @if($plot->hb837)
                                <p><strong>Project:</strong> {{ $plot->hb837->property_name }}</p>
                            @endif
                        </div>
                    `
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });
            }

            // Delete plot function
            function deletePlot() {
                if (confirm('Are you sure you want to delete this plot? This action cannot be undone.')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>

        <!-- Google Maps API -->
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY_HERE') }}&callback=initPlotMap">
        </script>
    @else
        <script>
            function deletePlot() {
                if (confirm('Are you sure you want to delete this plot? This action cannot be undone.')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
    @endif
@stop

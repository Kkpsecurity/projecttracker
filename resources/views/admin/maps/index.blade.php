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
            <a href="{{ route('admin.plot-clients.index') }}" class="btn btn-info">
                <i class="fas fa-building"></i> Macro Client Plots
            </a>
            <a href="{{ route('admin.plot-groups-management.index') }}" class="btn btn-warning">
                <i class="fas fa-layer-group"></i> Group Plots
            </a>
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
    {{-- Statistics Cards Component --}}
    @include('admin.maps.components.statistics-cards')

    {{-- Plot Groups Info Component --}}
    @include('admin.maps.components.info-card')

    {{-- Map Container --}}
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

                    {{-- Map Controls Component --}}
                    @include('admin.maps.components.map-controls')

                    {{-- Plot Group Controls Component --}}
                    @include('admin.maps.components.plot-group-controls')
                </div>
            </div>
        </div>
    </div>

    {{-- Plot Information Panel Component --}}
    @include('admin.maps.components.plot-info-panel')
@stop

@section('css')
    @include('admin.maps.components.map-styles')
@stop

@section('js')
    {{-- Google Maps Core JavaScript --}}
    @include('admin.maps.components.google-maps-core')

    {{-- Map Controls JavaScript --}}
    @include('admin.maps.components.map-controls-js')

    {{-- Map Actions JavaScript --}}
    @include('admin.maps.components.map-actions-js')

    {{-- Map Error Handling JavaScript --}}
    @include('admin.maps.components.map-error-handler')

    {{-- Google Maps API Script --}}
    @if(config('services.google_maps.api_key') && config('services.google_maps.api_key') !== 'YOUR_ACTUAL_GOOGLE_MAPS_API_KEY_HERE')
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places,geometry&callback=initMap">
        </script>
    @endif
@stop

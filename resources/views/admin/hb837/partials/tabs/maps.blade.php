{{-- Location & Maps Tab Content --}}

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Property Location & Maps</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Address Information</h6>
                        <address>
                            <strong>{{ $hb837->property_name }}</strong><br>
                            {{ $hb837->address }}<br>
                            {{ $hb837->city }}, {{ $hb837->state }} {{ $hb837->zip }}
                        </address>
                    </div>
                    <div class="col-md-6">
                        <h6>Security Gauge</h6>
                        <p><strong>Crime Risk:</strong>
                            <span class="badge badge-{{ $hb837->securitygauge_crime_risk === 'Elevated' ? 'warning' : ($hb837->securitygauge_crime_risk === 'High' ? 'danger' : 'success') }}">
                                {{ $hb837->securitygauge_crime_risk ?? 'Unknown' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Property Location Map</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>Address:</strong> {{ $hb837->address }}, {{ $hb837->city }}, {{ $hb837->state }} {{ $hb837->zip }}
                                </div>
                                <div class="mt-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($hb837->address . ', ' . $hb837->city . ', ' . $hb837->state) }}"
                                    target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i> Open in Google Maps
                                    </a>
                                    <a href="https://www.openstreetmap.org/search?query={{ urlencode($hb837->address . ', ' . $hb837->city . ', ' . $hb837->state) }}"
                                    target="_blank" class="btn btn-outline-secondary btn-sm ml-2">
                                        <i class="fas fa-map"></i> View on OpenStreetMap
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

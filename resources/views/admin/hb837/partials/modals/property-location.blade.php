<div class="modal fade" id="propertyLocationModal" tabindex="-1" role="dialog" aria-labelledby="propertyLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="propertyLocationModalLabel">
                    <i class="fas fa-map-marker-alt text-info"></i> Property Location
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" id="modal-property-name"></h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-muted"></i> 
                                    <span id="modal-property-address"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="propertyMap" style="height: 450px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                        <div id="mapLoadingIndicator" class="text-center p-4" style="display: none;">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Loading map...</p>
                        </div>
                        <div id="mapErrorIndicator" class="text-center p-4" style="display: none;">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                            <p class="mt-2">Unable to load map. Please check the address or try again later.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="openInGoogleMaps()">
                    <i class="fas fa-external-link-alt"></i> Open in Google Maps
                </button>
            </div>
        </div>
    </div>
</div>
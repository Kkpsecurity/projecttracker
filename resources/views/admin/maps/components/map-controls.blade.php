{{-- Map Controls Component --}}
<div class="mt-3">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="add-plot-address">Add Plot from Address:</label>
                <div class="input-group">
                    <input type="text" id="add-plot-address" class="form-control"
                           placeholder="Enter address to create plot...">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="add-plot-btn">
                            <i class="fas fa-plus"></i> Add Plot
                        </button>
                    </div>
                </div>
                <small class="text-muted">Click on map after geocoding to place plot</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="macro-client-filter">Load Macro Client Plots:</label>
                <div class="input-group">
                    <select id="macro-client-filter" class="form-control">
                        <option value="">Select Macro Client...</option>
                        @foreach($macroClients as $client)
                            <option value="{{ $client }}">{{ $client }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-info" type="button" id="load-client-plots-btn">
                            <i class="fas fa-map-marked-alt"></i> Load Plots
                        </button>
                    </div>
                </div>
                <small class="text-muted">View all addresses for selected client</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Map Options:</label>
                <div class="btn-group d-block">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="toggle-plots">
                        <i class="fas fa-eye"></i> Toggle Plots
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" id="center-map">
                        <i class="fas fa-crosshairs"></i> Center Map
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" id="refresh-plots">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm" id="clear-filters">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

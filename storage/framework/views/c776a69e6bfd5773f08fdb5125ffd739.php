<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/google-maps.css')); ?>">
    <style>
        /* Improved responsive layout */
        .map-container {
            position: relative;
            height: 70vh;
            min-height: 500px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #map {
            height: 100%;
            width: 100%;
        }

        #address-list {
            max-height: 60vh;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .address-item {
            transition: all 0.2s ease;
            cursor: pointer;
            border-left: 3px solid transparent;
        }

        .address-item:hover {
            background-color: #f5f5f5;
            border-left-color: #4285f4;
        }

        .address-item.selected {
            background-color: #e8f0fe;
            border-left-color: #1a73e8;
        }

        /* Responsive sidebar */
        @media (max-width: 992px) {
            .map-sidebar {
                position: absolute;
                right: 0;
                top: 0;
                width: 300px;
                height: 100%;
                z-index: 1000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                background: white;
            }

            .map-sidebar.visible {
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        /* Loading state */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        // Controller return vars: $plots, $selectedPlot, $macroClients
        $selectedPlotId = request()->get('selectedPlotId'); // Fetch from query string
        $selectedMacroClient = request()->get('macro_client');
    ?>

    <div class="container-fluid p-3">
        <?php echo $__env->make('admin.hb837.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Map Interface -->
        <div class="row mt-3">
            <div class="col-lg-9 col-md-8">
                <div class="map-container">
                    <div id="map"></div>
                    <div id="loading-overlay" class="loading-overlay" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading map...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Map Controls -->
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="btn-group" role="group">
                        <button id="reset-zoom" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-home"></i> Reset View
                        </button>
                        <button id="toggle-satellite" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-satellite"></i> Satellite
                        </button>
                        <button id="cluster-markers" class="btn btn-outline-info btn-sm">
                            <i class="fa fa-layer-group"></i> Cluster
                        </button>
                    </div>
                    
                    <div class="map-stats" id="map-stats">
                        <small class="text-muted">
                            <span id="marker-count">0</span> properties displayed
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Property Addresses</h6>
                        <div class="btn-group btn-group-sm">
                            <button id="reset-zoom-sidebar" class="btn btn-outline-primary btn-sm" title="Reset Map View">
                                <i class="fa fa-home"></i>
                            </button>
                            <button class="sidebar-toggle btn btn-outline-secondary btn-sm d-lg-none" title="Toggle Sidebar">
                                <i class="fa fa-bars"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <!-- Address Type Filter -->
                        <div class="p-3 border-bottom">
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <input type="radio" class="btn-check" name="address-filter" id="filter-all" value="all" checked>
                                <label class="btn btn-outline-primary" for="filter-all">All</label>
                                
                                <input type="radio" class="btn-check" name="address-filter" id="filter-plot" value="plot">
                                <label class="btn btn-outline-success" for="filter-plot">Custom</label>
                                
                                <input type="radio" class="btn-check" name="address-filter" id="filter-macro" value="macro">
                                <label class="btn btn-outline-info" for="filter-macro">Existing</label>
                            </div>
                        </div>
                        
                        <!-- Address List -->
                        <div id="address-list-container" class="overflow-auto" style="max-height: 50vh;">
                            <ul id="address-list" class="list-group list-group-flush">
                                <!-- Addresses will be populated by JavaScript -->
                            </ul>
                        </div>
                        
                        <!-- Empty State -->
                        <div id="empty-state" class="text-center p-4 text-muted" style="display: none;">
                            <i class="fa fa-map-marker-alt fa-2x mb-2"></i>
                            <p class="mb-0">No addresses to display</p>
                            <small>Select a plot or macro client above</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats Dashboard -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <h4 class="text-primary mb-1" id="stat-total-plots"><?php echo e($plots->count()); ?></h4>
                                    <small class="text-muted">Total Plots</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <h4 class="text-success mb-1" id="stat-custom-plots"><?php echo e($plots->where('plot_type', 'custom')->count()); ?></h4>
                                    <small class="text-muted">Custom Plots</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <h4 class="text-info mb-1" id="stat-prospect-plots"><?php echo e($plots->where('plot_type', 'prospect')->count()); ?></h4>
                                    <small class="text-muted">Prospects</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <h4 class="text-warning mb-1" id="stat-client-plots"><?php echo e($plots->where('plot_type', 'client')->count()); ?></h4>
                                    <small class="text-muted">Clients</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
    <?php echo $__env->make('partials.modals.create-plot-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <!-- Include Google Maps API -->
    <script>
        // Initialize global variables for selected plot and macro client


        window.MapPlotConfig = {
            selectedPlotId: <?php echo json_encode($selectedPlotId ?? null, 15, 512) ?>,
            selectedMacroClient: <?php echo json_encode($selectedMacroClient ?? null, 15, 512) ?>,
            csrfToken: "<?php echo e(csrf_token()); ?>",
            mapsEnabled: <?php echo e(config('hb837.enable_maps') ? 'true' : 'false'); ?>,
            developmentMode: <?php echo e(config('hb837.maps_development_mode') ? 'true' : 'false'); ?>,

            // URLs from your original config
            addAddressUrl: "<?php echo e(route('admin.mapplots.address.store')); ?>",
            deleteAddressUrl: "<?php echo e(route('admin.mapplots.delete.address', ['plotAddressId' => ':id'])); ?>",
            fetchPlotAddressesUrl: "<?php echo e(route('admin.mapplots.plot_addresses', ['plot_id' => ':id'])); ?>",
            fetchAddressesUrl: "<?php echo e(route('admin.mapplots.load_addresses')); ?>",
            fetchMacroClientPropertiesUrl: "<?php echo e(route('admin.mapplots.macro_client_properties', ['macro_client' => ':macro_client'])); ?>",

            // Map defaults
            mapDefaults: {
                center: {
                    lat: 39.8283,
                    lng: -98.5795
                }, // Center of US
                zoom: 4,
                minZoom: 3,
                maxZoom: 18
            }
        };

        // Global error handler for Google Maps API
        window.gm_authFailure = function() {
            console.error('Google Maps API authentication failed');
            window.MapPlotConfig.mapsEnabled = false;
            showMapError('Authentication Failed', 'The Google Maps API key is not properly configured for this domain. Please contact your administrator.');
        };

        // Error display function
        function showMapError(title, message) {
            document.getElementById('map').innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                    <div class="text-center p-4">
                        <div class="alert alert-warning" role="alert">
                            <h5 class="alert-heading">${title}</h5>
                            <p class="mb-0">${message}</p>
                            <hr>
                            <p class="mb-0 small">
                                <strong>For developers:</strong> 
                                Add <code><?php echo e(request()->getSchemeAndHttpHost()); ?>/*</code> to your Google Cloud Console API restrictions.
                            </p>
                        </div>
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="fa fa-refresh"></i> Retry
                        </button>
                    </div>
                </div>
            `;
        }
    </script>
    
    <?php if(config('hb837.enable_maps')): ?>
        <script async defer 
                src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('hb837.map_api_key')); ?>&libraries=places&callback=initializeGoogleMaps"
                onerror="showMapError('Failed to Load', 'Could not load Google Maps API. Please check your internet connection.')">
        </script>
    <?php else: ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showMapError('Maps Disabled', 'Google Maps is currently disabled in development mode.');
            });
        </script>
    <?php endif; ?>
    
    <script src="<?php echo e(asset('js/google-maps.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/hb837/google-maps.blade.php ENDPATH**/ ?>
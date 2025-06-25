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
        // Controller return vars: $plots, $selectedPlot
        $selectedPlotId = request()->get('selectedPlotId'); // Fetch from query string
        $selectedMacroClient = request()->get('macro_client');

        // Get distinct macro clients
        $macroClients = \App\Models\HB837::select('macro_client')->distinct()->get();
        $macroClients = $macroClients->filter(function ($client) {
            return !empty($client->macro_client);
        });
    ?>

    <div class="container-fluid p-3">
        <?php echo $__env->make('admin.hb837.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        0
        <div class="row mt-3">
            <div class="col-9">
                <div class="map-container">
                    <div id="map"></div>
                    <div class="map-loading" id="map-loading" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="loading-overlay" class="loading-overlay" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-3 bg-light p-0 m-0">
                <div class="list-group-header d-flex justify-content-between align-items-center p-3">
                    <h5>Addresses</h5>
                    <button id="reset-zoom" class="btn btn-sm btn-primary">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                <ul id="address-list" class="list-group m-0 p-0"></ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
    <?php echo $__env->make('partials.modals.create-plot-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <!-- Include Google Maps API -->
    <script>
        // Initialize global variables for selected plot and macro client


        window.MapPlotConfig = {
            selectedPlotId: <?php echo json_encode($selectedPlotId ?? null, 15, 512) ?>,
            selectedMacroClient: <?php echo json_encode($selectedMacroClient ?? null, 15, 512) ?>,
            csrfToken: "<?php echo e(csrf_token()); ?>",

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
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('hb837.map_api_key')); ?>&libraries=places"></script>
    <script src="<?php echo e(asset('js/google-maps.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/google-maps.blade.php ENDPATH**/ ?>
{{-- Map Error Handling JavaScript --}}
<script>
    window.MapErrorHandler = {
        // Global error handler for Google Maps API
        init() {
            window.gm_authFailure = () => {
                console.error('Google Maps API authentication failed. Invalid API key.');
                this.showMapError('Google Maps API authentication failed. Please check your API key configuration.');
            };

            // Handle API key not configured
            @if(!config('services.google_maps.api_key') || config('services.google_maps.api_key') === 'YOUR_ACTUAL_GOOGLE_MAPS_API_KEY_HERE')
                console.warn('Google Maps API key is not configured properly.');
                window.initMap = () => {
                    this.showMapError('Google Maps API key is not configured. Please contact the administrator to set up the API key.');
                };
                setTimeout(() => window.initMap(), 100);
            @endif

            // Handle other Google Maps errors
            window.addEventListener('error', (e) => {
                if (e.message && e.message.includes('Google Maps')) {
                    console.error('Google Maps error:', e.message);
                }
            });
        },

        // Show error message on map container
        showMapError(message) {
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.innerHTML = `
                    <div class="map-error">
                        <div class="map-error-content">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #ffc107;"></i>
                            <h4>Google Maps Configuration Required</h4>
                            <p style="margin-bottom: 20px;">${message}</p>
                            <div class="map-error-details">
                                <strong>To fix this:</strong><br>
                                1. Get a Google Maps API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a><br>
                                2. Enable Maps JavaScript API and Geocoding API<br>
                                3. Add the key to your .env file: <code>GOOGLE_MAPS_API_KEY=your_key_here</code><br>
                                4. Clear cache: <code>php artisan config:clear</code>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    };

    // Initialize error handler when DOM loads
    document.addEventListener('DOMContentLoaded', () => {
        window.MapErrorHandler.init();
    });
</script>

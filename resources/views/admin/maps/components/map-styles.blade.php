{{-- Map Styles --}}
<style>
    .map-marker {
        cursor: pointer;
    }

    .plot-info-card {
        max-width: 300px;
    }

    .info-window {
        font-family: 'Source Sans Pro', sans-serif;
    }

    .info-window h6 {
        margin-bottom: 10px;
        color: #007bff;
    }

    .info-window .btn {
        font-size: 12px;
        padding: 4px 8px;
    }

    #map {
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .map-controls {
        background: rgba(255, 255, 255, 0.9);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Loading states */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
    }

    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }

    /* Map error state */
    .map-error {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        color: #6c757d;
        text-align: center;
        padding: 40px;
        border-radius: 5px;
    }

    .map-error-content {
        max-width: 500px;
    }

    .map-error-details {
        background: #e9ecef;
        padding: 15px;
        border-radius: 5px;
        text-align: left;
        margin-top: 20px;
    }
</style>

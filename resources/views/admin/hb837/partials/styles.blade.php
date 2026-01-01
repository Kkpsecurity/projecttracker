{{-- HB837 Edit Form Styles --}}
<style>
.nav-tabs .nav-link {
    color: #495057;
    background-color: transparent;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #f8f9fa;
    transform: translateY(-1px);
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #007bff #007bff #fff;
    font-weight: 600;
    box-shadow: 0 -2px 8px rgba(0, 123, 255, 0.1);
}

.nav-tabs .nav-link.active i {
    color: #007bff !important;
}

.tab-content {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
    padding: 0;
    min-height: 400px;
}

.tab-pane {
    padding: 1.5rem;
}

.nav-tabs {
    margin-bottom: 0;
    border-bottom: 2px solid #dee2e6;
}

.tab-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.375rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #007bff;
}

.tab-title {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.tab-title i {
    margin-right: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.card {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    border: none;
}

.badge {
    font-size: 0.75rem;
}

/* Active tab indicator */
.nav-tabs .nav-link.active::before {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 2px 2px 0 0;
}

.nav-tabs .nav-item {
    position: relative;
}

/* Enhanced form styling */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}
</style>

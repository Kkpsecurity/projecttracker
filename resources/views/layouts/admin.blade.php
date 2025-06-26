@extends('adminlte::page')

@section('title', 'Project Tracker')

@section('content_header')
    @yield('content_header_content')
@stop

@section('content')
    @include('partials.messages')
    @yield('main_content')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom AdminLTE overrides */
        .content-wrapper, .right-side {
            min-height: calc(100vh - 50px);
        }
        
        /* Modern Table Styles */
        .table-modern {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid #e9ecef;
        }
        
        .table-modern th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table-modern td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        .table-modern tbody tr {
            transition: all 0.2s ease;
            border: none;
        }
        
        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Compact table variant */
        .table-compact td,
        .table-compact th {
            padding: 8px 12px;
            font-size: 0.8rem;
        }
        
        /* Striped modern tables */
        .table-modern.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        
        .table-modern.table-striped tbody tr:nth-of-type(odd):hover {
            background-color: #f8f9fa;
        }
        
        /* Action buttons in tables */
        .table-actions {
            white-space: nowrap;
        }
        
        .table-actions .btn {
            margin: 0 2px;
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 4px;
        }
        
        /* Status badges in tables */
        .table-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 12px;
            letter-spacing: 0.5px;
        }
        
        .table-badge.badge-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        
        .table-badge.badge-warning {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            color: #212529;
        }
        
        .table-badge.badge-danger {
            background: linear-gradient(45deg, #dc3545, #e83e8c);
            color: white;
        }
        
        .table-badge.badge-info {
            background: linear-gradient(45deg, #17a2b8, #6f42c1);
            color: white;
        }
        
        .table-badge.badge-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
            color: white;
        }
        
        /* User avatar in tables */
        .table-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e9ecef;
        }
        
        /* ID column styling */
        .table-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        /* Email links */
        .table-email {
            color: #007bff;
            text-decoration: none;
        }
        
        .table-email:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        /* Date/time formatting */
        .table-date {
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        /* Address formatting in tables */
        .table-address {
            line-height: 1.3;
            font-size: 0.85rem;
        }
        
        /* Responsive table wrapper */
        .table-responsive-modern {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }
        
        /* Card styling for tables */
        .card-table {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .card-table .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 20px;
        }
        
        .card-table .card-body {
            padding: 0;
        }
        
        .card-table .card-title {
            margin: 0;
            font-weight: 600;
        }
        
        /* Loading state */
        .table-loading {
            position: relative;
        }
        
        .table-loading::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            z-index: 999;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .table-modern td,
            .table-modern th {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .table-actions .btn {
                padding: 2px 6px;
                font-size: 0.7rem;
            }
            
            .table-badge {
                font-size: 0.65rem;
                padding: 3px 6px;
            }
        }
        
        .table th {
            border-top: none;
        }
        
        .btn {
            border-radius: 3px;
        }
        
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        .small-box {
            border-radius: 5px;
        }
        
        .small-box .icon {
            top: -10px;
            font-size: 70px;
        }
        
        /* Status badges */
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #28a745; color: white; }
        .status-quoted { background-color: #ffc107; color: #212529; }
        .status-completed { background-color: #007bff; color: white; }
        .status-closed { background-color: #dc3545; color: white; }
        
        /* Risk indicators */
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
        
        /* Custom tabs */
        .nav-tabs .nav-link {
            border-radius: 0;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .small-box .icon {
                font-size: 50px;
                top: -5px;
            }
            
            .content-header h1 {
                font-size: 1.5rem;
            }
            
            .col-lg-3 {
                margin-bottom: 15px;
            }
        }
    </style>
    @yield('custom_css')
@stop

@section('js')
    {{-- Add here extra JS --}}
    <script>
        // Global CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);
        
        // Confirm deletion actions
        $(document).on('click', '.btn-delete', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @yield('custom_js')
@stop

@section('adminlte_js')
    @parent
    {{-- Custom AdminLTE JS overrides --}}
@stop

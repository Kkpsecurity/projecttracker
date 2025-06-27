@extends('layouts.admin')

@section('title', 'HB837 Management')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">HB837 Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">HB837</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    @php
        // Use the active_tab passed from controller, or determine from URL as fallback
        $active_tab = $active_tab ?? ucfirst(strtolower(Request()->segment(4) ?? 'active'));
        $tabs = [
            'Active' => [
                'icon' => 'fas fa-play-circle',
                'name' => 'Active',
                'class' => 'btn-success'
            ],
            'Quoted' => [
                'icon' => 'fas fa-quote-right',
                'name' => 'Quoted',
                'class' => 'btn-warning'
            ],
            'Completed' => [
                'icon' => 'fas fa-check-circle',
                'name' => 'Completed',
                'class' => 'btn-primary'
            ],
            'Closed' => [
                'icon' => 'fas fa-times-circle',
                'name' => 'Closed',
                'class' => 'btn-secondary'
            ],
        ];
        
        // Ensure active_tab exists in tabs array, fallback to 'Active'
        if (!isset($tabs[$active_tab])) {
            $active_tab = 'Active';
        }
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="{{ $tabs[$active_tab]['icon'] }}"></i>
                                {{ $tabs[$active_tab]['name'] }} Records
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.hb837.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add New Record
                                </a>
                                <button type="button" class="btn btn-info" id="import-btn" onclick="openImportModal()">
                                    <i class="fas fa-exchange-alt"></i> Import/Export
                                </button>
                                <a href="{{ route('admin.hb837.backup.dashboard') }}" class="btn btn-warning">
                                    <i class="fas fa-database"></i> Backup
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($tabs as $key => $tabData)
                                <li class="nav-item">
                                    <a href="{{ route('admin.hb837.tabs', ['tab' => $key]) }}" 
                                       class="nav-link {{ $active_tab == $key ? 'active' : '' }}">
                                        <i class="{{ $tabData['icon'] }}"></i>
                                        {{ $tabData['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Records Table -->
                    <div class="table-responsive-modern">
                        <table class="table table-modern table-compact" id="hb837-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Property ID</th>
                                    <th>Property Name</th>
                                    <th>Owner Name</th>
                                    <th>Address</th>
                                    <th style="width: 120px;">Consultant</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 100px;">Crime Risk</th>
                                    <th style="width: 120px;">Updated</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mt-3">
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Records</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-play-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active</span>
                    <span class="info-box-number">{{ $stats['active'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-quote-right"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quoted</span>
                    <span class="info-box-number">{{ $stats['quoted'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number">{{ $stats['completed'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the existing modals -->
    @include('partials.modals.export_import_modal')
@stop

@section('custom_css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .nav-tabs-custom {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs .nav-link {
            border-radius: 0;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #007bff;
            color: #007bff;
        }
        
        .nav-tabs .nav-link.active {
            background-color: transparent;
            color: #007bff;
            border-bottom-color: #007bff;
            font-weight: bold;
        }
        
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .btn-group-sm > .btn {
            margin: 0 1px;
        }
        
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
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
        
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
        
        /* Fix pagination styling */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .pagination {
            margin: 0;
        }
        
        .pagination .page-link {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-color: #dee2e6;
        }
        
        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        /* Fix any large icons in pagination */
        .pagination .page-link i,
        .pagination .page-link svg {
            font-size: 0.875rem !important;
            width: 1rem !important;
            height: 1rem !important;
        }
        
        /* Ensure no oversized icons anywhere in the table area */
        .card-footer i,
        .card-footer svg {
            font-size: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
        }
        
        /* Control table icons */
        .table i:not(.fa-2x):not(.fa-3x) {
            font-size: 0.875rem;
        }
        
        /* Empty state styling */
        .table .fa-2x {
            font-size: 2rem !important;
            color: #6c757d;
        }
        
        /* Action button icons */
        .btn-group-sm .btn i {
            font-size: 0.75rem;
        }
        
        /* Status badge icons */
        .table-badge i,
        .risk-low i,
        .risk-medium i,
        .risk-high i {
            font-size: 0.625rem;
            margin-right: 2px;
        }
    </style>
@stop

@section('custom_js')
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        // Simple modal function
        function openImportModal() {
            console.log('openImportModal called directly');
            
            // Force show the modal with all possible methods
            try {
                console.log('Trying jQuery modal show...');
                $('#export_import_modal').modal('show');
            } catch (e) {
                console.error('jQuery modal failed:', e);
            }
            
            try {
                console.log('Trying direct style manipulation...');
                document.getElementById('export_import_modal').style.display = 'block';
                document.getElementById('export_import_modal').classList.add('show');
                
                // Add backdrop
                let backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'modal-backdrop-custom';
                document.body.appendChild(backdrop);
                
            } catch (e) {
                console.error('Direct manipulation failed:', e);
            }
        }
        
        $(document).ready(function() {
            // Debug modal trigger comprehensive
            console.log('=== HB837 Modal Debugging ===');
            console.log('jQuery loaded:', typeof $);
            console.log('Bootstrap modal available:', typeof $.fn.modal);
            console.log('Modal element count:', $('#export_import_modal').length);
            console.log('Button element count:', $('#import-btn').length);
            console.log('Modal HTML exists:', $('#export_import_modal').html() ? 'YES' : 'NO');
            
            // Check modal state
            if ($('#export_import_modal').length > 0) {
                console.log('Modal classes:', $('#export_import_modal').attr('class'));
                console.log('Modal display style:', $('#export_import_modal').css('display'));
            }
            
            // Add click handler with comprehensive debugging
            $('#import-btn').on('click', function(e) {
                console.log('=== Button Click Event ===');
                console.log('Event triggered at:', new Date().toISOString());
                console.log('Button element:', this);
                console.log('Event object:', e);
                
                // Try manual modal show
                try {
                    console.log('Attempting manual modal show...');
                    $('#export_import_modal').modal('show');
                    console.log('Modal show command completed');
                    
                    // Check if modal is visible after a delay
                    setTimeout(function() {
                        const isVisible = $('#export_import_modal').is(':visible');
                        const hasShowClass = $('#export_import_modal').hasClass('show');
                        console.log('Modal visible after 500ms:', isVisible);
                        console.log('Modal has show class:', hasShowClass);
                        console.log('Modal display style after show:', $('#export_import_modal').css('display'));
                    }, 500);
                    
                } catch (error) {
                    console.error('Error in modal show:', error);
                }
            });
            
            // Test function
            window.debugModal = function() {
                console.log('Manual test function called');
                $('#export_import_modal').modal('show');
            };
            
            // Initialize DataTable with advanced features
            var table = $('#hb837-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('admin.hb837.datatable', $tab ?? 'active') }}",
                    "type": "POST",
                    "data": function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.tab = '{{ $tab ?? "active" }}';
                    }
                },
                "columns": [
                    {"data": "id", "name": "id", "searchable": false},
                    {"data": "property_name", "name": "property_name"},
                    {"data": "owner_name", "name": "owner_name"},
                    {"data": "address", "name": "address", "orderable": false},
                    {"data": "consultant", "name": "consultant.name", "orderable": false},
                    {"data": "report_status", "name": "report_status"},
                    {"data": "crime_risk", "name": "securitygauge_crime_risk"},
                    {"data": "updated_at", "name": "updated_at"},
                    {"data": "actions", "name": "actions", "searchable": false, "orderable": false}
                ],
                "order": [[ 7, "desc" ]], // Sort by updated_at by default
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "processing": '<i class="fas fa-spinner fa-spin"></i> Loading...',
                    "emptyTable": '<div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-3"></i><br>No HB837 records found for this status.<br><a href="{{ route('admin.hb837.create') }}" class="btn btn-success btn-sm mt-2"><i class="fas fa-plus"></i> Add First Record</a></div>',
                    "zeroRecords": '<div class="text-center text-muted py-4"><i class="fas fa-search fa-2x mb-3"></i><br>No matching records found.</div>'
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                       '<"row"<"col-sm-12"tr>>' +
                       '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "buttons": [
                    {
                        text: '<i class="fas fa-download"></i> Export',
                        className: 'btn btn-primary btn-sm',
                        action: function() {
                            window.open('{{ route('admin.hb837.export', $tab ?? 'active') }}', '_blank');
                        }
                    },
                    {
                        text: '<i class="fas fa-upload"></i> Import',
                        className: 'btn btn-success btn-sm',
                        action: function() {
                            openImportModal();
                        }
                    }
                ]
            });
            
            // Confirm deletion
            $(document).on('click', '.btn-delete', function(e) {
                if (!confirm('Are you sure you want to delete this HB837 record? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Auto-refresh every 5 minutes
            setInterval(function() {
                if (!$('.modal').hasClass('show')) { // Don't refresh if modal is open
                    window.location.reload();
                }
            }, 300000); // 5 minutes
        });
    </script>
@stop

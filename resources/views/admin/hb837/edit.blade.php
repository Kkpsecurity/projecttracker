@extends('adminlte::page')

@section('title', 'Edit HB837 Record - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit HB837 Record</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">Edit Record</li>
            </ol>
        </div>
    </div>
@stop

@section('css')
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
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit: {{ $hb837->property_name }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.hb837.pdf-report', $hb837->id) }}" class="btn btn-danger" target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF Report
                            </a>
                            <a href="{{ route('admin.hb837.show', $hb837->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.hb837.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.hb837.partials.alerts')

                        <form action="{{ route('admin.hb837.update', $hb837->id) }}" method="POST" id="hb837-edit-form">
                            @csrf
                            @method('PUT')

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" id="editTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'general' ? 'active' : '' }}" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="{{ $tab === 'general' ? 'true' : 'false' }}">
                                        <i class="fas fa-home"></i> General
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'address' ? 'active' : '' }}" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="{{ $tab === 'address' ? 'true' : 'false' }}">
                                        <i class="fas fa-map-marker-alt"></i> Address
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'contact' ? 'active' : '' }}" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="{{ $tab === 'contact' ? 'true' : 'false' }}">
                                        <i class="fas fa-users"></i> Contact
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'financial' ? 'active' : '' }}" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="{{ $tab === 'financial' ? 'true' : 'false' }}">
                                        <i class="fas fa-dollar-sign"></i> Financial
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'notes' ? 'active' : '' }}" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="{{ $tab === 'notes' ? 'true' : 'false' }}">
                                        <i class="fas fa-sticky-note"></i> Notes
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'files' ? 'active' : '' }}" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="{{ $tab === 'files' ? 'true' : 'false' }}">
                                        <i class="fas fa-file-alt"></i> Files
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'maps' ? 'active' : '' }}" id="maps-tab" data-toggle="tab" href="#maps" role="tab" aria-controls="maps" aria-selected="{{ $tab === 'maps' ? 'true' : 'false' }}">
                                        <i class="fas fa-map"></i> Maps
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab === 'custom' ? 'active' : '' }}" id="custom-tab" data-toggle="tab" href="#custom" role="tab" aria-controls="custom" aria-selected="{{ $tab === 'custom' ? 'true' : 'false' }}">
                                        <i class="fas fa-wrench"></i> Custom Fields
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content" id="editTabsContent">
                                <!-- General Tab -->
                                <div class="tab-pane fade {{ $tab === 'general' ? 'show active' : '' }}" id="general" role="tabpanel" aria-labelledby="general-tab">
                                    @include('admin.hb837.partials.tabs.general')
                                </div>

                                <!-- Contacts Tab -->
                                <div class="tab-pane fade {{ $tab === 'contact' ? 'show active' : '' }}" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                    @include('admin.hb837.partials.tabs.contact')
                                </div>

                                <!-- Financial Tab -->
                                <div class="tab-pane fade {{ $tab === 'financial' ? 'show active' : '' }}" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                                    @include('admin.hb837.partials.tabs.financial')
                                </div>

                                <!-- Address Tab -->
                                <div class="tab-pane fade {{ $tab === 'address' ? 'show active' : '' }}" id="address" role="tabpanel" aria-labelledby="address-tab">
                                    @include('admin.hb837.partials.tabs.address')
                                </div>

                                <!-- Notes Tab -->
                                <div class="tab-pane fade {{ $tab === 'notes' ? 'show active' : '' }}" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                                    @include('admin.hb837.partials.tabs.notes')
                                </div>

                                <!-- Files Tab -->
                                <div class="tab-pane fade {{ $tab === 'files' ? 'show active' : '' }}" id="files" role="tabpanel" aria-labelledby="files-tab">
                                    @include('admin.hb837.partials.tabs.files')
                                </div>

                                <!-- Maps Tab -->
                                <div class="tab-pane fade {{ $tab === 'maps' ? 'show active' : '' }}" id="maps" role="tabpanel" aria-labelledby="maps-tab">
                                    @include('admin.hb837.partials.tabs.maps')
                                </div>

                                <!-- Custom Fields Tab -->
                                <div class="tab-pane fade {{ $tab === 'custom' ? 'show active' : '' }}" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                                    @include('admin.hb837.partials.tabs.custom')
                                </div>
                            </div>

                            <div class="form-group text-right mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Record
                                </button>
                                <a href="{{ route('admin.hb837.show', $hb837->id) }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Upload Modal -->
    @include('admin.hb837.partials.modals.file-upload')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const storageKey = 'hb837_edit_active_tab_{{ $hb837->id }}';

            // Auto-calculate project net profit
            function calculateNetProfit() {
                let quoted = parseFloat($('#quoted_price').val()) || 0;
                let expenses = parseFloat($('#sub_fees_estimated_expenses').val()) || 0;
                let profit = quoted - expenses;
                
                // Update the net profit field
                $('#project_net_profit').val(profit.toFixed(2));
                
                // Add visual feedback
                const $profitField = $('#project_net_profit');
                $profitField.removeClass('text-success text-danger text-warning');
                
                if (quoted > 0 && expenses > 0) {
                    if (profit > 0) {
                        $profitField.addClass('text-success');
                    } else if (profit < 0) {
                        $profitField.addClass('text-danger');
                    } else {
                        $profitField.addClass('text-warning');
                    }
                }
            }
            
            // Bind calculation to input events
            $('#quoted_price, #sub_fees_estimated_expenses').on('input change', calculateNetProfit);
            
            // Calculate on page load if values exist
            calculateNetProfit();

            // Initialize Bootstrap tabs
            $('#editTabs a[data-toggle="tab"]').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Handle tab switching with URL updates and localStorage
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr("href");
                const tabName = target.substring(1); // Remove the # symbol

                // Save to localStorage
                localStorage.setItem(storageKey, tabName);

                // Update URL without page reload
                const baseUrl = "{{ route('admin.hb837.edit', $hb837->id) }}";
                const newUrl = baseUrl + (tabName !== 'general' ? '/' + tabName : '');
                window.history.pushState({path: newUrl}, '', newUrl);

                console.log('Tab switched to:', tabName);
            });

            // Determine which tab to activate
            function getActiveTab() {
                // Priority: URL parameter > localStorage > default (general)
                const urlTab = "{{ $tab }}";
                const savedTab = localStorage.getItem(storageKey);

                console.log('URL tab:', urlTab);
                console.log('Saved tab:', savedTab);

                // If URL has a specific tab, use it
                if (urlTab && urlTab !== 'general') {
                    return urlTab;
                }

                // If no URL tab but we have a saved tab, use it
                if (savedTab && savedTab !== 'general') {
                    return savedTab;
                }

                // Default to general
                return 'general';
            }

            // Set active tab based on priority logic
            const activeTab = getActiveTab();
            console.log('Activating tab:', activeTab);

            if (activeTab && activeTab !== 'general') {
                // Use Bootstrap's tab method to properly show the tab
                const tabLink = '#' + activeTab + '-tab';
                console.log('Trying to activate tab link:', tabLink);
                if ($(tabLink).length > 0) {
                    $(tabLink).tab('show');
                    console.log('Tab activated:', tabLink);
                } else {
                    console.error('Tab link not found:', tabLink);
                    // Fallback to general tab
                    $('#general-tab').tab('show');
                }
            } else {
                // Show general tab by default
                $('#general-tab').tab('show');
                console.log('Default general tab activated');
            }

            // File upload modal functionality
            $('#uploadModal').on('show.bs.modal', function (event) {
                // Modal setup code here
            });

            // Delete file function
            window.deleteFile = function(fileId) {
                if (confirm('Are you sure you want to delete this file?')) {
                    $.ajax({
                        url: '/admin/hb837/files/' + fileId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success('File deleted successfully');
                            } else {
                                alert('File deleted successfully');
                            }
                            location.reload();
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Unknown error';
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Error deleting file: ' + message);
                            } else {
                                alert('Error deleting file: ' + message);
                            }
                        }
                    });
                }
            };

            // Enhanced form validation and submission
            $('#hb837-edit-form').on('submit', function(e) {
                const requiredFields = ['property_name'];
                let isValid = true;

                requiredFields.forEach(function(field) {
                    const $field = $('#' + field);
                    if ($field.length && !$field.val().trim()) {
                        $field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        $field.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (typeof toastr !== 'undefined') {
                        toastr.warning('Please fill in all required fields');
                    } else {
                        alert('Please fill in all required fields');
                    }
                    return false;
                }

                // Store current tab before form submission
                const currentActiveTab = $('.nav-link.active').attr('href');
                if (currentActiveTab) {
                    const tabName = currentActiveTab.substring(1);
                    localStorage.setItem(storageKey, tabName);
                    console.log('Stored tab before form submission:', tabName);
                }
            });

            // Clear localStorage when navigating away from edit page
            window.addEventListener('beforeunload', function() {
                // Only clear if we're navigating to a different page (not submitting form)
                if (!$('#hb837-edit-form').data('submitting')) {
                    // Don't clear - keep the tab preference
                    // localStorage.removeItem(storageKey);
                }
            });

            // Mark form as submitting to avoid clearing localStorage
            $('#hb837-edit-form').on('submit', function() {
                $(this).data('submitting', true);
            });
        });
    </script>
@stop

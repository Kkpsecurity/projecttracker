@extends('adminlte::page')

@section('plugins.Datatables', true)

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
    .tab-pane {
        padding: 1.5rem;
    }

    /* Keep styling theme-safe (no hard-coded colors). */
    .nav-tabs .nav-link {
        font-weight: 600;
    }

    .nav-tabs .nav-link i {
        margin-right: 0.25rem;
    }

    .tab-content {
        min-height: 400px;
    }

    /* Date validation styling */
    input[type="date"].date-error {
        border: 2px solid #dc3545 !important;
        background-color: #f8d7da !important;
    }

    input[type="date"].date-warning {
        border: 2px solid #ffc107 !important;
        background-color: #fff3cd !important;
    }

    input[type="date"].date-info {
        border: 2px solid #17a2b8 !important;
        background-color: #d1ecf1 !important;
    }

    input[type="date"].date-old {
        border: 2px solid #6c757d !important;
        background-color: #e2e3e5 !important;
    }

    .date-issue-badge {
        display: inline-block;
        margin-top: 0.25rem;
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
                            <a href="{{ route('admin.hb837.pdf-report', ['hb837' => $hb837->id]) }}" class="btn btn-danger" target="_blank" title="Generate HB837 report PDF">
                                <i class="fas fa-file-pdf"></i> HB837 PDF
                            </a>
                            <a href="{{ route('admin.hb837.pdf-report', ['hb837' => $hb837->id, 'mode' => 'appendix']) }}" class="btn btn-warning" target="_blank" title="Generate Crime Report PDF (Appendix page)">
                                <i class="fas fa-file-alt"></i> Crime Report PDF
                            </a>
                            <a href="{{ route('admin.hb837.pdf-report', ['hb837' => $hb837->id, 'mode' => 'crime_records']) }}" class="btn btn-secondary" target="_blank" title="Generate Crime Report Records PDF">
                                <i class="fas fa-file-medical"></i> Crime Records PDF
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
                            <ul class="nav nav-tabs bg-dark px-2 pt-2" id="editTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'general' ? 'active' : '' }}" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="{{ $tab === 'general' ? 'true' : 'false' }}" title="General">
                                        <i class="fas fa-home"></i> Gen
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'address' ? 'active' : '' }}" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="{{ $tab === 'address' ? 'true' : 'false' }}" title="Address">
                                        <i class="fas fa-map-marker-alt"></i> Addr
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'contact' ? 'active' : '' }}" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="{{ $tab === 'contact' ? 'true' : 'false' }}" title="Contact">
                                        <i class="fas fa-users"></i> Contact
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'financial' ? 'active' : '' }}" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="{{ $tab === 'financial' ? 'true' : 'false' }}" title="Financial">
                                        <i class="fas fa-dollar-sign"></i> Fin
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'notes' ? 'active' : '' }}" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="{{ $tab === 'notes' ? 'true' : 'false' }}" title="Notes">
                                        <i class="fas fa-sticky-note"></i> Notes
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'findings' ? 'active' : '' }}" id="findings-tab" data-toggle="tab" href="#findings" role="tab" aria-controls="findings" aria-selected="{{ $tab === 'findings' ? 'true' : 'false' }}" title="Findings">
                                        <i class="fas fa-clipboard-list"></i> Findings
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'risk-measures' ? 'active' : '' }}" id="risk-measures-tab" data-toggle="tab" href="#risk-measures" role="tab" aria-controls="risk-measures" aria-selected="{{ $tab === 'risk-measures' ? 'true' : 'false' }}" title="Risk Measures">
                                        <i class="fas fa-list-ol"></i> Risk
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'recent-incidents' ? 'active' : '' }}" id="recent-incidents-tab" data-toggle="tab" href="#recent-incidents" role="tab" aria-controls="recent-incidents" aria-selected="{{ $tab === 'recent-incidents' ? 'true' : 'false' }}" title="Recent Incidents">
                                        <i class="fas fa-exclamation-circle"></i> Incidents
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'statute-conditions' ? 'active' : '' }}" id="statute-conditions-tab" data-toggle="tab" href="#statute-conditions" role="tab" aria-controls="statute-conditions" aria-selected="{{ $tab === 'statute-conditions' ? 'true' : 'false' }}" title="Statute Conditions">
                                        <i class="fas fa-balance-scale"></i> Statutes
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'crime' ? 'active' : '' }}" id="crime-tab" data-toggle="tab" href="#crime" role="tab" aria-controls="crime" aria-selected="{{ $tab === 'crime' ? 'true' : 'false' }}" title="Crime Stats">
                                        <i class="fas fa-shield-alt"></i> Crime
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'files' ? 'active' : '' }}" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="{{ $tab === 'files' ? 'true' : 'false' }}" title="Crime Report Files">
                                        <i class="fas fa-file-alt"></i> Crime Files
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-light {{ $tab === 'maps' ? 'active' : '' }}" id="maps-tab" data-toggle="tab" href="#maps" role="tab" aria-controls="maps" aria-selected="{{ $tab === 'maps' ? 'true' : 'false' }}" title="Maps">
                                        <i class="fas fa-map"></i> Maps
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content bg-dark text-light border border-top-0 border-secondary rounded-bottom" id="editTabsContent">
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

                                <!-- Findings Tab -->
                                <div class="tab-pane fade {{ $tab === 'findings' ? 'show active' : '' }}" id="findings" role="tabpanel" aria-labelledby="findings-tab">
                                    @include('admin.hb837.partials.tabs.findings')
                                </div>

                                <!-- Risk Measures Tab -->
                                <div class="tab-pane fade {{ $tab === 'risk-measures' ? 'show active' : '' }}" id="risk-measures" role="tabpanel" aria-labelledby="risk-measures-tab">
                                    @include('admin.hb837.partials.tabs.risk-measures')
                                </div>

                                <!-- Recent Incidents Tab -->
                                <div class="tab-pane fade {{ $tab === 'recent-incidents' ? 'show active' : '' }}" id="recent-incidents" role="tabpanel" aria-labelledby="recent-incidents-tab">
                                    @include('admin.hb837.partials.tabs.recent-incidents')
                                </div>

                                <!-- Statute Conditions Tab -->
                                <div class="tab-pane fade {{ $tab === 'statute-conditions' ? 'show active' : '' }}" id="statute-conditions" role="tabpanel" aria-labelledby="statute-conditions-tab">
                                    @include('admin.hb837.partials.tabs.statute-conditions')
                                </div>

                                <!-- Crime Stats Tab -->
                                <div class="tab-pane fade {{ $tab === 'crime' ? 'show active' : '' }}" id="crime" role="tabpanel" aria-labelledby="crime-tab">
                                    @include('admin.hb837.partials.tabs.crime')
                                </div>

                                <!-- Files Tab -->
                                <div class="tab-pane fade {{ $tab === 'files' ? 'show active' : '' }}" id="files" role="tabpanel" aria-labelledby="files-tab">
                                    @include('admin.hb837.partials.tabs.files')
                                </div>

                                <!-- Maps Tab -->
                                <div class="tab-pane fade {{ $tab === 'maps' ? 'show active' : '' }}" id="maps" role="tabpanel" aria-labelledby="maps-tab">
                                    @include('admin.hb837.partials.tabs.maps')
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
        (function initHb837EditPage(attempt) {
            attempt = attempt || 0;

            // Some layouts load jQuery after inline scripts; avoid "$ is not defined".
            if (!window.jQuery) {
                if (attempt < 100) {
                    return window.setTimeout(function() { initHb837EditPage(attempt + 1); }, 50);
                }
                return;
            }

            var $ = window.jQuery;

            // Date validation function to detect bad dates
            function validateDateField($input) {
                const value = $input.val();
                if (!value) {
                    $input.removeClass('date-error date-warning date-info date-old');
                    $input.next('.date-issue-badge').remove();
                    return;
                }

                const date = new Date(value);
                const year = date.getFullYear();
                const now = new Date();
                const tenYearsAgo = new Date();
                tenYearsAgo.setFullYear(tenYearsAgo.getFullYear() - 10);
                
                // Remove existing classes and badges
                $input.removeClass('date-error date-warning date-info date-old');
                $input.next('.date-issue-badge').remove();
                
                let badgeHtml = '';
                let cssClass = '';
                
                // Check for date issues in priority order
                if (year === 1970) {
                    cssClass = 'date-error';
                    badgeHtml = '<span class="badge badge-danger date-issue-badge ml-1" title="1970 epoch date - likely NULL conversion issue">⚠ 1970 Epoch Date</span>';
                } else if (year < 1980) {
                    cssClass = 'date-error';
                    badgeHtml = '<span class="badge badge-danger date-issue-badge ml-1" title="Pre-1980 date - likely data error">⚠ Pre-1980 Date (' + year + ')</span>';
                } else if (date > now) {
                    cssClass = 'date-info';
                    badgeHtml = '<span class="badge badge-info date-issue-badge ml-1" title="Future date">📅 Future Date</span>';
                } else if (date < tenYearsAgo) {
                    cssClass = 'date-old';
                    badgeHtml = '<span class="badge badge-warning date-issue-badge ml-1" title="Date is more than 10 years old">⏰ Old Date (>10 years)</span>';
                }
                
                // Check for backwards logic (report_submitted before scheduled_date_of_inspection)
                if ($input.attr('name') === 'report_submitted') {
                    const scheduledVal = $('#scheduled_date_of_inspection').val();
                    if (scheduledVal) {
                        const scheduledDate = new Date(scheduledVal);
                        if (date < scheduledDate && year >= 1980) {  // Only check if not already flagged for other issues
                            cssClass = 'date-error';
                            badgeHtml = '<span class="badge badge-danger date-issue-badge ml-1" title="Report submitted before inspection scheduled">⚠ Backwards Logic</span>';
                        }
                    }
                }
                
                if (cssClass) {
                    $input.addClass(cssClass);
                }
                
                if (badgeHtml) {
                    $input.after(badgeHtml);
                }
            }

            // Check backwards logic for both date fields
            function checkBackwardsLogic() {
                const scheduledVal = $('#scheduled_date_of_inspection').val();
                const reportVal = $('#report_submitted').val();
                
                if (scheduledVal && reportVal) {
                    const scheduledDate = new Date(scheduledVal);
                    const reportDate = new Date(reportVal);
                    
                    // Re-validate report_submitted field to show backwards logic warning
                    validateDateField($('#report_submitted'));
                }
            }

            function initSlotImagesTable() {
                if (!$.fn || !$.fn.DataTable) {
                    return;
                }

                var $table = $('#hb837-slot-images-table');
                if ($table.length === 0) {
                    return;
                }

                // Avoid double-init when switching tabs.
                if ($.fn.DataTable.isDataTable($table)) {
                    return;
                }

                $table.DataTable({
                    paging: true,
                    pageLength: 10,
                    lengthChange: false,
                    searching: false,
                    info: true,
                    ordering: true,
                    autoWidth: false,
                    // Show only table + info + pagination (no search box).
                    dom: "<'row'<'col-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    order: [[4, 'desc']],
                });
            }

            $(function() {
            const storageKey = 'hb837_edit_active_tab_{{ $hb837->id }}';

            // Validate all date fields on page load
            $('input[type="date"]').each(function() {
                validateDateField($(this));
            });

            // Validate date fields on change
            $('input[type="date"]').on('change', function() {
                validateDateField($(this));
                // Also check backwards logic when either date changes
                if ($(this).attr('name') === 'scheduled_date_of_inspection' || $(this).attr('name') === 'report_submitted') {
                    checkBackwardsLogic();
                }
            });

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

                if (tabName === 'files') {
                    initSlotImagesTable();
                }

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

            // Crime stats tab: save via AJAX to avoid nested forms.
            $(document).on('click', '#save-crime-stats', function () {
                const url = $(this).data('update-url');
                const csrf = $('#crimeStatsCsrf').val() || $('meta[name="csrf-token"]').attr('content');

                const offenses = [];
                $('#crime-offenses-table tbody tr').each(function () {
                    const $row = $(this);
                    const label = $row.find('.crime-offense-label').val();
                    const category = $row.find('.crime-offense-category').val();
                    const count = $row.find('.crime-offense-count').val();
                    const rate = $row.find('.crime-offense-rate').val();
                    const stateRate = $row.find('.crime-offense-state-rate').val();
                    const nationalRate = $row.find('.crime-offense-national-rate').val();
                    const compare = $row.find('.crime-offense-compare').val();
                    const sg = $row.find('.crime-offense-sg').val();

                    offenses.push({
                        label: label,
                        category: category,
                        count: count === '' ? null : parseInt(count, 10),
                        rate_per_1000: rate === '' ? null : parseFloat(rate),
                        state_rate_per_1000: stateRate === '' ? null : parseFloat(stateRate),
                        national_rate_per_1000: nationalRate === '' ? null : parseFloat(nationalRate),
                        comparison_to_state: compare === '' ? null : compare,
                        securitygauge_score: sg === '' ? null : parseInt(sg, 10),
                    });
                });

                const payload = {
                    report_title: $('#crime_report_title').val(),
                    period_start: $('#crime_period_start').val(),
                    period_end: $('#crime_period_end').val(),
                    crime_risk: $('#crime_risk').val(),
                    is_reviewed: $('#crime_is_reviewed').is(':checked') ? 1 : 0,
                    offenses: offenses,
                    crime_risk_trends: {
                        total_crime: $('#crime_trend_total').val() || null,
                        violent_crime: $('#crime_trend_violent').val() || null,
                        property_crime: $('#crime_trend_property').val() || null,
                    },
                };

                const $alert = $('#crime-stats-alert');
                $alert.removeClass('d-none alert-success alert-danger').addClass('alert-info').text('Saving...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    data: payload,
                    success: function (resp) {
                        $alert.removeClass('alert-info alert-danger').addClass('alert-success').text(resp.message || 'Saved.');
                    },
                    error: function (xhr) {
                        let message = 'Failed to save crime stats.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const firstKey = Object.keys(errors)[0];
                            if (firstKey && errors[firstKey] && errors[firstKey][0]) {
                                message = errors[firstKey][0];
                            }
                        }

                        $alert.removeClass('alert-info alert-success').addClass('alert-danger').text(message);
                    }
                });
            });
            });
        })(0);
    </script>
@stop

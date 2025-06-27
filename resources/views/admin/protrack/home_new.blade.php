@extends('layouts.admin')

@section('title', 'ProTrack Projects')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">ProTrack Projects</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">ProTrack Projects</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    @php
        $active_tab = Request()->segment(4) ?? 'opp';
        $tabs = [
            'opp' => [
                'icon' => 'fa fa-file',
                'name' => 'Opportunities Pipeline',
                'class' => 'btn-info'
            ],
            'active' => [
                'icon' => 'fa fa-check',
                'name' => 'Active Projects',
                'class' => 'btn-success'
            ],
            'closed' => [
                'icon' => 'fa fa-ban',
                'name' => 'Closed Projects',
                'class' => 'btn-secondary'
            ],
            'completed' => [
                'icon' => 'fa fa-star',
                'name' => 'Completed Projects',
                'class' => 'btn-primary'
            ],
        ];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="{{ $tabs[$active_tab]['icon'] }}"></i>
                                {{ $tabs[$active_tab]['name'] }}
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create_project">
                                <i class="fas fa-plus"></i> Create New Project
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($tabs as $key => $tab)
                                <li class="nav-item">
                                    <a href="{{ route('admin.home.tabs', ['tab' => $key]) }}"
                                       class="nav-link {{ $active_tab == $key ? 'active' : '' }}">
                                        <i class="{{ $tab['icon'] }}"></i>
                                        {{ $tab['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Projects Table -->
                    <div class="table-responsive">
                        <table id="protrack-table" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Corporate Name</th>
                                    <th>Client Name</th>
                                    <th>Project Name</th>
                                    <th>Status</th>
                                    <th>Quick Status</th>
                                    <th>Updated</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Project Modal -->
    <div class="modal fade" id="create_project" tabindex="-1" role="dialog" aria-labelledby="createProjectLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.home.process_new') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectLabel">
                            <i class="fas fa-plus-circle"></i> Create New Project
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="corporate_name">Corporate Name <span class="text-danger">*</span></label>
                                    <input type="text" name="corporate_name" id="corporate_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_name">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" name="client_name" id="client_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="project_name">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" name="project_name" id="project_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="New Lead">New Lead</option>
                                        <option value="Proposal Sent">Proposal Sent</option>
                                        <option value="Contracting Now">Contracting Now</option>
                                        <option value="Active">Active</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quick_status">Quick Status</label>
                            <input type="text" name="quick_status" id="quick_status" class="form-control"
                                   placeholder="Brief status description">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('custom_css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

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
        }

        .btn-group-sm > .btn {
            margin: 0 1px;
        }

        /* DataTables specific styles */
        .dataTables_wrapper {
            padding: 20px;
        }

        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 6px 12px;
        }

        .dataTables_length select {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }

        .dataTables_info {
            color: #6c757d;
        }

        .page-link {
            color: #007bff;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
@stop

@section('custom_js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Get current tab from URL
            const urlSegments = window.location.pathname.split('/');
            const currentTab = urlSegments[urlSegments.length - 1] || 'opp';

            // Initialize DataTable
            const protackTable = $('#protrack-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.home.datatable") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.tab = currentTab;
                        return d;
                    }
                },
                columns: [
                    { data: 'corporate_name', name: 'corporate_name' },
                    { data: 'client_name', name: 'client_name' },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'quick_status', name: 'quick_status', orderable: false },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']], // Order by updated_at descending
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="fas fa-inbox fa-3x mb-3"></i><br>No projects found for this category.</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="fas fa-search fa-3x mb-3"></i><br>No matching projects found.</div>'
                },
                drawCallback: function(settings) {
                    // Re-initialize tooltips after table redraw
                    $('[title]').tooltip();
                }
            });

            // Handle tab changes - reload DataTable with new tab data
            $('.nav-link[href*="/admin/home/tabs/"]').on('click', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                const newTab = href.split('/').pop();

                // Update active tab
                $('.nav-link').removeClass('active');
                $(this).addClass('active');

                // Update tab header
                const icon = $(this).find('i').attr('class');
                const name = $(this).text().trim();
                $('.card-title i').attr('class', icon);
                $('.card-title').html('<i class="' + icon + '"></i> ' + name);

                // Reload DataTable with new tab
                protackTable.ajax.url('{{ route("admin.home.datatable") }}?tab=' + newTab).load();

                // Update URL without page reload
                window.history.pushState({}, '', href);
            });

            // Auto-focus on first input when modal opens
            $('#create_project').on('shown.bs.modal', function () {
                $('#corporate_name').focus();
            });

            // Handle form submission
            $('#create_project form').on('submit', function(e) {
                let hasErrors = false;

                // Simple validation
                $(this).find('[required]').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        hasErrors = true;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    toastr.error('Please fill in all required fields.');
                } else {
                    // If form is valid, show success message and reload table after submission
                    $(this).on('submit', function() {
                        setTimeout(function() {
                            protackTable.ajax.reload();
                        }, 1000);
                    });
                }
            });

            // Remove validation styling on input
            $('#create_project input, #create_project select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });

            // Handle delete buttons with confirmation
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).attr('href');

                if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                    // You can implement the delete functionality here
                    // For now, just reload the table
                    protackTable.ajax.reload();
                }
            });
        });
    </script>
@stop

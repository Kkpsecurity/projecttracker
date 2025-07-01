@extends('adminlte::page')

@section('title', 'Manage Plots - Project Tracker')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-map-marker-alt"></i> Manage Plots</h1>
        <div>
            <a href="{{ route('admin.plots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Plot
            </a>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-info">
                <i class="fas fa-map"></i> View on Map
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Plots</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" id="bulk-actions-btn" style="display: none;">
                    <i class="fas fa-cogs"></i> Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="plots-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Plot Name</th>
                            <th>HB837 Project</th>
                            <th>Address</th>
                            <th>Coordinates</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Modal -->
    <div class="modal fade" id="bulk-actions-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Actions</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bulk-actions-form">
                        <div class="form-group">
                            <label for="bulk-action">Select Action:</label>
                            <select class="form-control" id="bulk-action" name="action" required>
                                <option value="">Choose an action...</option>
                                <option value="delete">Delete Selected</option>
                                <option value="export">Export Selected</option>
                                <option value="update_status">Update Status</option>
                            </select>
                        </div>
                        <div class="form-group" id="status-field" style="display: none;">
                            <label for="new-status">New Status:</label>
                            <select class="form-control" id="new-status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <input type="hidden" id="selected-ids" name="ids">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="execute-bulk-action">Execute</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th {
            white-space: nowrap;
        }

        .coordinate-badge {
            font-size: 0.8em;
            padding: 4px 8px;
        }

        .status-badge {
            text-transform: capitalize;
        }

        .action-buttons {
            white-space: nowrap;
        }

        .action-buttons .btn {
            margin-right: 2px;
            padding: 4px 8px;
            font-size: 12px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#plots-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.plots.datatable') }}",
                    type: 'GET'
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                        }
                    },
                    { data: 'id', name: 'id' },
                    {
                        data: 'plot_name',
                        name: 'plot_name',
                        render: function(data, type, row) {
                            return data || '<em class="text-muted">Unnamed Plot</em>';
                        }
                    },
                    {
                        data: 'hb837.project_name',
                        name: 'hb837.project_name',
                        render: function(data, type, row) {
                            if (data) {
                                return `<a href="/admin/hb837/${row.hb837.id}" class="text-primary">${data}</a>`;
                            }
                            return '<em class="text-muted">No project</em>';
                        }
                    },
                    {
                        data: 'plot_address',
                        name: 'plot_address.address_line_1',
                        render: function(data, type, row) {
                            if (data && data.address_line_1) {
                                return `${data.address_line_1}<br><small class="text-muted">${data.city}, ${data.state} ${data.zip_code}</small>`;
                            }
                            return '<em class="text-muted">No address</em>';
                        }
                    },
                    {
                        data: 'coordinates_latitude',
                        name: 'coordinates_latitude',
                        render: function(data, type, row) {
                            if (data && row.coordinates_longitude) {
                                return `<span class="badge badge-info coordinate-badge">${parseFloat(data).toFixed(4)}, ${parseFloat(row.coordinates_longitude).toFixed(4)}</span>`;
                            }
                            return '<span class="badge badge-secondary">Not mapped</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            const statusClass = {
                                'active': 'success',
                                'inactive': 'secondary',
                                'pending': 'warning'
                            };
                            return `<span class="badge badge-${statusClass[data] || 'secondary'} status-badge">${data || 'unknown'}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return new Date(data).toLocaleDateString();
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-buttons">
                                    <a href="/admin/plots/${data}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/plots/${data}/edit" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    ${row.coordinates_latitude ?
                                        `<a href="/admin/maps?plot=${data}" class="btn btn-success btn-sm" title="View on Map">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>` : ''
                                    }
                                    <button type="button" class="btn btn-danger btn-sm delete-plot" data-id="${data}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Select all checkbox
            $('#select-all').on('change', function() {
                $('.row-checkbox').prop('checked', this.checked);
                toggleBulkActions();
            });

            // Individual checkbox
            $(document).on('change', '.row-checkbox', function() {
                toggleBulkActions();

                // Update select all checkbox
                const totalRows = $('.row-checkbox').length;
                const checkedRows = $('.row-checkbox:checked').length;
                $('#select-all').prop('checked', totalRows === checkedRows);
            });

            // Toggle bulk actions button
            function toggleBulkActions() {
                const checkedRows = $('.row-checkbox:checked').length;
                if (checkedRows > 0) {
                    $('#bulk-actions-btn').show();
                } else {
                    $('#bulk-actions-btn').hide();
                }
            }

            // Bulk actions button click
            $('#bulk-actions-btn').on('click', function() {
                const selectedIds = $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                $('#selected-ids').val(selectedIds.join(','));
                $('#bulk-actions-modal').modal('show');
            });

            // Bulk action selection change
            $('#bulk-action').on('change', function() {
                if ($(this).val() === 'update_status') {
                    $('#status-field').show();
                } else {
                    $('#status-field').hide();
                }
            });

            // Execute bulk action
            $('#execute-bulk-action').on('click', function() {
                const action = $('#bulk-action').val();
                const ids = $('#selected-ids').val();

                if (!action) {
                    alert('Please select an action.');
                    return;
                }

                if (action === 'delete' && !confirm('Are you sure you want to delete the selected plots?')) {
                    return;
                }

                const formData = new FormData();
                formData.append('action', action);
                formData.append('ids', ids);
                if (action === 'update_status') {
                    formData.append('status', $('#new-status').val());
                }
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('admin.plots.bulk') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#bulk-actions-modal').modal('hide');
                        table.ajax.reload();
                        alert(response.message || 'Bulk action completed successfully.');

                        // Reset form
                        $('#bulk-actions-form')[0].reset();
                        $('#status-field').hide();
                        $('.row-checkbox').prop('checked', false);
                        $('#select-all').prop('checked', false);
                        toggleBulkActions();
                    },
                    error: function(xhr) {
                        alert('Error executing bulk action: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            });

            // Delete individual plot
            $(document).on('click', '.delete-plot', function() {
                const plotId = $(this).data('id');

                if (confirm('Are you sure you want to delete this plot?')) {
                    $.ajax({
                        url: `/admin/plots/${plotId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload();
                            alert('Plot deleted successfully.');
                        },
                        error: function(xhr) {
                            alert('Error deleting plot: ' + (xhr.responseJSON?.message || 'Unknown error'));
                        }
                    });
                }
            });
        });
    </script>
@stop

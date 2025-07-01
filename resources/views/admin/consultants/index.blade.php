@extends('adminlte::page')

@section('title', 'Consultants - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Consultant Records</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Consultants</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Consultant Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.consultants.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Consultant
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="consultants-table" class="table table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Company</th>
                                <th>FCP Status</th>
                                <th>Assignments</th>
                                <th>Files</th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Using the same color scheme as HB837 property records */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.02);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.035);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#consultants-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.consultants.index") }}',
            type: 'GET',
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'company', name: 'dba_company_name' },
            { data: 'fcp_status', name: 'fcp_expiration_date', orderable: false },
            { data: 'assignments', name: 'assignments', orderable: false, searchable: false },
            { data: 'files_count', name: 'files_count', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            emptyTable: 'No consultants found'
        }
    });

    // Select all checkbox functionality
    $('#select-all').on('click', function() {
        let isChecked = $(this).prop('checked');
        $('.consultant-checkbox').prop('checked', isChecked);
    });

    // Individual checkbox handling
    $(document).on('change', '.consultant-checkbox', function() {
        let totalCheckboxes = $('.consultant-checkbox').length;
        let checkedCheckboxes = $('.consultant-checkbox:checked').length;

        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Delete consultant functionality
    $(document).on('click', '.delete-consultant', function(e) {
        e.preventDefault();
        let consultantId = $(this).data('id');

        if (confirm('Are you sure you want to delete this consultant?')) {
            $.ajax({
                url: '{{ route("admin.consultants.destroy", ":id") }}'.replace(':id', consultantId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    alert('Consultant deleted successfully');
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || 'Error deleting consultant';
                    alert(message);
                }
            });
        }
    });
});
</script>
@stop

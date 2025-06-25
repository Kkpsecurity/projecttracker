<style>
    /* Improved table styling */
    .table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1rem;
        background-color: #ffffff;
        color: #212529;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }

    .table thead th {
        background-color: #11467a; /* Dark blue */
        color: #ffffff;
        text-align: left;
        font-weight: bold;
        font-size: 14px;
    }

    .table thead th a {
        color: #ffffff;
        text-decoration: none;
        font-size: 14px;
    }

    .table tbody tr:nth-of-type(odd) {
        background-color: #f8f9fa; /* Light gray */
    }

    .table tbody tr:nth-of-type(even) {
        background-color: #e9ecef; /* Slightly darker gray */
    }

    .table tbody tr:hover {
        background-color: #cfe2ff; /* Light blue hover */
    }

    .table th:last-child,
    .table td:last-child {
        text-align: center;
    }

    .table .alert {
        margin-bottom: 0;
        width: 100%;
    }

    /* Highlight selected row */
    .table tbody tr.selected {
        background-color: #ffe591 !important;
    }

    .table tbody tr.selected th {
        background-color: #ffe591 !important;
    }
</style>


<table class="table table-striped table-bordered mt-3">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Company Name</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($consultants as $consultant)
            <tr>
                <td>{{ $consultant->first_name }}</td>
                <td>{{ $consultant->last_name }}</td>
                <td>{{ $consultant->email }}</td>
                <td>{{ $consultant->dba_company_name }}</td>
                <td class="text-center">
                    <a href="{{ route('admin.consultants.edit', $consultant->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No Consultant records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($consultants->isNotEmpty())
    <div class="d-flex justify-content-center">
        {!! $consultants->links() !!}
    </div>
@endif

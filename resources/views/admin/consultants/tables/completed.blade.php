<style>
    /* Updated table styling */
    .table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }
    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border: 1px solid #dee2e6;
    }
    .table thead th {
        background-color: #0e3a60; /* Darker blue to match Properties records */
        color: #ffffff;
        text-align: left;
        font-weight: bold;
        font-size: 12px;
    }
    .table thead th a {
        color: #ffffff;
        text-decoration: none;
        font-size: 12px;
    }
    .table tbody tr:nth-of-type(odd) {
        background-color: #fdfdfd;
    }
    .table tbody tr:nth-of-type(even) {
        background-color: #f0f2f5; /* Light gray for better contrast */
    }
    .table tbody tr:hover {
        background-color: #e9ecef;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table th:last-child,
    .table td:last-child {
        text-align: center;
        vertical-align: middle;
    }
    .table .alert {
        margin-bottom: 0;
        width: 100%;
    }
    /* Highlight for selected row */
    .table tbody tr.selected {
        background-color: #ffe591 !important;
    }
    .table tbody tr.selected th {
        background-color: #ffe591 !important;
    }
</style>

@if ($completedOrders->isEmpty())
    <p class="alert alert-danger text-center">No Completed Project records found</p>
@else
    <div class="table-responsive">
        <table class="table table-light table-bordered mt-3">
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>Macro Client</th>
                    <th>County</th>
                    <th>Type</th>
                    <th>No. of Units</th>
                    <th>Date of Scheduled Assessment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completedOrders as $order)
                    <tr>
                        <td>{{ $order->property_name }}</td>
                        <td>{{ $order->macro_client }}</td>
                        <td>{{ $order->county }}</td>
                        <td>{{ ucwords(str_replace('-', ' ', $order->property_type)) }}</td>
                        <td>{{ $order->units }}</td>
                        <td>{{ $order->scheduled_date_of_inspection ? \Carbon\Carbon::parse($order->scheduled_date_of_inspection)->format('m/d/Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {!! $completedOrders->links() !!}
@endif

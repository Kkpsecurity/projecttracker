@php
    use App\Models\Consultant;
    use Carbon\Carbon;

    // Data Variable $hb837
    $active_table_fields = [
        'property_name',
        'county',
        'securitygauge_crime_risk',
        'macro_client',
        'assigned_consultant_id',
        'scheduled_date_of_inspection',
        'report_status',
    ];

    $quoted_table_fields = [
        'property_name',
        'county',
        'property_type',
        'units',
        'management_company',
        'quoted_price',
        'contracting_status',
        'macro_client',
    ];

    $completed_table_fields = [
        'property_name',
        'management_company',
        'macro_client',
        'report_submitted',
        'scheduled_date_of_inspection',
        'days_until_renewal',
    ];

    switch ($active_tab) {
        case 'Active':
            $table_fields = $active_table_fields;
            break;
        case 'Quoted':
            $table_fields = $quoted_table_fields;
            break;
        case 'Completed':
            $table_fields = $completed_table_fields;
            break;
        default:
            $table_fields = $active_table_fields;
            break;
    }

    function getRiskClass($value)
    {
        $value = strtolower($value);
        switch ($value) {
            case 'low':
                return 'risk-low';
            case 'moderate':
                return 'risk-moderate';
            case 'elevated':
                return 'risk-elevated';
            case 'high':
                return 'risk-high';
            case 'severe':
                return 'risk-severe';
            default:
                return '';
        }
    }

    function getStatusClass($value)
    {
        $value = strtolower($value);
        switch ($value) {
            case 'not-started':
                return 'status-not-started';
            case 'in-progress':
                return 'status-in-progress';
            case 'in-review':
                return 'status-in-review';
            case 'completed':
                return 'status-completed';
            default:
                return '';
        }
    }

    function formatField($field, $value, $row = [])
    {
        // Temporary variable for days until renewal
        $daysUntilRenewal = 'N/A';

        if (!empty($row['scheduled_date_of_inspection'])) {
            try {
                $inspectionDate = Carbon::parse($row['scheduled_date_of_inspection']);
                $renewalDate = $inspectionDate->addYears(3);
                $days = Carbon::now()->diffInDays($renewalDate, false);
                $daysUntilRenewal = $days >= 0 ? $days . ' Days' : ' Expired';
            } catch (Exception $e) {
                $daysUntilRenewal = 'Invalid Date';
            }
        }

        switch ($field) {
            case 'scheduled_date_of_inspection':
            case 'report_submitted':
            case 'billing_req_sent':
            case 'agreement_submitted':
                return $value ? Carbon::parse($value)->format('m/d/Y') : 'N/A';

            case 'assigned_consultant_id':
                $consultant = Consultant::find($value);
                return $consultant ? "{$consultant->first_name} {$consultant->last_name}" : 'N/A';

            case 'property_type':
                return ucwords($value);

            case 'contracting_status':
                switch ($value) {
                    case 'quoted':
                        return 'Quoted';
                    case 'started':
                        return 'Started';
                    case 'executed':
                        return 'Executed';
                    case 'closed':
                        return 'Closed';
                    default:
                        return 'Quoted';
                }

            case 'report_status':
                switch ($value) {
                    case 'not-started':
                        return 'Not Started';
                    case 'in-progress':
                        return 'Underway';
                    case 'in-review':
                        return 'In Review';
                    case 'completed':
                        return 'Completed';
                    default:
                        return 'Not Started';
                }

            case 'securitygauge_crime_risk':
                return ucfirst($value);

            case 'quoted_price':
            case 'sub_fees_estimated_expenses':
            case 'project_net_profit':
                return is_numeric($value) ? '$' . number_format($value, 2) : 'N/A';

            case 'days_until_renewal':
                return $daysUntilRenewal;

            default:
                return $value ?? 'N/A';
        }
    }

@endphp

<style>
    /* Existing table styling */
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
        border: 1px solid #cccccc;
    }

    .table thead th {
        background-color: #11467a;
        color: #f1f8ff;
        text-align: left;
        font-weight: bold;
        font-size: 12px;
    }

    .table thead th a {
        color: #f1f8ff;
        text-decoration: none;
        font-size: 12px;
    }

    .table tbody tr:nth-of-type(odd) {
        background-color: #fdfdfd;
    }

    .table tbody tr:nth-of-type(even) {
        background-color: #f8f9fa;
    }
</style>
{{--







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

    /* Risk level styling - applied to TD cells */
    td.risk-low {
        background-color: #72b862 !important;
        color: white !important;
    }

    td.risk-moderate {
        background-color: #95f181 !important;
        color: black !important;
    }

    td.risk-elevated {
        background-color: #fae099 !important;
        color: black !important;
    }

    td.risk-high {
        background-color: #f2a36e !important;
        color: black !important;
    }

    td.risk-severe {
        background-color: #c75845 !important;
        color: white !important;
    }

    /* Report status styling - applied to TD cells */
    td.status-not-started {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    td.status-in-progress {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    td.status-in-review {
        background-color: #cce5ff !important;
        color: #004085 !important;
    }

    td.status-completed {
        background-color: #d4edda !important;
        color: #155724 !important;
    } --}}
<div class="table-responsive m-0 p-0">
    <table class="table table-striped table-hover table-light table-bordered m-0" id="hb837-table">
        <thead>
            <tr>
                @foreach ($table_fields as $field)
                    <th class="text-capitalize w-auto">
                        <a href="{{ route(
                            'admin.hb837.tabs',
                            array_merge(request()->query(), [
                                'tab' => $active_tab,
                                'sort' => $field,
                                'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
                            ]),
                        ) }}"
                            class="d-flex justify-content-between align-items-center">
                            <span>{{ ucwords(str_replace('_', ' ', $field)) }}</span>
                            @if (request('sort') === $field)
                                @if (request('direction') === 'asc')
                                    <i class="fa fa-arrow-up"></i>
                                @else
                                    <i class="fa fa-arrow-down"></i>
                                @endif
                            @endif
                        </a>
                    </th>
                @endforeach

                <th style="width: 120px">Action</th>
            </tr>
        </thead>

        <tbody>
            @if ($hb837->isEmpty())
                <tr>
                    <td colspan="{{ count($table_fields) + 1 }}" class="text-center">
                        <div class="alert alert-danger d-flex justify-content-center align-items-center"
                            style="min-height: 200px">
                            <i class="fa fa-exclamation-triangle fa-2x mr-2"></i>
                            <span>No data found.</span>
                        </div>
                    </td>
                </tr>
            @else
                @foreach ($hb837 as $item)
                    <tr>
                        @foreach ($table_fields as $field)
                            @php
                                $cellClass = '';
                                if ($field === 'securitygauge_crime_risk') {
                                    $cellClass = getRiskClass($item->$field);
                                } elseif ($field === 'report_status') {
                                    $cellClass = getStatusClass($item->$field);
                                }
                            @endphp
                            <td class="{{ $cellClass }}">{!! formatField($field, $item->$field, $item) !!}</td>
                        @endforeach
                        <td>
                            <a href="{{ route('admin.hb837.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="https://maps.google.com/?q={{ urlencode($item->address) }}" target="_blank"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-map"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tbody = document.querySelector("#hb837-table tbody");
        if (tbody) {
            tbody.addEventListener("click", function(e) {
                // Find the closest row from the click target
                const row = e.target.closest("tr");
                if (!row) return;
                // Skip if the click came from an interactive element
                if (e.target.closest("a, button, form")) return;
                // Remove the 'selected' class from all rows in tbody
                tbody.querySelectorAll("tr").forEach(r => r.classList.remove("selected"));
                // Add the 'selected' class to the clicked row
                row.classList.add("selected");
            });
        }
    });
</script>

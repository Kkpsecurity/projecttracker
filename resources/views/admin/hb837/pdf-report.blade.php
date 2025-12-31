<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Property Information Sheet - {{ $hb837->property_name }}</title>
    <style>
        body {
            font-family: "Arial", "Helvetica", sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 15px;
            line-height: 1.3;
            color: #333;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 15px;
            background: linear-gradient(135deg, #f8f9ff 0%, #e9ecff 100%);
            padding: 20px;
            border-radius: 8px;
            margin: -15px -15px 25px -15px;
        }

        .header h1 {
            font-size: 22px;
            color: #6f42c1;
            margin: 0 0 8px 0;
            font-weight: bold;
        }

        .header .subtitle {
            font-size: 13px;
            color: #6c757d;
            font-weight: normal;
            margin: 0;
        }

        .property-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .property-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .property-table tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .property-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }

        .property-table td:first-child {
            font-weight: bold;
            color: #495057;
            width: 35%;
            background-color: #f1f3f4;
            border-right: 1px solid #dee2e6;
        }

        .property-table td:last-child {
            color: #212529;
            word-wrap: break-word;
        }

        .property-table tr:last-child td {
            border-bottom: none;
        }

        .consultant-notes-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .consultant-notes-section h4 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 13px;
            font-weight: bold;
        }

        .consultant-notes-section p {
            margin: 0;
            color: #856404;
            font-style: italic;
            line-height: 1.4;
        }

        .crime-stats-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-left: 4px solid #6f42c1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .crime-stats-section h4 {
            color: #6f42c1;
            margin: 0 0 10px 0;
            font-size: 13px;
            font-weight: bold;
        }

        .crime-stats-meta {
            font-size: 10px;
            color: #6c757d;
            margin: 0 0 10px 0;
        }

        .crime-stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            border: 1px solid #dee2e6;
        }

        .crime-stats-table th,
        .crime-stats-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }

        .crime-stats-table th {
            text-align: left;
            background: #f1f3f4;
            color: #495057;
            font-weight: bold;
        }

        .crime-stats-table tr:last-child td {
            border-bottom: none;
        }

        .findings-section {
            background: #fff;
            border: 1px solid #dee2e6;
            border-left: 4px solid #6f42c1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .findings-section h4 {
            color: #6f42c1;
            margin: 0 0 10px 0;
            font-size: 13px;
            font-weight: bold;
        }

        .findings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            border: 1px solid #dee2e6;
        }

        .findings-table th,
        .findings-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
            vertical-align: top;
        }

        .findings-table th {
            text-align: left;
            background: #f1f3f4;
            color: #495057;
            font-weight: bold;
        }

        .findings-table tr:last-child td {
            border-bottom: none;
        }

        .status-highlight {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-not-started {
            background-color: #6c757d;
            color: white;
        }

        .status-in-progress {
            background-color: #007bff;
            color: white;
        }

        .status-in-review {
            background-color: #ffc107;
            color: #212529;
        }

        .status-completed {
            background-color: #28a745;
            color: white;
        }

        .status-quoted {
            background-color: #17a2b8;
            color: white;
        }

        .status-executed {
            background-color: #28a745;
            color: white;
        }

        .status-closed {
            background-color: #6c757d;
            color: white;
        }

        .crime-risk-low {
            background-color: #d4edda;
            color: #155724;
        }

        .crime-risk-moderate {
            background-color: #fff3cd;
            color: #856404;
        }

        .crime-risk-elevated {
            background-color: #ffeaa7;
            color: #856404;
        }

        .crime-risk-high {
            background-color: #f8d7da;
            color: #721c24;
        }

        .crime-risk-severe {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .map-wrapper {
            margin-top: 25px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .map-wrapper h3 {
            color: #6f42c1;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
            border: 2px solid #bdc3c7;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: block;
            margin: 0 auto;
        }

        .map-fallback {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin: 10px 0;
        }

        .map-fallback p {
            margin: 0;
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        .map-fallback .address-info {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .map-fallback .fallback-note {
            margin-top: 10px;
            font-size: 12px;
            color: #999;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            background: #f8f9fa;
            margin-left: -15px;
            margin-right: -15px;
            margin-bottom: -15px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 15px;
        }

        .property-id {
            background: #6f42c1;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
            display: inline-block;
            margin-top: 5px;
            font-weight: bold;
        }

        .container {
            max-width: 100%;
            margin: 0;
        }

        /* Helper function to format values */
        .value-currency {
            color: #28a745;
            font-weight: bold;
        }

        .value-date {
            color: #007bff;
        }

        .value-email {
            color: #6f42c1;
            text-decoration: underline;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Property Information Sheet</h1>

            <p class="address-info">

                {{ $hb837->address }}
                @if ($hb837->city)
                    , {{ $hb837->city }}
                @endif
                @if ($hb837->state)
                    , {{ $hb837->state }}
                @endif
                @if ($hb837->zip)
                    {{ $hb837->zip }}
                @endif
            </p>
        </div>

        <!-- Property Data -->
        <table class="property-table">
            @php
                // Helper function to format status badges
                function getStatusBadge($status, $type = 'status')
                {
                    if (!$status) {
                        return 'N/A';
                    }

                    $status = strtolower(str_replace(['-', '_', ' '], '-', $status));
                    $class = $type . '-' . $status;
                    $display = ucfirst(str_replace('-', ' ', $status));

                    return '<span class="status-highlight ' . $class . '">' . $display . '</span>';
                }

                // Helper function to format crime risk
                function getCrimeRiskBadge($risk)
                {
                    if (!$risk) {
                        return 'N/A';
                    }

                    $risk = strtolower($risk);
                    $class = 'crime-risk-' . $risk;
                    $display = ucfirst($risk);

                    return '<span class="status-highlight ' . $class . '">' . $display . '</span>';
                }

                $propertyData = [
                    'Property Name' => $hb837->property_name ?? 'N/A',
                    'Type' => ucfirst($hb837->property_type ?? 'N/A'),
                    'No. Of Units' => $hb837->units ? number_format($hb837->units) : 'N/A',
                    'SecurityGauge Crime Risk' => getCrimeRiskBadge($hb837->crimeStats?->crime_risk ?? $hb837->securitygauge_crime_risk),
                    'Address' =>
                        trim(
                            implode(
                                ', ',
                                array_filter([$hb837->address, $hb837->city, $hb837->state . ' ' . $hb837->zip]),
                            ),
                        ) ?:
                        'N/A',
                    'Phone Number' => $hb837->phone ?? 'N/A',
                    'Owner' => $hb837->owner_name ?? 'N/A',
                    'Management Company' => $hb837->management_company ?? 'N/A',
                    'Property Manager' => $hb837->property_manager_name ?? 'N/A',
                    'Property Manager Email' => $hb837->property_manager_email
                        ? '<span class="value-email">' . $hb837->property_manager_email . '</span>'
                        : 'N/A',
                    'Regional Manager' => $hb837->regional_manager_name ?? 'N/A',
                    'Regional Mgr Email' => $hb837->regional_manager_email
                        ? '<span class="value-email">' . $hb837->regional_manager_email . '</span>'
                        : 'N/A',
                    'Assigned Consultant' => $hb837->consultant
                        ? $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name
                        : 'N/A',
                    'Scheduled Inspection' => $hb837->scheduled_date_of_inspection
                        ? '<span class="value-date">' .
                            $hb837->scheduled_date_of_inspection->format('F j, Y') .
                            '</span>'
                        : 'N/A',
                    'Macro Client' => $hb837->macro_client ?? 'N/A',
                ];
            @endphp

            @foreach ($propertyData as $label => $value)
                <tr>
                    <td>{{ $label }}:</td>
                    <td>{!! $value !!}</td>
                </tr>
            @endforeach
        </table>

        <!-- Consultant Notes Section (if present) -->
        @if ($hb837->consultant_notes)
            <div class="consultant-notes-section">
                <h4>Consultant Notes</h4>
                <p>{{ $hb837->consultant_notes }}</p>
            </div>
        @endif

        @if ($hb837->findings && $hb837->findings->count())
            <div class="findings-section">
                <h4>Findings / Recommendations</h4>

                <table class="findings-table">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Category</th>
                            <th style="width: 12%;">Severity</th>
                            <th style="width: 20%;">Location</th>
                            <th style="width: 25%;">Finding</th>
                            <th style="width: 25%;">Recommendation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hb837->findings->sortBy('created_at') as $finding)
                            <tr>
                                <td>
                                    {{ $finding->category ?: 'N/A' }}
                                    @if ($finding->status)
                                        <br><span style="color:#6c757d;">Status: {{ $finding->status }}</span>
                                    @endif
                                    @if ($finding->plot_id)
                                        <br><span style="color:#6c757d;">Plot: #{{ $finding->plot_id }}</span>
                                    @endif
                                </td>
                                <td>{{ $finding->severity ?: 'N/A' }}</td>
                                <td>{{ $finding->location_context ?: 'N/A' }}</td>
                                <td>{{ $finding->description ?: 'N/A' }}</td>
                                <td>{{ $finding->recommendation ?: 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($hb837->crimeStats)
            @php
                $crimeStats = $hb837->crimeStats;
                $offenses = data_get($crimeStats->stats, 'tables.offenses', []);
                $periodStart = $crimeStats->period_start?->format('M j, Y') ?? data_get($crimeStats->stats, 'report.period.start');
                $periodEnd = $crimeStats->period_end?->format('M j, Y') ?? data_get($crimeStats->stats, 'report.period.end');
                $reviewedLabel = $crimeStats->is_reviewed ? 'Reviewed' : 'Pending Review';
                $reviewedAt = $crimeStats->reviewed_at?->format('M j, Y g:i A');
                $reviewedBy = $crimeStats->reviewer?->name;
            @endphp

            <div class="crime-stats-section">
                <h4>Crime Statistics</h4>

                <p class="crime-stats-meta">
                    Status: <strong>{{ $reviewedLabel }}</strong>
                    @if ($reviewedAt)
                        &nbsp;|&nbsp; {{ $reviewedAt }}
                    @endif
                    @if ($reviewedBy)
                        &nbsp;|&nbsp; {{ $reviewedBy }}
                    @endif
                </p>

                <p style="margin: 0; font-size: 11px;">
                    Crime Risk: {!! getCrimeRiskBadge($crimeStats->crime_risk ?? data_get($crimeStats->stats, 'summary.crime_risk')) !!}
                    @if ($periodStart || $periodEnd)
                        <br>
                        Report Period:
                        {{ $periodStart ?: 'N/A' }}
                        @if ($periodEnd)
                            &ndash; {{ $periodEnd }}
                        @endif
                    @endif
                </p>

                @if (is_array($offenses) && count($offenses))
                    <table class="crime-stats-table">
                        <thead>
                            <tr>
                                <th style="width: 55%;">Offense</th>
                                <th style="width: 15%;">Count</th>
                                <th style="width: 30%;">Rate / 1,000</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($offenses as $row)
                                <tr>
                                    <td>{{ data_get($row, 'label', 'N/A') }}</td>
                                    <td>{{ data_get($row, 'count') !== null ? number_format((int) data_get($row, 'count')) : 'N/A' }}</td>
                                    <td>{{ data_get($row, 'rate_per_1000') !== null ? number_format((float) data_get($row, 'rate_per_1000'), 2) : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif

        <div class="page-break"></div>
        
        <!-- Google Map Section -->
        <div class="map-wrapper">
            <h3>Property Location</h3>
            
            @if($show_map && $map_url)
                <!-- Display Google Maps Static Image -->
                <img src="{{ $map_url }}" 
                     alt="Map showing location of {{ $hb837->address }}" 
                     class="img-fluid"
                     style="max-width: 100%; height: auto;">
                <p style="font-size: 10px; color: #666; margin-top: 10px; text-align: center;">
                    <em>Map view of property location</em>
                </p>
            @else
                <!-- Fallback when map is not available -->
                <div class="map-fallback">
                    <p class="address-info">
                        Property Address:<br>
                        @if($hb837->address)
                            {{ $hb837->address }}
                            @if($hb837->city), {{ $hb837->city }}@endif
                            @if($hb837->state), {{ $hb837->state }}@endif
                            @if($hb837->zip) {{ $hb837->zip }}@endif
                        @else
                            <em>No address specified</em>
                        @endif
                    </p>
                    <p class="fallback-note">
                        @if($map_fallback_reason)
                            Reason: {{ $map_fallback_reason }}.<br>
                        @endif
                        For detailed location mapping, please use your preferred mapping service with the address above.
                    </p>
                </div>
            @endif
        </div>

    </div>
</body>

</html>

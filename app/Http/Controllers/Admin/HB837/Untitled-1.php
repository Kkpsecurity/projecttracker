<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Information Sheet</title>
    <style>
        body {
            font-family: "Arial", "Helvetica", sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }

        .list-group-item strong {
            width: 200px;
            display: inline-block;
        }

        .list-group-item {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .list-group-item-primary {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .map-wrapper {
            margin-top: 20px;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .consultant-notes {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 5px 0;
            font-style: italic;
            border-radius: 4px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="list-group">
            <div class="list-group-item list-group-item-primary text-center">
                <h2>Property Information Sheet</h2>
            </div>

            @php
                $propertyData = [
                    'Property Name' => $hb837->property_name ?? 'N/A',
                    'Type' => ucfirst($hb837->property_type ?? 'N/A'),
                    'No. Of Units' => $hb837->units ? number_format($hb837->units) : 'N/A',
                    'SecurityGauge Crime Risk' => $hb837->securitygauge_crime_risk ?? 'N/A',
                    'Address' => trim(implode(', ', array_filter([
                        $hb837->address,
                        $hb837->city,
                        $hb837->state . ' ' . $hb837->zip
                    ]))) ?: 'N/A',
                    'Phone Number' => $hb837->phone ?? 'N/A',
                    'Owner' => $hb837->owner_name ?? 'N/A',
                    'Management Company' => $hb837->management_company ?? 'N/A',
                    'Property Manager' => $hb837->property_manager_name ?? 'N/A',
                    'Property Manager Email' => $hb837->property_manager_email ?? 'N/A',
                    'Regional Manager' => $hb837->regional_manager_name ?? 'N/A',
                    'Regional Mgr Email' => $hb837->regional_manager_email ?? 'N/A',
                    'Report Status' => $hb837->report_status ? ucfirst(str_replace('-', ' ', $hb837->report_status)) : 'N/A',
                    'Contracting Status' => $hb837->contracting_status ? ucfirst($hb837->contracting_status) : 'N/A',
                    'Quoted Price' => $hb837->quoted_price ? '$' . number_format((float)$hb837->quoted_price, 2) : 'N/A',
                    'Assigned Consultant' => $hb837->consultant ? ($hb837->consultant->first_name . ' ' . $hb837->consultant->last_name) : 'N/A',
                    'Scheduled Inspection' => $hb837->scheduled_date_of_inspection ? $hb837->scheduled_date_of_inspection->format('F j, Y') : 'N/A',
                    'Macro Client' => $hb837->macro_client ?? 'N/A',
                    'Macro Contact' => $hb837->macro_contact ?? 'N/A',
                    'Macro Email' => $hb837->macro_email ?? 'N/A',
                    'Consultant Notes' => $hb837->consultant_notes ?? null
                ];
            @endphp

            @foreach ($propertyData as $label => $value)
                @if ($label === 'Consultant Notes' && $value)
                    <div class="list-group-item consultant-notes">
                        <strong>Consultant Notes:</strong>
                        <p>{{ $value }}</p>
                    </div>
                @elseif ($label !== 'Consultant Notes')
                    <div class="list-group-item">
                        <strong>{{ $label }}:</strong> {{ $value }}
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Google Map Section -->
        @if($hb837->address)
        <div class="map-wrapper">
            <h3>Property Location</h3>
            <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ urlencode($hb837->address) }}&zoom=13&size=600x300&maptype=roadmap&markers=color:red%7Clabel:A%7C{{ urlencode($hb837->address) }}&key={{ env('GOOGLE_MAPS_API_KEY') }}"
                alt="Google Map of {{ $hb837->address }}" class="img-fluid">
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>Generated on {{ $generated_at ?? now()->format('F j, Y \a\t g:i A') }} by {{ $generated_by ?? 'System' }}</div>
            <div>HB837 Project Tracking System - Property ID: {{ $hb837->id }}</div>
        </div>
    </div>
</body>
</html>

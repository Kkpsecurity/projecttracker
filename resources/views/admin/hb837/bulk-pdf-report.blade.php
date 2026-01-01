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

        .map-fallback {
            border: 2px dashed #ccc !important;
            padding: 20px !important;
            text-align: center !important;
            background-color: #f9f9f9 !important;
            border-radius: 8px;
            margin: 10px 0;
        }

        .map-fallback p {
            margin: 0 !important;
            font-size: 14px !important;
            color: #666 !important;
            line-height: 1.4;
        }

        .map-fallback .address-info {
            font-weight: bold;
            margin-bottom: 10px !important;
        }

        .map-fallback .fallback-note {
            margin-top: 10px !important;
            font-size: 12px !important;
            color: #999 !important;
            font-style: italic !important;
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
        @if(isset($hb837))
            {{-- Individual Record PDF --}}
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
                @if(env('GOOGLE_MAPS_API_KEY'))
                    <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ urlencode($hb837->address) }}&zoom=13&size=600x300&maptype=roadmap&markers=color:red%7Clabel:A%7C{{ urlencode($hb837->address) }}&key={{ env('GOOGLE_MAPS_API_KEY') }}"
                        alt="Map showing location of {{ $hb837->address }}" 
                        class="img-fluid"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="map-fallback" style="display:none;">
                        <p class="address-info">
                            Property Address:<br>
                            {{ $hb837->address }}
                            @if($hb837->city), {{ $hb837->city }}@endif
                            @if($hb837->state), {{ $hb837->state }}@endif
                            @if($hb837->zip) {{ $hb837->zip }}@endif
                        </p>
                        <p class="fallback-note">
                            Map unavailable in PDF format. Please refer to the address above for location details.
                        </p>
                    </div>
                @else
                    <div class="map-fallback">
                        <p class="address-info">
                            Property Address:<br>
                            {{ $hb837->address }}
                            @if($hb837->city), {{ $hb837->city }}@endif
                            @if($hb837->state), {{ $hb837->state }}@endif
                            @if($hb837->zip) {{ $hb837->zip }}@endif
                        </p>
                        <p class="fallback-note">
                            For detailed location mapping, please refer to the address above or use your preferred mapping service.
                        </p>
                    </div>
                @endif
            </div>
            @else
            <div class="map-wrapper">
                <h3>Property Location</h3>
                <div class="map-fallback">
                    <p class="address-info">
                        Property Address: Not specified
                    </p>
                    <p class="fallback-note">
                        No address information available for this property.
                    </p>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <div>Generated on {{ $generated_at ?? now()->format('F j, Y \a\t g:i A') }} by {{ $generated_by ?? 'System' }}</div>
                <div>HB837 Project Tracking System - Property ID: {{ $hb837->id }}</div>
            </div>

        @elseif(isset($records))
            {{-- Bulk Records PDF --}}
            <div class="list-group">
                <div class="list-group-item list-group-item-primary text-center">
                    <h2>HB837 Bulk Report - {{ $tab_title ?? 'Properties' }}</h2>
                </div>
                <div class="list-group-item">
                    <strong>Total Properties:</strong> {{ $total_count ?? count($records) }}
                </div>
                <div class="list-group-item">
                    <strong>Generated:</strong> {{ $generated_at ?? now()->format('F j, Y \a\t g:i A') }}
                </div>
                @if($search)
                <div class="list-group-item">
                    <strong>Search Filter:</strong> {{ $search }}
                </div>
                @endif
            </div>

            @if($records->count() > 0)
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="border: 1px solid #ddd; padding: 8px;">Property Name</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Address</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Type</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Consultant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $record->property_name }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $record->address }}, {{ $record->city }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($record->property_type ?? '') }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst(str_replace('-', ' ', $record->report_status ?? '')) }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $record->consultant ? ($record->consultant->first_name . ' ' . $record->consultant->last_name) : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="list-group-item text-center">
                    <strong>No records found for the selected criteria.</strong>
                </div>
            @endif
        @endif
    </div>
</body>
</html>
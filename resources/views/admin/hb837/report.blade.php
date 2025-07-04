<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Property Information Sheet</title>
    <!-- Use Bootstrap and FontAwesome for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <style>
        body {
            font-family: "Nunito", sans-serif;
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
    </style>
</head>

@php
    use Illuminate\Support\Str;
@endphp

<body>
    <div class="container mt-4">
        <div class="list-group">
            <div class="list-group-item list-group-item-primary text-center">
                <h2>Property Information Sheet</h2>
            </div>

            @php
                $propertyData = [
                    'Property Name' => $hb837->property_name ?? 'N/A',
                    'Type' => $hb837->property_type ?? 'N/A',
                    'No. Of Units' => $hb837->units ?? 'N/A',
                    'SecurityGauge Crime Risk' => $hb837->securitygauge_crime_risk ?? 'N/A',
                    'Address' => trim(($hb837->address ?? '') . ', ' . ($hb837->city ?? '') . ', ' . ($hb837->state ?? '') . ' ' . ($hb837->zip ?? '')),
                    'Phone Number' => $hb837->phone ?? 'N/A',
                    'Owner' => $hb837->owner_name ?? 'N/A',
                    'Management Company' => $hb837->management_company ?? 'N/A',
                    'Property Manager' => $hb837->property_manager_name ?? 'N/A',
                    'Property Manager Email' => $hb837->property_manager_email ?? 'N/A',
                    'Regional Manager' => $hb837->regional_manager ?? 'N/A',
                    'Regional Mgr Email' => $hb837->regional_manager_email ?? 'N/A',
                    'Consultant Notes' => $hb837->consultant_notes ?? null // Can be null
                ];
            @endphp

            @foreach ($propertyData as $label => $value)
                @if ($label === 'Consultant Notes')
                    <div class="list-group-item">
                        <strong><i class="fa fa-sticky-note"></i> Consultant Notes:</strong>
                        <p>{{ e($value) }}</p>
                    </div>
                @elseif ($label !== 'Consultant Notes')
                    <div class="list-group-item">
                        <strong>{{ $label }}:</strong> {{ e($value) }}
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Google Map Section -->
        <div class="map-wrapper mt-4">
            <h3>Property Location</h3>
            <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ urlencode($hb837->address ?? '') }}&zoom=13&size=600x300&maptype=roadmap&markers=color:red%7Clabel:A%7C{{ urlencode($hb837->address ?? '') }}&key={{ env('GOOGLE_MAPS_API_KEY') }}"
                alt="Google Map" class="img-fluid">
        </div>
    </div>
</body>

</html>

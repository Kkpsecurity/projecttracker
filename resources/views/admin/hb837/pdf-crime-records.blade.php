<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>HB837 Crime Records</title>
    <style>
        @page { margin: 26px 26px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .meta { font-size: 11px; margin-bottom: 14px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 14px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px 7px; vertical-align: top; }
        th { background: #f2f2f2; font-weight: bold; }
        .muted { color: #555; }
        .right { text-align: right; }
        .wrap { word-break: break-word; }
    </style>
</head>
<body>
@php
    /** @var \App\Models\HB837 $hb837 */
    $crimeStat = $hb837->crimeStats;
    $stats = is_array($crimeStat?->stats ?? null) ? $crimeStat->stats : [];

    $offenses = data_get($stats, 'tables.offenses', []);
    if (!is_array($offenses)) {
        $offenses = [];
    }

    $incidents = $hb837->recentIncidents
        ? $hb837->recentIncidents->sortBy('sort_order')->sortBy('id')
        : collect();
@endphp

    <div class="title">HB837 Crime Records</div>
    <div class="meta">
        <div><strong>Property:</strong> {{ $hb837->property_name ?? 'N/A' }} <span class="muted">(ID {{ $hb837->id }})</span></div>
        <div><strong>Generated:</strong> {{ $generated_at ?? '' }} <span class="muted">by {{ $generated_by ?? 'System' }}</span></div>
        @if(!empty($hb837->address) || !empty($hb837->city) || !empty($hb837->state) || !empty($hb837->zip))
            <div><strong>Address:</strong> {{ trim(implode(', ', array_filter([$hb837->address, $hb837->city, $hb837->state, $hb837->zip]))) }}</div>
        @endif
    </div>

    <div class="section-title">Recent Incidents</div>
    <table>
        <thead>
            <tr>
                <th style="width: 52px;">Sort</th>
                <th style="width: 140px;">Date</th>
                <th>Summary</th>
            </tr>
        </thead>
        <tbody>
        @forelse($incidents as $i)
            <tr>
                <td class="right muted">{{ $i->sort_order ?? 0 }}</td>
                <td>{{ $i->incident_date ?: 'N/A' }}</td>
                <td class="wrap">{{ $i->summary }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="muted">No incidents recorded.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="section-title">Crime Stats (SecurityGauge)</div>
    <table>
        <tbody>
            <tr>
                <th style="width: 180px;">Report Title</th>
                <td class="wrap">{{ $crimeStat?->report_title ?? data_get($stats, 'report.title') ?? '—' }}</td>
            </tr>
            <tr>
                <th>Period</th>
                <td>
                    {{ optional($crimeStat?->period_start)->toDateString() ?? data_get($stats, 'report.period.start') ?? '—' }}
                    to
                    {{ optional($crimeStat?->period_end)->toDateString() ?? data_get($stats, 'report.period.end') ?? '—' }}
                </td>
            </tr>
            <tr>
                <th>Crime Risk</th>
                <td>{{ $crimeStat?->crime_risk ?? data_get($stats, 'summary.crime_risk') ?? '—' }}</td>
            </tr>
            <tr>
                <th>Review</th>
                <td>
                    @if($crimeStat)
                        {{ $crimeStat->is_reviewed ? 'Reviewed' : 'Pending review' }}
                        @if($crimeStat->reviewed_at)
                            <span class="muted">(at {{ $crimeStat->reviewed_at->format('Y-m-d H:i') }})</span>
                        @endif
                    @else
                        <span class="muted">No extracted stats yet.</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Offense Rates</div>
    <table>
        <thead>
            <tr>
                <th style="width: 170px;">Label</th>
                <th style="width: 110px;">Category</th>
                <th style="width: 60px;">Count</th>
                <th style="width: 80px;">Prop/1000</th>
                <th style="width: 80px;">State/1000</th>
                <th style="width: 80px;">Nat/1000</th>
                <th style="width: 80px;">Compare</th>
                <th style="width: 52px;">SG</th>
            </tr>
        </thead>
        <tbody>
        @forelse($offenses as $row)
            <tr>
                <td class="wrap">{{ data_get($row, 'label') }}</td>
                <td class="wrap muted">{{ data_get($row, 'category') }}</td>
                <td class="right">{{ data_get($row, 'count') ?? '' }}</td>
                <td class="right">{{ data_get($row, 'rate_per_1000') ?? '' }}</td>
                <td class="right">{{ data_get($row, 'state_rate_per_1000') ?? '' }}</td>
                <td class="right">{{ data_get($row, 'national_rate_per_1000') ?? '' }}</td>
                <td class="right">{{ data_get($row, 'comparison_to_state') ?? '' }}</td>
                <td class="right">{{ data_get($row, 'securitygauge_score') ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="muted">No offense rows available.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>

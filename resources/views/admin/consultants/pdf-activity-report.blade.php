<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consultant Activity Report</title>
    <style>
        @page { margin: 28px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .meta { font-size: 11px; margin-bottom: 14px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 14px 0 6px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #444; padding: 6px 7px; vertical-align: top; }
        th { background: #1f1f1f; color: #fff; font-weight: bold; text-align: center; font-size: 11px; line-height: 1.15; }
        .muted { color: #555; }
        .right { text-align: right; }
        .wrap { overflow-wrap: break-word; word-break: normal; }
        .page-break { page-break-before: always; }
        .kv td { border: none; padding: 2px 0; }
        .kv td:first-child { width: 170px; color: #333; }
    </style>
</head>
<body>
@php
    $totalProjects = $activeAssignments->count() + $completedAssignments->count();

    $sumCounts = function ($items, $field) {
        if (!$items) return 0;
        return (int) collect($items)->sum(function ($x) use ($field) {
            return (int) data_get($x, $field, 0);
        });
    };

    $activeFiles = $sumCounts($activeAssignments, 'files_count');
    $activeFindings = $sumCounts($activeAssignments, 'findings_count');
    $activeMeasures = $sumCounts($activeAssignments, 'risk_measures_count');
    $activeIncidents = $sumCounts($activeAssignments, 'recent_incidents_count');

    $sumMoney = function ($items, $field) {
        if (!$items) return 0.0;
        return (float) collect($items)->sum(function ($x) use ($field) {
            $value = data_get($x, $field);
            return $value === null ? 0.0 : (float) $value;
        });
    };

    $activeGross = $sumMoney($activeAssignments, 'quoted_price');
    $activeExpenses = $sumMoney($activeAssignments, 'sub_fees_estimated_expenses');
    $activeNet = $activeGross - $activeExpenses;

    $completedGross = $sumMoney($completedAssignments, 'quoted_price');
    $completedExpenses = $sumMoney($completedAssignments, 'sub_fees_estimated_expenses');
    $completedNet = $completedGross - $completedExpenses;

    $allGross = $activeGross + $completedGross;
    $allExpenses = $activeExpenses + $completedExpenses;
    $allNet = $allGross - $allExpenses;

    $completionDaysValues = collect($completedAssignments)
        ->filter(fn ($hb) => $hb->scheduled_date_of_inspection && $hb->report_submitted)
        ->map(fn ($hb) => (int) $hb->scheduled_date_of_inspection->diffInDays($hb->report_submitted));

    $completedAvgCompletionDays = $completionDaysValues->count() ? (float) $completionDaysValues->avg() : null;
@endphp

    <div class="title">Consultant Activity Report</div>
    <div class="meta">
        <div><strong>Consultant:</strong> {{ $consultant->full_name }} @if($consultant->dba_company_name)<span class="muted">({{ $consultant->dba_company_name }})</span>@endif</div>
        <div><strong>Email:</strong> {{ $consultant->email ?: 'N/A' }}</div>
        <div><strong>Generated:</strong> {{ $generatedAt->format('Y-m-d H:i:s') }} <span class="muted">by {{ $generatedBy ?? 'System' }}</span></div>
    </div>

    <div class="section-title">Consultant Profile</div>
    <table class="kv">
        <tr>
            <td><strong>Mailing Address</strong></td>
            <td class="wrap">{{ $consultant->mailing_address ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>FCP Expiration</strong></td>
            <td>{{ optional($consultant->fcp_expiration_date)->format('Y-m-d') ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Assigned Light Meter</strong></td>
            <td>{{ $consultant->assigned_light_meter ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>LM NIST Expiration</strong></td>
            <td>{{ optional($consultant->lm_nist_expiration_date)->format('Y-m-d') ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Bonus Rate</strong></td>
            <td>{{ $consultant->subcontractor_bonus_rate !== null ? ('$' . number_format((float) $consultant->subcontractor_bonus_rate, 2)) : 'N/A' }}</td>
        </tr>
    </table>

    @if($consultant->notes)
        <div class="section-title">Consultant Notes</div>
        <div class="wrap">{{ $consultant->notes }}</div>
    @endif

    <div class="section-title">Work Summary</div>
    <table>
        <thead>
            <tr>
                <th>Total Projects</th>
                <th>Active</th>
                <th>Completed</th>
                <th>Active Files</th>
                <th>Active Findings</th>
                <th>Active Measures</th>
                <th>Active Incidents</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="right">{{ $totalProjects }}</td>
                <td class="right">{{ $activeAssignments->count() }}</td>
                <td class="right">{{ $completedAssignments->count() }}</td>
                <td class="right">{{ $activeFiles }}</td>
                <td class="right">{{ $activeFindings }}</td>
                <td class="right">{{ $activeMeasures }}</td>
                <td class="right">{{ $activeIncidents }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Active HB837 Projects</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 35%;">Property</th>
                <th style="width: 11%;">Status</th>
                <th style="width: 10%;">Contract</th>
                <th style="width: 10%;">Inspection</th>
                <th style="width: 6%;">Prog<br>%</th>
                <th style="width: 7%;">Find<br>ings</th>
                <th style="width: 8%;">Meas<br>ures</th>
                <th style="width: 7%;">Inci<br>dents</th>
            </tr>
        </thead>
        <tbody>
        @forelse($activeAssignments as $hb)
            <tr>
                <td class="right">{{ $hb->id }}</td>
                <td class="wrap">
                    <strong>{{ $hb->property_name ?: 'N/A' }}</strong>
                    @if($hb->macro_client)
                        <div class="muted">Macro: {{ $hb->macro_client }}</div>
                    @endif
                    @if($hb->full_address)
                        <div class="muted">{{ $hb->full_address }}</div>
                    @endif
                </td>
                <td>{{ $hb->report_status ?: 'N/A' }} @if($hb->is_overdue)<span class="muted">(overdue)</span>@endif</td>
                <td>{{ $hb->contracting_status ?: 'N/A' }}</td>
                <td>{{ $hb->scheduled_date_of_inspection ? $hb->scheduled_date_of_inspection->format('Y-m-d') : '—' }}</td>
                <td class="right">{{ number_format((float) ($hb->progress_percentage ?? 0), 0) }}</td>
                <td class="right">{{ (int) ($hb->findings_count ?? 0) }}</td>
                <td class="right">{{ (int) ($hb->risk_measures_count ?? 0) }}</td>
                <td class="right">{{ (int) ($hb->recent_incidents_count ?? 0) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="muted">No active projects.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="section-title">Completed HB837 Projects</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 61%;">Property</th>
                <th style="width: 14%;">Completed</th>
                <th style="width: 7%;">Find<br>ings</th>
                <th style="width: 7%;">Meas<br>ures</th>
                <th style="width: 5%;">Inci<br>dents</th>
            </tr>
        </thead>
        <tbody>
        @forelse($completedAssignments as $hb)
            <tr>
                <td class="right">{{ $hb->id }}</td>
                <td class="wrap">
                    <strong>{{ $hb->property_name ?: 'N/A' }}</strong>
                    @if($hb->macro_client)
                        <div class="muted">Macro: {{ $hb->macro_client }}</div>
                    @endif
                    @if($hb->full_address)
                        <div class="muted">{{ $hb->full_address }}</div>
                    @endif
                </td>
                <td>{{ $hb->updated_at ? $hb->updated_at->format('Y-m-d') : '—' }}</td>
                <td class="right">{{ (int) ($hb->findings_count ?? 0) }}</td>
                <td class="right">{{ (int) ($hb->risk_measures_count ?? 0) }}</td>
                <td class="right">{{ (int) ($hb->recent_incidents_count ?? 0) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="muted">No completed projects.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if($activeAssignments->count() > 0)
        <div class="page-break"></div>
        <div class="title">Active Project Details</div>
        <div class="meta muted">One section per active HB837 project.</div>

        @foreach($activeAssignments as $hb)
            <div class="section-title">HB837 #{{ $hb->id }} — {{ $hb->property_name ?: 'N/A' }}</div>
            <table class="kv">
                <tr>
                    <td><strong>Address</strong></td>
                    <td class="wrap">{{ $hb->full_address ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Management Company</strong></td>
                    <td class="wrap">{{ $hb->management_company ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Macro Client</strong></td>
                    <td class="wrap">{{ $hb->macro_client ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>{{ $hb->report_status ?: 'N/A' }} / {{ $hb->contracting_status ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Scheduled Inspection</strong></td>
                    <td>{{ $hb->scheduled_date_of_inspection ? $hb->scheduled_date_of_inspection->format('Y-m-d') : '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Financials</strong></td>
                    <td>
                        Quoted: {{ $hb->quoted_price !== null ? ('$' . number_format((float) $hb->quoted_price, 2)) : '—' }}
                        | Est Expenses: {{ $hb->sub_fees_estimated_expenses !== null ? ('$' . number_format((float) $hb->sub_fees_estimated_expenses, 2)) : '—' }}
                        | Net Profit: {{ $hb->project_net_profit !== null ? ('$' . number_format((float) $hb->project_net_profit, 2)) : '—' }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Artifacts</strong></td>
                    <td>
                        Files: {{ (int) ($hb->files_count ?? 0) }}
                        | Plots: {{ (int) ($hb->plots_count ?? 0) }}
                        | Findings: {{ (int) ($hb->findings_count ?? 0) }}
                        | Measures: {{ (int) ($hb->risk_measures_count ?? 0) }}
                        | Incidents: {{ (int) ($hb->recent_incidents_count ?? 0) }}
                    </td>
                </tr>
            </table>

            @if($hb->consultant_notes)
                <div class="section-title">Consultant Notes</div>
                <div class="wrap">{{ $hb->consultant_notes }}</div>
            @endif

            @if($hb->financial_notes)
                <div class="section-title">Financial Notes</div>
                <div class="wrap">{{ $hb->financial_notes }}</div>
            @endif

            @if($hb->notes)
                <div class="section-title">Project Notes</div>
                <div class="wrap">{{ $hb->notes }}</div>
            @endif

            <div class="section-title">Crime Stats</div>
            @php
                $crimeStat = $hb->crimeStats;
                $stats = is_array($crimeStat?->stats ?? null) ? $crimeStat->stats : [];
            @endphp
            <table class="kv">
                <tr>
                    <td><strong>SecurityGauge Crime Risk</strong></td>
                    <td>{{ $crimeStat?->crime_risk ?? data_get($stats, 'summary.crime_risk') ?? ($hb->securitygauge_crime_risk ?: '—') }}</td>
                </tr>
                <tr>
                    <td><strong>Report Title</strong></td>
                    <td class="wrap">{{ $crimeStat?->report_title ?? data_get($stats, 'report.title') ?? '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Period</strong></td>
                    <td>
                        {{ optional($crimeStat?->period_start)->toDateString() ?? data_get($stats, 'report.period.start') ?? '—' }}
                        to
                        {{ optional($crimeStat?->period_end)->toDateString() ?? data_get($stats, 'report.period.end') ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Reviewed</strong></td>
                    <td>
                        @if($crimeStat)
                            {{ $crimeStat->is_reviewed ? 'Yes' : 'No' }}
                            @if($crimeStat->reviewed_at)
                                <span class="muted">(at {{ $crimeStat->reviewed_at->format('Y-m-d H:i') }})</span>
                            @endif
                        @else
                            <span class="muted">No extracted stats yet.</span>
                        @endif
                    </td>
                </tr>
            </table>

            @if(($hb->recentIncidents?->count() ?? 0) > 0)
                <div class="section-title">Recent Incidents</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">Sort</th>
                            <th style="width: 140px;">Date</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hb->recentIncidents as $i)
                            <tr>
                                <td class="right muted">{{ $i->sort_order ?? 0 }}</td>
                                <td>{{ $i->incident_date ?: 'N/A' }}</td>
                                <td class="wrap">{{ $i->summary }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif

    <div class="page-break"></div>
    <div class="title">Financial Summary</div>
    <div class="meta muted">Totals across all projects assigned to this consultant.</div>

    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Scope</th>
                <th style="width: 14%;">Projects</th>
                <th style="width: 21%;">Gross<br>Revenue</th>
                <th style="width: 21%;">Est<br>Expenses</th>
                <th style="width: 22%;">Net<br>Revenue</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Active</strong></td>
                <td class="right">{{ number_format($activeAssignments->count()) }}</td>
                <td class="right">${{ number_format($activeGross, 2) }}</td>
                <td class="right">${{ number_format($activeExpenses, 2) }}</td>
                <td class="right">${{ number_format($activeNet, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Completed</strong></td>
                <td class="right">{{ number_format($completedAssignments->count()) }}</td>
                <td class="right">${{ number_format($completedGross, 2) }}</td>
                <td class="right">${{ number_format($completedExpenses, 2) }}</td>
                <td class="right">${{ number_format($completedNet, 2) }}</td>
            </tr>
            <tr>
                <td><strong>All Projects</strong></td>
                <td class="right">{{ number_format($totalProjects) }}</td>
                <td class="right">${{ number_format($allGross, 2) }}</td>
                <td class="right">${{ number_format($allExpenses, 2) }}</td>
                <td class="right">${{ number_format($allNet, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Average Report Completion Time</div>
    <table class="kv">
        <tr>
            <td><strong>Completed Projects Avg (Days)</strong></td>
            <td>
                {{ $completedAvgCompletionDays === null ? '—' : number_format($completedAvgCompletionDays, 1) }}
                <span class="muted">(Inspection Date → Report Delivered)</span>
            </td>
        </tr>
        <tr>
            <td><strong>Count Used</strong></td>
            <td>{{ number_format($completionDaysValues->count()) }} <span class="muted">completed projects with both dates</span></td>
        </tr>
    </table>
</body>
</html>

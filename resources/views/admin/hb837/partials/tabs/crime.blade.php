@php
    /** @var \App\Models\HB837 $hb837 */
    $crimeStat = $hb837->crimeStats;
    $stats = is_array($crimeStat?->stats ?? null) ? $crimeStat->stats : [];

    $parsedCrimeRisk = data_get($stats, 'summary.crime_risk');
    $parsedGeneratedAt = data_get($stats, 'report.generated_at');
    $parsedPeriodStart = data_get($stats, 'report.period.start');
    $parsedPeriodEnd = data_get($stats, 'report.period.end');

    $warnings = data_get($stats, 'raw.warnings', []);
    if (!is_array($warnings)) {
        $warnings = [];
    }

    $offenses = data_get($stats, 'tables.offenses', []);
    if (!is_array($offenses)) {
        $offenses = [];
    }

    // Allow manual entry even when no PDF has been uploaded/extracted.
    if (empty($offenses)) {
        $defaultCategories = (array) config('hb837.crime_stats_offense_categories', []);
        $offenses = collect($defaultCategories)
            ->map(function ($category) {
                $label = ucwords(str_replace(['-', '_'], ' ', (string) $category));
                return [
                    'label' => $label,
                    'category' => $category,
                    'count' => null,
                    'rate_per_1000' => null,
                    'state_rate_per_1000' => null,
                    'national_rate_per_1000' => null,
                    'comparison_to_state' => null,
                    'securitygauge_score' => null,
                ];
            })
            ->values()
            ->all();
    }

    $trends = data_get($stats, 'tables.crime_risk_trends', []);
    if (!is_array($trends)) {
        $trends = [];
    }

    $crimeRiskOptions = collect(config('hb837.security_gauge', []))->values()->all();
    $crimeRiskValue = old('crime_risk', $crimeStat?->crime_risk ?? data_get($stats, 'summary.crime_risk'));
@endphp

<div class="mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Crime Stats (SecurityGauge)</h4>
            <div class="text-muted">
                @if($crimeStat)
                    <span>Extracted</span>
                    <span class="mx-1">•</span>
                    <span>{{ $crimeStat->is_reviewed ? 'Reviewed' : 'Pending review' }}</span>
                @else
                    <span>No extracted crime stats yet.</span>
                @endif
            </div>
        </div>
        <div class="mt-2 mt-sm-0">
            <button type="button" class="btn btn-primary" id="save-crime-stats" data-update-url="{{ route('admin.hb837.crime-stats.update', $hb837->id) }}">
                <i class="fas fa-save"></i> Save Crime Stats
            </button>
        </div>
    </div>
</div>

<div id="crime-stats-alert" class="alert d-none" role="alert"></div>

@if(!empty($warnings))
    <div class="alert alert-warning">
        <strong>Extraction warnings</strong>
        <ul class="mb-0">
            @foreach($warnings as $w)
                <li>{{ $w }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($crimeStat)
    <div class="card mb-3">
        <div class="card-header">
            <strong>Parsed From PDF (read-only)</strong>
        </div>
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted d-block">Crime Risk</small>
                    <div>{{ $parsedCrimeRisk ?: '—' }}</div>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <small class="text-muted d-block">Report Date</small>
                    <div>{{ $parsedGeneratedAt ?: '—' }}</div>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <small class="text-muted d-block">Period Start</small>
                    <div>{{ $parsedPeriodStart ?: '—' }}</div>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <small class="text-muted d-block">Period End</small>
                    <div>{{ $parsedPeriodEnd ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label>Report Title</label>
            <input type="text" class="form-control" id="crime_report_title" value="{{ old('report_title', $crimeStat?->report_title ?? data_get($stats, 'report.title')) }}">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label>Period Start</label>
            <input type="date" class="form-control" id="crime_period_start" value="{{ old('period_start', optional($crimeStat?->period_start)->toDateString() ?? data_get($stats, 'report.period.start')) }}">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label>Period End</label>
            <input type="date" class="form-control" id="crime_period_end" value="{{ old('period_end', optional($crimeStat?->period_end)->toDateString() ?? data_get($stats, 'report.period.end')) }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label>Crime Risk</label>
            <select class="form-control" id="crime_risk">
                <option value="">(blank)</option>
                @foreach($crimeRiskOptions as $opt)
                    <option value="{{ $opt }}" {{ $crimeRiskValue === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="d-block">Review</label>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="crime_is_reviewed" {{ old('is_reviewed', (bool) ($crimeStat?->is_reviewed ?? false)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="crime_is_reviewed">Mark as reviewed</label>
            </div>
            @if($crimeStat?->reviewed_at)
                <small class="text-muted">Reviewed at: {{ $crimeStat->reviewed_at->format('M j, Y g:i A') }}</small>
            @endif
        </div>
    </div>
</div>

<hr class="border-secondary">

<h5>Offense Rates (for PDF)</h5>

@if(!$crimeStat)
    <div class="alert alert-info">No PDF extracted yet. You can fill these rows manually, or upload a Crime Report PDF to pre-fill them.</div>
@endif

    <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered mb-0" id="crime-offenses-table">
            <thead>
                <tr>
                    <th style="width: 18%">Label</th>
                    <th style="width: 14%">Category</th>
                    <th style="width: 8%">Count</th>
                    <th style="width: 12%">Prop / 1000</th>
                    <th style="width: 12%">State / 1000</th>
                    <th style="width: 12%">Nat / 1000</th>
                    <th style="width: 12%">Compare</th>
                    <th style="width: 12%">SG</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offenses as $idx => $row)
                    <tr>
                        <td>
                            <input type="text" class="form-control form-control-sm crime-offense-label" value="{{ data_get($row, 'label') }}" data-index="{{ $idx }}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm crime-offense-category" value="{{ data_get($row, 'category') }}" data-index="{{ $idx }}" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm crime-offense-count" value="{{ data_get($row, 'count') }}" data-index="{{ $idx }}" min="0">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm crime-offense-rate" value="{{ data_get($row, 'rate_per_1000') }}" data-index="{{ $idx }}" min="0" step="0.01">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm crime-offense-state-rate" value="{{ data_get($row, 'state_rate_per_1000') }}" data-index="{{ $idx }}" min="0" step="0.01">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm crime-offense-national-rate" value="{{ data_get($row, 'national_rate_per_1000') }}" data-index="{{ $idx }}" min="0" step="0.01">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm crime-offense-compare" value="{{ data_get($row, 'comparison_to_state') }}" data-index="{{ $idx }}" placeholder="e.g. +1.2">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm crime-offense-sg" value="{{ data_get($row, 'securitygauge_score') }}" data-index="{{ $idx }}" min="1" max="5" step="1" placeholder="1-5">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<hr class="border-secondary">

<h5>Crime Risk Trends (5-year trend vs nation)</h5>

<div class="table-responsive">
    <table class="table table-dark table-striped table-bordered mb-0" id="crime-trends-table">
        <thead>
            <tr>
                <th style="width: 25%">Metric</th>
                <th>Trend (display text)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Crime</td>
                <td><input type="text" class="form-control form-control-sm" id="crime_trend_total" value="{{ data_get($trends, 'total_crime') }}" placeholder="e.g. -26% (decrease/improvement)"></td>
            </tr>
            <tr>
                <td>Violent Crime</td>
                <td><input type="text" class="form-control form-control-sm" id="crime_trend_violent" value="{{ data_get($trends, 'violent_crime') }}" placeholder="e.g. -15% (decrease/improvement)"></td>
            </tr>
            <tr>
                <td>Property Crime</td>
                <td><input type="text" class="form-control form-control-sm" id="crime_trend_property" value="{{ data_get($trends, 'property_crime') }}" placeholder="e.g. -36% (decrease/improvement)"></td>
            </tr>
        </tbody>
    </table>
</div>

<hr class="border-secondary">

<h5>Raw Extracted Text (read-only)</h5>
<textarea class="form-control" rows="8" readonly>{{ data_get($stats, 'raw.text') }}</textarea>

<hr class="border-secondary">

<h5>Extracted Payload (JSON, read-only)</h5>
<pre class="bg-light border rounded p-3 mb-0" style="white-space: pre-wrap; font-size: 12px;">{{ json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

<input type="hidden" id="crimeStatsCsrf" value="{{ csrf_token() }}">

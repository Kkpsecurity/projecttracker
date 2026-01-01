<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HB 837 CPTED Assessment Report - {{ $hb837->property_name }}</title>
    <style>
        @page {
            margin: 40px 40px;
        }

        body {
            font-family: "Arial", "Helvetica", sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .cover {
            text-align: center;
            margin-top: 140px;
        }

        .cover .line-1 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .cover .line-2 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .cover .line-3 {
            font-size: 16px;
            margin-bottom: 14px;
        }

        .cover .report-title {
            font-size: 18px;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 13px;
            margin: 14px 0 6px 0;
        }

        .subsection-title {
            font-size: 12px;
            margin: 10px 0 6px 0;
        }

        .small {
            font-size: 10px;
            color: #6c757d;
        }

        .page-break {
            page-break-before: always;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            vertical-align: top;
        }

        .table th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: left;
        }

        .label {
            width: 35%;
            background: #f8f9fa;
            font-weight: bold;
        }

        .note-box {
            border: 1px solid #dee2e6;
            padding: 8px;
            margin: 6px 0 10px 0;
            page-break-inside: avoid;
        }

        .img-placeholder {
            border: 1px solid #dee2e6;
            padding: 18px;
            text-align: center;
            margin: 6px 0 10px 0;
            page-break-inside: avoid;
        }

        .img-placeholder .ph-title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $fullAddress = trim(implode(', ', array_filter([$hb837->address, $hb837->city, $hb837->state . ' ' . $hb837->zip]))) ?: 'N/A';
        $consultantName = $hb837->consultant ? trim($hb837->consultant->first_name . ' ' . $hb837->consultant->last_name) : 'N/A';
        $siteAssessmentDate = $hb837->scheduled_date_of_inspection?->format('j F Y') ?? 'N/A';
        $reportDate = isset($generated_at) ? (string) $generated_at : now()->format('j F Y');

        $crimeStats = $hb837->crimeStats;
        $offenses = $crimeStats ? (data_get($crimeStats->stats, 'tables.offenses', []) ?: []) : [];
        $crimeRisk = $crimeStats?->crime_risk ?? data_get($crimeStats?->stats, 'summary.crime_risk') ?? $hb837->securitygauge_crime_risk;
        $periodStart = $crimeStats?->period_start?->format('M j, Y') ?? data_get($crimeStats?->stats, 'report.period.start');
        $periodEnd = $crimeStats?->period_end?->format('M j, Y') ?? data_get($crimeStats?->stats, 'report.period.end');

        $findings = $hb837->findings ? $hb837->findings->sortBy('created_at')->values() : collect();

        $cbRankForSeverity = function (?string $severity): string {
            $severity = strtolower(trim((string) $severity));
            return match ($severity) {
                'critical', 'severe', 'high' => 'CB1',
                'medium', 'moderate' => 'CB2',
                'low' => 'CB3',
                default => 'CB2',
            };
        };

        $matchesKeywords = function ($finding, array $keywords): bool {
            $haystack = strtolower(trim(
                implode(' ', array_filter([
                    $finding->category ?? '',
                    $finding->location_context ?? '',
                    $finding->description ?? '',
                    $finding->recommendation ?? '',
                ]))
            ));

            foreach ($keywords as $k) {
                if ($k !== '' && str_contains($haystack, strtolower($k))) {
                    return true;
                }
            }

            return false;
        };

        $bucketFindings = function ($findings, array $keywords) use ($matchesKeywords) {
            return $findings->filter(fn ($f) => $matchesKeywords($f, $keywords))->values();
        };

        $statuteConditions = [
            [
                'key' => 'cctv_system',
                'label' => 'CCTV System',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(1)',
                'text' => 'A security camera system at points of entry and exit which records, and maintains as retrievable for at least 30 days, video footage to assist in offender identification and apprehension.',
            ],
            [
                'key' => 'parking_lot_illumination',
                'label' => 'Parking Lot Illumination',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(2)',
                'text' => 'A lighted parking lot illuminated at an intensity of at least an average of 1.8 foot-candles per square foot at 18 inches above the surface from dusk until dawn or controlled by photocell or any similar electronic device that provides light from dusk until dawn.',
            ],
            [
                'key' => 'other_lighting',
                'label' => 'Other Lighting',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(3)',
                'text' => 'Lighting in walkways, laundry rooms, common areas, and porches. Such lighting must be illuminated from dusk until dawn or controlled by photocell or any similar electronic device that provides light from dusk until dawn.',
            ],
            [
                'key' => 'deadbolt_locks',
                'label' => 'Deadbolt Locks',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(4)',
                'text' => 'At least a 1-inch deadbolt in each dwelling unit door.',
            ],
            [
                'key' => 'locking_devices',
                'label' => 'Locking Devices',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(5)',
                'text' => 'A locking device on each window, each exterior sliding door, and any other doors not used for community purposes.',
            ],
            [
                'key' => 'pool_access',
                'label' => 'Pool Access',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(6)',
                'text' => 'Locked gates with key or fob access along pool fence areas.',
            ],
            [
                'key' => 'peepholes',
                'label' => 'Peepholes/Door Viewers',
                'statute' => 'Fla. Stat. § 768.0706(2)(a)(7)',
                'text' => 'A peephole or door viewer on each dwelling unit door that does not include a window or that does not have a window next to the door.',
            ],
        ];
    @endphp

    <!-- Cover page -->
    <div class="cover">
        <div class="line-1">Fla. Stat. § 768.0706 (HB 837) Compliance &amp;</div>
        <div class="line-2">Crime Prevention Through Environmental Design (CPTED)</div>
        <div class="report-title">Assessment Report</div>
    </div>

    <div class="page-break"></div>

    <!-- Property Information -->
    <h2 class="section-title">Property Information</h2>
    <table class="table avoid-break">
        <tbody>
            <tr>
                <td class="label">Property Name:</td>
                <td>{{ $hb837->property_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Owner:</td>
                <td>{{ $hb837->owner_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Address:</td>
                <td>{{ $fullAddress }}</td>
            </tr>
            <tr>
                <td class="label">No of Units:</td>
                <td>{{ $hb837->units ? number_format($hb837->units) : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Est. No. of Residents:</td>
                <td>N/A</td>
            </tr>
            <tr>
                <td class="label">Demographics:</td>
                <td>N/A</td>
            </tr>
            <tr>
                <td class="label">Police Jurisdiction:</td>
                <td>{{ $hb837->county ?: 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Assessment Details -->
    <h2 class="section-title">Assessment Details</h2>
    <table class="table avoid-break">
        <tbody>
            <tr>
                <td class="label">Florida CPTED Practitioner(s):</td>
                <td>{{ $consultantName }}</td>
            </tr>
            <tr>
                <td class="label">Property Manager:</td>
                <td>{{ $hb837->property_manager_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Date of Site Assessment:</td>
                <td>{{ $siteAssessmentDate }}</td>
            </tr>
            <tr>
                <td class="label">Date of Report:</td>
                <td>{{ $reportDate }}</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- 1. Crime and Victimization Assessment -->
    <h2 class="section-title">1. Crime and Victimization Assessment</h2>

    <h3 class="subsection-title">1.1 Crime Statistics</h3>
    <div class="small">
        @if ($crimeRisk)
            Current Crime Risk: <strong>{{ strtoupper((string) $crimeRisk) }}</strong>
        @else
            Current Crime Risk: <strong>N/A</strong>
        @endif
        @if ($periodStart || $periodEnd)
            <br>Report Period: {{ $periodStart ?: 'N/A' }}@if ($periodEnd)&nbsp;&ndash;&nbsp;{{ $periodEnd }}@endif
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 22%;">Offense</th>
                <th style="width: 14%;">Property &amp; Neighborhood</th>
                <th style="width: 14%;">State</th>
                <th style="width: 14%;">National</th>
                <th style="width: 18%;">Comparison to State</th>
                <th style="width: 18%;">SecurityGauge®</th>
            </tr>
        </thead>
        <tbody>
            @php
                $templateOffenses = ['Homicide', 'Rape', 'Aggravated Assault', 'Burglary', 'Vehicle Theft'];
                $offenseByLabel = [];
                foreach ($offenses as $row) {
                    $label = (string) data_get($row, 'label', '');
                    if ($label !== '') {
                        $offenseByLabel[strtolower($label)] = $row;
                    }
                }

                $fmt = function ($value): string {
                    if ($value === null || $value === '') {
                        return '';
                    }
                    if (is_numeric($value)) {
                        return number_format((float) $value, 2);
                    }
                    return (string) $value;
                };
            @endphp

            @foreach ($templateOffenses as $label)
                @php
                    $row = $offenseByLabel[strtolower($label)] ?? null;
                    $propRate = $row ? data_get($row, 'rate_per_1000') : null;
                    $stateRate = $row ? data_get($row, 'state_rate_per_1000') : null;
                    $nationalRate = $row ? data_get($row, 'national_rate_per_1000') : null;
                    $compare = $row ? data_get($row, 'comparison_to_state') : null;
                    $sg = $row ? data_get($row, 'securitygauge_score') : null;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $fmt($propRate) }}</td>
                    <td>{{ $fmt($stateRate) }}</td>
                    <td>{{ $fmt($nationalRate) }}</td>
                    <td>{{ $compare }}</td>
                    <td>{{ $sg }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $trends = is_array(data_get($crimeStats?->stats, 'tables.crime_risk_trends'))
            ? data_get($crimeStats?->stats, 'tables.crime_risk_trends')
            : [];
    @endphp

    <div class="avoid-break">
        <div class="subsection-title" style="margin-top: 10px;">Crime Risk Trends</div>
        <div class="small" style="margin-bottom: 6px;">Trends at this property as compared to the nation from 5 years ago to present.</div>
        <table class="table">
            <tbody>
                <tr>
                    <td style="width: 30%;"><strong>Total Crime</strong></td>
                    <td>{{ data_get($trends, 'total_crime') }}</td>
                </tr>
                <tr>
                    <td><strong>Violent Crime</strong></td>
                    <td>{{ data_get($trends, 'violent_crime') }}</td>
                </tr>
                <tr>
                    <td><strong>Property Crime</strong></td>
                    <td>{{ data_get($trends, 'property_crime') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="subsection-title">1.2 Geospatial Crime Analysis</h3>
    <div class="avoid-break">
        <div style="font-weight:bold; margin: 6px 0;">Violent Crime Risk</div>
        <div class="img-placeholder">
            <div class="ph-title">[IMAGE PLACEHOLDER – SecurityGauge Violent Endangerment Risk Map]</div>
            <div class="small">(Use SecurityGauge map image when available.)</div>
        </div>

        <div style="font-weight:bold; margin: 10px 0 6px 0;">Burglary Risk</div>
        <div class="img-placeholder">
            <div class="ph-title">[IMAGE PLACEHOLDER – SecurityGauge Burglary Risk Map]</div>
            <div class="small">(Use SecurityGauge map image when available.)</div>
        </div>
    </div>

    <div class="note-box">
        <div style="font-weight:bold; margin-bottom: 6px;">NOTES:</div>
        @if ($hb837->consultant_notes)
            <div>{{ $hb837->consultant_notes }}</div>
        @else
            <div class="small">Leave blank if no unique sources of risk are observed.</div>
        @endif
    </div>

    <h3 class="subsection-title">1.3 Recent Incidents</h3>
    <div class="small" style="margin-bottom: 6px;">Following is a summary of recent incidents of crime as reported to the Florida CPTED Practitioner.</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 22%;">Date</th>
                    <th>Summary of Incident</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $recentIncidents = $hb837->recentIncidents()->orderBy('sort_order')->orderBy('id')->get();
                @endphp

                @if($recentIncidents->count())
                    @foreach($recentIncidents as $incident)
                        <tr>
                            <td>{{ $incident->incident_date ?: 'N/A' }}</td>
                            <td>{{ $incident->summary }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td></td>
                        <td class="small">No incidents provided.</td>
                    </tr>
                @endif
            </tbody>
        </table>

    <div class="page-break"></div>

    <!-- 2. Specific Property Conditions -->
    <h2 class="section-title">2. Specific Property Conditions as defined in Fla. Stat. § 768.0706(2)(a)</h2>
    <div class="small" style="margin-bottom: 8px;">
        The following section describes the consultant’s observations regarding property conditions specified in Fla. Stat. § 768.0706(2)(a).
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 28%;">Condition</th>
                <th style="width: 12%;">Status</th>
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statuteByKey = $hb837->statuteConditions()->get()->keyBy('condition_key');
                $statusLabel = function (?string $status): string {
                    $status = strtolower(trim((string) $status));
                    return match ($status) {
                        'compliant' => '☑ Compliant',
                        'non_compliant' => '☒ Non-compliant',
                        'unknown' => '☐ Unknown',
                        default => '',
                    };
                };
            @endphp

            @foreach ($statuteConditions as $c)
                @php
                    $key = $c['key'] ?? null;
                    $curated = $key ? $statuteByKey->get($key) : null;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:bold;">{{ $c['label'] }}</div>
                        <div class="small">{{ $c['statute'] }}</div>
                        <div class="small">{{ $c['text'] }}</div>
                    </td>
                    <td>{{ $statusLabel($curated?->status) }}</td>
                    <td>{{ $curated?->observations }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- 3. CPTED Conditions -->
    <h2 class="section-title">3. Crime Prevention Through Environmental Design (CPTED) Conditions</h2>
    <div class="small" style="margin-bottom: 8px;">The following section describes the consultant’s observations regarding Crime Prevention Through Environmental Design (CPTED).</div>

    <h3 class="subsection-title">3.1 Natural Surveillance</h3>
    <div class="note-box">
        @if ($findings->count())
            <div class="small">Observations summarized from findings (where applicable):</div>
            <div>{{ $findings->pluck('description')->filter()->take(3)->implode(' ' ) }}</div>
        @else
            <div>The consultant had no noteworthy observations regarding natural surveillance.</div>
        @endif
    </div>

    <h3 class="subsection-title">3.2 Natural Access Control</h3>
    <div class="note-box">
        <div>The consultant had no noteworthy observations regarding natural access control.</div>
    </div>

    <h3 class="subsection-title">3.3 Territorial Reinforcement</h3>
    <div class="note-box">
        <div>The consultant had no noteworthy observations regarding territorial reinforcement.</div>
    </div>

    <h3 class="subsection-title">3.4 Maintenance</h3>
    <div class="note-box">
        <div>The consultant had no noteworthy observations regarding maintenance.</div>
    </div>

    <h3 class="subsection-title">3.5 Additional CPTED Issues</h3>
    <div class="note-box">
        <div>The consultant had no noteworthy observations regarding additional CPTED issues.</div>
    </div>

    <div class="page-break"></div>

    <!-- 4. Risk Management Considerations -->
    <h2 class="section-title">4. Risk Management Considerations</h2>
    <div class="small" style="margin-bottom: 8px;">
        Following is a description of proposed measures to reduce conditions contributing to criminal confidence and capability, and/or achieve compliance with conditions defined in Fla. Stat. § 768.0706(2)(a).
    </div>

    <table class="table avoid-break">
        <thead>
            <tr>
                <th style="width: 10%;">CB Rank</th>
                <th>Definition</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>CB1</strong></td>
                <td>Necessary for statutory compliance or likely significant benefit with minimal cost.</td>
            </tr>
            <tr>
                <td><strong>CB2</strong></td>
                <td>Likely significant benefit; may require budget planning as part of a long-term strategy.</td>
            </tr>
            <tr>
                <td><strong>CB3</strong></td>
                <td>Lesser benefit and/or high cost requiring long-term budget planning.</td>
            </tr>
            <tr>
                <td><strong>CB4</strong></td>
                <td>Impractical or restricted by ordinance; stated for comprehensiveness.</td>
            </tr>
        </tbody>
    </table>

    @php
        $curatedMeasures = $hb837->riskMeasures ?? collect();
        $hasCuratedMeasures = $curatedMeasures && $curatedMeasures->count();

        $renderCuratedMeasures = function ($section) use ($curatedMeasures) {
            $bucket = $curatedMeasures
                ->where('section', (string) $section)
                ->sortBy([
                    ['sort_order', 'asc'],
                    ['measure_no', 'asc'],
                    ['id', 'asc'],
                ])
                ->values();

            if (!$bucket->count()) {
                echo '<div class="small" style="margin: 4px 0 10px 0;">No measures provided.</div>';
                return;
            }

            echo '<table class="table">';
            echo '<thead><tr><th style="width: 8%;">No.</th><th>Risk Reduction Measure</th><th style="width: 12%;">CB Rank</th></tr></thead>';
            echo '<tbody>';

            $i = 1;
            foreach ($bucket as $m) {
                $no = $m->measure_no ? (string) $m->measure_no : (string) $i;
                $measure = trim((string) ($m->measure ?? ''));
                $rank = trim((string) ($m->cb_rank ?? ''));
                echo '<tr>';
                echo '<td>' . e($no) . '</td>';
                echo '<td>' . e($measure) . '</td>';
                echo '<td>' . e($rank) . '</td>';
                echo '</tr>';
                $i++;
            }

            echo '</tbody></table>';
        };

        if (!$hasCuratedMeasures) {
            $bucket41 = $bucketFindings($findings, ['cctv', 'camera', 'illumination', 'lighting', 'deadbolt', 'lock', 'window', 'sliding', 'pool', 'gate', 'peephole', 'door viewer']);
            $bucket43 = $bucketFindings($findings, ['parking', 'lot', 'grounds', 'landscap', 'perimeter', 'sidewalk', 'dumpster', 'trash']);
            $bucket44 = $bucketFindings($findings, ['building', 'unit', 'stair', 'corridor', 'hall', 'breezeway']);
            $bucket45 = $bucketFindings($findings, ['office', 'laundry', 'clubhouse', 'community', 'mail', 'leasing']);
            $bucket46 = $bucketFindings($findings, ['policy', 'procedure', 'management', 'training', 'engagement', 'program']);
            $usedIds = collect([$bucket41, $bucket43, $bucket44, $bucket45, $bucket46])->flatten()->pluck('id')->unique()->filter();
            $bucket42 = $findings->filter(fn ($f) => !$usedIds->contains($f->id))->values();
        }

        $renderFindingMeasures = function ($bucket) use ($cbRankForSeverity) {
            if (!$bucket || !$bucket->count()) {
                echo '<div class="small" style="margin: 4px 0 10px 0;">No measures provided.</div>';
                return;
            }

            echo '<table class="table">';
            echo '<thead><tr><th style="width: 8%;">No.</th><th>Risk Reduction Measure</th><th style="width: 12%;">CB Rank</th></tr></thead>';
            echo '<tbody>';

            $i = 1;
            foreach ($bucket as $f) {
                $measure = trim((string) ($f->recommendation ?: $f->description ?: ''));
                $rank = $cbRankForSeverity($f->severity);
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . e($measure) . '</td>';
                echo '<td>' . e($rank) . '</td>';
                echo '</tr>';
                $i++;
            }

            echo '</tbody></table>';
        };
    @endphp

    <h3 class="subsection-title">4.1 Specific Property Conditions as defined in Fla. Stat. § 768.0706(2)(a)</h3>
    @if ($hasCuratedMeasures)
        {!! $renderCuratedMeasures('4.1') !!}
    @else
        {!! $renderFindingMeasures($bucket41) !!}
    @endif

    <h3 class="subsection-title">4.2 Security Infrastructure</h3>
    @if ($hasCuratedMeasures)
        {!! $renderCuratedMeasures('4.2') !!}
    @else
        {!! $renderFindingMeasures($bucket42) !!}
    @endif

    <h3 class="subsection-title">4.3 Outdoor Grounds</h3>
    @if ($hasCuratedMeasures)
        {!! $renderCuratedMeasures('4.3') !!}
    @else
        {!! $renderFindingMeasures($bucket43) !!}
    @endif

    <h3 class="subsection-title">4.4 Residential Buildings &amp; Units</h3>
    @if ($hasCuratedMeasures)
        {!! $renderCuratedMeasures('4.4') !!}
    @else
        {!! $renderFindingMeasures($bucket44) !!}
    @endif

    <h3 class="subsection-title">4.5 Community Buildings and Main Office</h3>
    @if ($hasCuratedMeasures)
        {!! $renderCuratedMeasures('4.5') !!}
    @else
        {!! $renderFindingMeasures($bucket45) !!}
    @endif

    <h3 class="subsection-title">4.6 Management Practices &amp; Community Engagement</h3>
    @if ($hasCuratedMeasures)
        {!! $renderCuratedMeasures('4.6') !!}
    @else
        {!! $renderFindingMeasures($bucket46) !!}
    @endif

    @php
        $filesByPosition = ($hb837->relationLoaded('files') ? $hb837->files : $hb837->files()->get())
            ->whereNotNull('file_position')
            ->keyBy('file_position');

        $appendixMap = $filesByPosition->get('appendix_a_map');
        $appendixPhoto1 = $filesByPosition->get('appendix_a_photo_1');
        $appendixPhoto2 = $filesByPosition->get('appendix_a_photo_2');
        $appendixPhoto3 = $filesByPosition->get('appendix_a_photo_3');

        $fileImgSrc = function ($file) {
            if (!$file || empty($file->file_path)) {
                return null;
            }

            $relative = ltrim((string) $file->file_path, '/');

            // Prefer the real storage path (works even if public/storage symlink is missing).
            $fsPath = storage_path('app/public/' . $relative);
            if (!is_file($fsPath)) {
                $fsPath = public_path('storage/' . $relative);
            }

            if (!is_file($fsPath)) {
                return null;
            }

            // DomPDF on Windows is more reliable with a file:// URI and forward slashes.
            $src = str_replace('\\', '/', $fsPath);
            if (preg_match('/^[A-Za-z]:\//', $src)) {
                $src = 'file:///' . $src;
            }

            return $src;
        };
    @endphp

    @if ($appendixMap || $appendixPhoto1 || $appendixPhoto2 || $appendixPhoto3)
        <div class="page-break"></div>
        <h2 class="section-title">Appendix A. Photos</h2>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 34%; vertical-align: top; padding-right: 10px;">
                    <div class="small" style="line-height: 1.35;">
                        <div style="font-weight: bold; margin-bottom: 6px;">Map Notes</div>
                        @if ($appendixMap && $appendixMap->description)
                            {!! nl2br(e($appendixMap->description)) !!}
                        @else
                            <span class="small">(Optional) Add notes/labels in the file description for the map upload.</span>
                        @endif
                    </div>
                </td>
                <td style="width: 66%; vertical-align: top;">
                    @php $mapSrc = $fileImgSrc($appendixMap); @endphp
                    @if ($mapSrc)
                        <img src="{{ $mapSrc }}" alt="Appendix map" style="width: 100%; height: auto; border: 1px solid #999;">
                    @else
                        <div class="img-placeholder" style="height: 320px;">
                            <div class="ph-title">[MAP IMAGE – upload with position Appendix A Map]</div>
                            <div class="small">Position: appendix_a_map</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 14px;">
            <tr>
                @foreach ([$appendixPhoto1, $appendixPhoto2, $appendixPhoto3] as $i => $photo)
                    <td style="width: 33.33%; vertical-align: top; padding-right: {{ $i < 2 ? '10px' : '0' }};">
                        @php $photoSrc = $fileImgSrc($photo); @endphp
                        @if ($photoSrc)
                            <img src="{{ $photoSrc }}" alt="Appendix photo {{ $i + 1 }}" style="width: 100%; height: 130px; object-fit: cover; border: 1px solid #999;">
                        @else
                            <div class="img-placeholder" style="height: 130px;">
                                <div class="ph-title">[PHOTO {{ $i + 1 }} – upload with position]</div>
                                <div class="small">Position: appendix_a_photo_{{ $i + 1 }}</div>
                            </div>
                        @endif

                        @if ($photo && $photo->description)
                            <div class="small" style="margin-top: 6px;">{!! nl2br(e($photo->description)) !!}</div>
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    @if ($show_map)
        <div class="page-break"></div>
        <h2 class="section-title">Property Location</h2>

        @if ($show_map && $map_url)
            <div class="img-placeholder">
                <div class="ph-title">Map (Property Location)</div>
                <img src="{{ $map_url }}" alt="Map showing location of {{ $hb837->address }}" style="max-width: 100%; height: auto;">
                <div class="small" style="margin-top: 6px;">Map view of property location</div>
            </div>
        @else
            <div class="note-box">
                <div style="font-weight:bold;">Property Address:</div>
                <div>{{ $fullAddress }}</div>
                @if ($map_fallback_reason)
                    <div class="small" style="margin-top: 6px;">Reason: {{ $map_fallback_reason }}</div>
                @endif
            </div>
        @endif
    @endif
</body>

</html>

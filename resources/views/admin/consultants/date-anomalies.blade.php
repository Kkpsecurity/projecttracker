@extends('adminlte::page')

@section('title', 'Date Anomalies - Consultant Report')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-exclamation-triangle text-warning"></i> Date Anomalies Report</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.consultants.report') }}">Consultant Report</a></li>
                <li class="breadcrumb-item active">Date Anomalies</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> Data Quality Issues Detected</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Found {{ $total_suspicious }} suspicious date records</h5>
                <p class="mb-2">
                    The following completed projects have date inconsistencies that affect the average completion time calculation:
                </p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-bar"></i> Issues by Type:</h6>
                        <ul class="mb-0">
                            @if($by_issue_type['epoch_1970'] > 0)
                                <li class="text-danger"><strong>1970 Epoch Dates:</strong> {{ $by_issue_type['epoch_1970'] }} (Unix timestamp conversion errors)</li>
                            @endif
                            @if($by_issue_type['pre_1980'] > 0)
                                <li class="text-danger"><strong>Pre-1980 Dates:</strong> {{ $by_issue_type['pre_1980'] }} (Likely data corruption)</li>
                            @endif
                            @if($by_issue_type['future_dates'] > 0)
                                <li class="text-warning"><strong>Future Dates:</strong> {{ $by_issue_type['future_dates'] }} (Dates beyond today)</li>
                            @endif
                            @if($by_issue_type['too_old'] > 0)
                                <li class="text-info"><strong>Very Old Projects:</strong> {{ $by_issue_type['too_old'] }} (Over 10 years old)</li>
                            @endif
                            @if($by_issue_type['backwards'] > 0)
                                <li class="text-danger"><strong>Backwards Dates:</strong> {{ $by_issue_type['backwards'] }} (Report before inspection)</li>
                            @endif
                            @if($by_issue_type['excessive_duration'] > 0)
                                <li class="text-warning"><strong>Excessive Duration:</strong> {{ $by_issue_type['excessive_duration'] }} (Over 1 year)</li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-lightbulb"></i> Common Causes:</h6>
                        <ul class="mb-0">
                            <li><strong>1970 dates:</strong> Database NULL converted to Unix epoch (Jan 1, 1970)</li>
                            <li><strong>Future dates:</strong> Typo in year entry (e.g., 2026 instead of 2025)</li>
                            <li><strong>Old dates:</strong> Imported legacy data with incorrect dates</li>
                            <li><strong>Backwards:</strong> Dates entered in wrong fields</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Suspicious Records</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($anomalies->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <p>No date anomalies found. All completion times are within normal ranges.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>HB837 ID</th>
                                <th>Property Name</th>
                                <th>Consultant</th>
                                <th>Scheduled Inspection</th>
                                <th>Report Submitted</th>
                                <th class="text-center">Days Diff</th>
                                <th>Issues Detected</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anomalies as $anomaly)
                                @php
                                    $rowClass = 'table-warning';
                                    if ($anomaly['is_1970']) $rowClass = 'table-danger';
                                    elseif ($anomaly['is_future']) $rowClass = 'table-info';
                                    elseif ($anomaly['is_backwards']) $rowClass = 'table-danger';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <a href="{{ route('admin.hb837.show', $anomaly['id']) }}" target="_blank">
                                            #{{ $anomaly['id'] }}
                                        </a>
                                    </td>
                                    <td>{{ $anomaly['property_name'] ?: 'N/A' }}</td>
                                    <td>{{ $anomaly['consultant'] }}</td>
                                    <td>
                                        {{ $anomaly['scheduled_date'] }}
                                        @if($anomaly['scheduled_year'] == 1970)
                                            <span class="badge badge-danger">1970!</span>
                                        @elseif($anomaly['scheduled_year'] < 1980)
                                            <span class="badge badge-danger">Pre-1980</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $anomaly['report_submitted'] }}
                                        @if($anomaly['report_year'] == 1970)
                                            <span class="badge badge-danger">1970!</span>
                                        @elseif($anomaly['report_year'] < 1980)
                                            <span class="badge badge-danger">Pre-1980</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($anomaly['is_backwards'])
                                            <span class="badge badge-danger">
                                                <i class="fas fa-arrow-left"></i> {{ number_format($anomaly['abs_days']) }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                {{ number_format($anomaly['abs_days']) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($anomaly['issues'] as $issue)
                                            @php
                                                $badgeClass = 'badge-secondary';
                                                if (str_contains($issue, '1970')) $badgeClass = 'badge-danger';
                                                elseif (str_contains($issue, 'Pre-1980')) $badgeClass = 'badge-danger';
                                                elseif (str_contains($issue, 'Future')) $badgeClass = 'badge-info';
                                                elseif (str_contains($issue, 'Backwards')) $badgeClass = 'badge-danger';
                                                elseif (str_contains($issue, 'Excessive')) $badgeClass = 'badge-warning';
                                                elseif (str_contains($issue, 'Too Old')) $badgeClass = 'badge-secondary';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} mb-1">{{ $issue }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.hb837.edit', $anomaly['id']) }}" class="btn btn-sm btn-primary" title="Edit to fix dates">
                                            <i class="fas fa-edit"></i> Fix
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if(!$anomalies->isEmpty())
            <div class="card-footer">
                <strong>Recommendation:</strong> Review and correct the dates for these projects to ensure accurate completion time reporting.
            </div>
        @endif
    </div>

    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-lightbulb"></i> How to Fix</h3>
        </div>
        <div class="card-body">
            <ol>
                <li>Click on the HB837 ID or "Fix Dates" button to open the project</li>
                <li>Review the <strong>Scheduled Date of Inspection</strong> and <strong>Report Submitted</strong> fields</li>
                <li>Correct any dates that are clearly wrong (e.g., year 1970, future dates, or reversed dates)</li>
                <li>Save the changes</li>
                <li>Return to this report to verify the issue is resolved</li>
            </ol>
            <p class="mb-0 text-muted">
                <i class="fas fa-info-circle"></i> 
                Note: The average completion time calculation uses absolute values, so negative times won't appear, 
                but fixing these dates will improve data accuracy.
            </p>
        </div>
    </div>
@stop

@section('css')
    <style>
        @media print {
            .btn, .breadcrumb, .card-tools { display: none; }
        }
    </style>
@stop

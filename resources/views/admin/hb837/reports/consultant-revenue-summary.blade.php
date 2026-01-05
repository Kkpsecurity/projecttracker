@extends('adminlte::page')

@section('title', 'Consultant Revenue Summary - HB837')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="text-shadow-md"><i class="fas fa-chart-line"></i> Consultant Revenue Summary</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">Consultant Revenue Summary</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Date Range (Report Delivered)
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.hb837.reports.consultant-revenue-summary') }}" class="form-inline flex-wrap">
                    <div class="form-group mr-3 mb-2">
                        <label for="start_date" class="mr-2">Start</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $start_date }}">
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="end_date" class="mr-2">End</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $end_date }}">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 mr-2">
                        <i class="fas fa-play"></i> Run Report
                    </button>

                    <a class="btn btn-success mb-2 mr-2"
                       href="{{ route('admin.hb837.reports.consultant-revenue-summary.export', ['format' => 'xlsx']) }}?start_date={{ urlencode($start_date) }}&end_date={{ urlencode($end_date) }}">
                        <i class="fas fa-file-excel"></i> Export XLSX
                    </a>
                    <a class="btn btn-info mb-2"
                       href="{{ route('admin.hb837.reports.consultant-revenue-summary.export', ['format' => 'csv']) }}?start_date={{ urlencode($start_date) }}&end_date={{ urlencode($end_date) }}">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </form>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3 mb-0">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table"></i> Summary</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Consultant Name</th>
                                <th class="text-right">Completed Projects</th>
                                <th class="text-right">Gross Revenue</th>
                                <th class="text-right">Estimated Expenses</th>
                                <th class="text-right">Net Revenue</th>
                                <th class="text-right">Avg Completion (Days)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td>{{ $row['consultant_name'] }}</td>
                                    <td class="text-right">{{ number_format($row['completed_projects']) }}</td>
                                    <td class="text-right">${{ number_format($row['gross_revenue'], 2) }}</td>
                                    <td class="text-right">${{ number_format($row['estimated_expenses'], 2) }}</td>
                                    <td class="text-right">${{ number_format($row['net_revenue'], 2) }}</td>
                                    <td class="text-right">
                                        {{ $row['avg_completion_days'] === null ? '—' : number_format($row['avg_completion_days'], 1) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No completed projects found for this date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

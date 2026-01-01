@extends('adminlte::page')

@section('title', 'View HB837 Record - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="text-shadow-md">HB837 Record Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">View Record</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-shadow-sm">{{ $hb837->property_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.hb837.edit', $hb837->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.hb837.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-shadow-sm">Property Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Property Name:</th>
                                    <td>{{ $hb837->property_name }}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $hb837->address }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $hb837->city }}</td>
                                </tr>
                                <tr>
                                    <th>County:</th>
                                    <td>{{ $hb837->county }}</td>
                                </tr>
                                <tr>
                                    <th>State:</th>
                                    <td>{{ $hb837->state }}</td>
                                </tr>
                                <tr>
                                    <th>ZIP:</th>
                                    <td>{{ $hb837->zip }}</td>
                                </tr>
                                <tr>
                                    <th>Property Type:</th>
                                    <td>{{ $hb837->property_type }}</td>
                                </tr>
                                <tr>
                                    <th>Units:</th>
                                    <td>{{ $hb837->units }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-shadow-sm">Project Status</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Report Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $hb837->report_status == 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst(str_replace('-', ' ', $hb837->report_status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Contracting Status:</th>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst($hb837->contracting_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Assigned Consultant:</th>
                                    <td>
                                        @if($hb837->consultant)
                                            {{ $hb837->consultant->first_name }} {{ $hb837->consultant->last_name }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Inspection Date:</th>
                                    <td>
                                        @if($hb837->scheduled_date_of_inspection)
                                            {{ \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection)->format('M j, Y') }}
                                        @else
                                            <span class="text-muted">Not scheduled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Crime Risk:</th>
                                    <td>
                                        @if($hb837->securitygauge_crime_risk)
                                            <span class="badge badge-secondary">{{ $hb837->securitygauge_crime_risk }}</span>
                                        @else
                                            <span class="text-muted">Not assessed</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($hb837->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5 class="text-shadow-sm">Notes</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $hb837->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

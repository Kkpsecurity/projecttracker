@extends('adminlte::page')

@php
    $pageTitle = $pageTitle ?? 'HB 837 Compliance';
@endphp

@section('title', $pageTitle . ' - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="text-shadow-md"><i class="fas fa-shield-alt"></i> {{ $pageTitle }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $pageTitle }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-success">
                    <div class="inner">
                        <h3>{{ $stats['total_projects'] ?? 0 }}</h3>
                        <p>HB837 Projects</p>
                        <small class="text-white-50">{{ $stats['projects_with_findings'] ?? 0 }} with findings</small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                        View Projects <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h3>{{ $stats['open_findings'] ?? 0 }}</h3>
                        <p>Open Findings</p>
                        <small class="text-white-50">{{ $stats['total_findings'] ?? 0 }} total</small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                        Manage Findings <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-primary">
                    <div class="inner">
                        <h3>{{ $stats['projects_with_crime_report'] ?? 0 }}</h3>
                        <p>Crime PDFs Uploaded</p>
                        <small class="text-white-50">{{ $stats['crime_report_uploads'] ?? 0 }} files</small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                        Upload / View Files <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-danger">
                    <div class="inner">
                        <h3>{{ $stats['projects_missing_crime_report'] ?? 0 }}</h3>
                        <p>Missing Crime PDF</p>
                        <small class="text-white-50">needs upload to proceed</small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                        Find Projects <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-info">
                    <div class="inner">
                        <h3>{{ $stats['crime_stats_pending_review'] ?? 0 }}</h3>
                        <p>Crime Stats Pending Review</p>
                        <small class="text-white-50">{{ $stats['crime_stats_total'] ?? 0 }} extracted</small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                        Go To Crime Data <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bullseye mr-2"></i>
                            Compliance Workflow Preview
                        </h3>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li>Open a project and add structured Findings (category/severity/location/description/recommendation).</li>
                            <li>Upload the SecurityGauge crime PDF to the project Files tab (category: <strong>crime_report</strong>).</li>
                            <li>(Next phase) Extract crime stats → review → include in generated report.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-2"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.hb837.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus mr-1"></i> New HB837 Project
                        </a>
                        <a href="{{ route('admin.hb837.index') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Find a Project
                        </a>
                        <div class="text-muted mt-2">
                            <small>Updated: {{ now()->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

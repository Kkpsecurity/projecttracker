@extends('adminlte::page')

@section('title', 'Analytics Dashboard - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-chart-line mr-2"></i>
                {{ $title ?? 'Analytics Dashboard' }}
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Analytics</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Coming Soon
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="py-5">
                        <i class="fas fa-chart-bar fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted">{{ $title ?? 'Analytics Dashboard' }}</h3>
                        <p class="lead text-muted">
                            {{ $message ?? 'Analytics and reporting functionality will be implemented here.' }}
                        </p>
                        <p class="text-muted">
                            This section will include comprehensive analytics, reporting capabilities,
                            and data visualization tools for system monitoring and business intelligence.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Back to Admin Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

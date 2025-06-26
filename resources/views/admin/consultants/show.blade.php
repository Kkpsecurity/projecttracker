@extends('layouts.admin')

@section('title', 'Consultant Management')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Consultant Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Consultants</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card card-table">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="fas fa-user-tie"></i>
                                Consultants List
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.consultants.export', ['format' => 'excel']) }}">
                                        <i class="fas fa-file-excel"></i> Excel (.xlsx)
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.consultants.export', ['format' => 'csv']) }}">
                                        <i class="fas fa-file-csv"></i> CSV
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.consultants.export', ['format' => 'json']) }}">
                                        <i class="fas fa-file-code"></i> JSON
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.consultants.export', ['format' => 'excel', 'include_files' => 1]) }}">
                                        <i class="fas fa-file-excel"></i> Excel with Files
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.consultants.export', ['format' => 'csv', 'include_files' => 1]) }}">
                                        <i class="fas fa-file-csv"></i> CSV with Files
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('admin.consultants.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Consultant
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @include('admin.consultants.tables.consultants')
                </div>
            </div>
        </div>
    </div>
@stop

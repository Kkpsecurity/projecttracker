@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'HB837 Management - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="text-shadow-md"><i class="fas fa-shield-alt"></i> HB837 Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">HB837 Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-shadow-sm">
                        <i class="fas fa-tools"></i> Actions
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.hb837.create') }}" class="btn btn-success mr-2">
                        <i class="fas fa-plus"></i> Create New Record
                    </a>
                    <a href="{{ route('admin.hb837.smart-import.show') }}" class="btn btn-primary mr-2">
                        <i class="fas fa-magic"></i> Smart Import
                    </a>
                    <a href="#" id="export-data-btn" class="btn btn-info mr-2" onclick="exportCurrentTab()">
                        <i class="fas fa-file-download"></i> Export Data
                    </a>
                    <button type="button" class="btn btn-warning mr-2 bulk-actions-container" onclick="bulkActions()">
                        <i class="fas fa-edit"></i> Bulk Actions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Warnings & Business Summary -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="background:#f8f9fa;border-radius:8px;padding:16px 22px;font-size:1rem;color:#333;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <div class="mb-2 mb-md-0">
                    <strong class="mr-2"><i class="fas fa-exclamation-triangle text-warning"></i> Warnings:</strong>
                    <span class="badge badge-warning mr-2">
                        Unassigned: {{ $warnings['unassigned_projects'] }}
                        <span 
                            class="ml-1" 
                            data-toggle="tooltip" 
                            title="This count reflects properties that are not assigned to any consultant and have not been completed.">
                            <i class="fas fa-question-circle text-muted"></i>
                        </span>
                    </span>
                    <span class="badge badge-info mr-2">
                        Unscheduled: {{ $warnings['unscheduled_projects'] }}
                        <span 
                            class="ml-1" 
                            data-toggle="tooltip" 
                            title="This count reflects properties that have not yet been scheduled for inspection.">
                            <i class="fas fa-question-circle text-muted"></i>
                        </span>
                    </span>
                    <span class="badge badge-danger">Late Reports: {{ $warnings['late_reports'] }}</span>
                </div>
                <div>
                    <strong class="mr-2"><i class="fas fa-briefcase text-primary"></i> Current Business:</strong>
                    <span class="badge badge-success mr-2">Active: {{ $business['active_projects'] }}</span>
                    <span class="badge badge-primary mr-2">Gross Billing: ${{ number_format($business['gross_billing_in_process'], 0) }}</span>
                    <span class="badge badge-secondary mr-2">Net Profit: ${{ number_format($business['net_profit_in_process'], 0) }}</span>
                    <span class="text-muted" style="font-size:0.95em;">
                        (Actual: ${{ number_format($business['actual_net_profit'], 0) }}, Est: ${{ number_format($business['estimated_net_profit'], 0) }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card with Tabs -->
    <div class="row">
">        <div class="col-12
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="hb837-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'active' ? 'active' : '' }}"
                               id="active-tab" data-toggle="tab" href="#active" role="tab"
                               aria-controls="active" aria-selected="{{ $tab == 'active' ? 'true' : 'false' }}"
                               onclick="changeTab('active')">
                                <i class="fas fa-project-diagram"></i> Active 
                                <span class="badge badge-{{ $tab == 'active' ? 'light' : 'info' }} ml-1">{{ $tabCounts['active'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'quoted' ? 'active' : '' }}"
                               id="quoted-tab" data-toggle="tab" href="#quoted" role="tab"
                               aria-controls="quoted" aria-selected="{{ $tab == 'quoted' ? 'true' : 'false' }}"
                               onclick="changeTab('quoted')">
                                <i class="fas fa-file-invoice-dollar"></i> Quoted 
                                <span class="badge badge-{{ $tab == 'quoted' ? 'light' : 'warning' }} ml-1">{{ $tabCounts['quoted'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'completed' ? 'active' : '' }}"
                               id="completed-tab" data-toggle="tab" href="#completed" role="tab"
                               aria-controls="completed" aria-selected="{{ $tab == 'completed' ? 'true' : 'false' }}"
                               onclick="changeTab('completed')">
                                <i class="fas fa-check-circle"></i> Completed 
                                <span class="badge badge-{{ $tab == 'completed' ? 'light' : 'success' }} ml-1">{{ $tabCounts['completed'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'closed' ? 'active' : '' }}"
                               id="closed-tab" data-toggle="tab" href="#closed" role="tab"
                               aria-controls="closed" aria-selected="{{ $tab == 'closed' ? 'true' : 'false' }}"
                               onclick="changeTab('closed')">
                                <i class="fas fa-times-circle"></i> Closed 
                                <span class="badge badge-{{ $tab == 'closed' ? 'light' : 'danger' }} ml-1">{{ $tabCounts['closed'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'all' ? 'active' : '' }}"
                               id="all-tab" data-toggle="tab" href="#all" role="tab"
                               aria-controls="all" aria-selected="{{ $tab == 'all' ? 'true' : 'false' }}"
                               onclick="changeTab('all')">
                                <i class="fas fa-list"></i> All 
                                <span class="badge badge-{{ $tab == 'all' ? 'light' : 'secondary' }} ml-1">{{ $tabCounts['all'] }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="hb837-tabContent">
                        <div class="tab-pane fade {{ $tab == 'all' ? 'show active' : '' }}" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <!-- DataTable -->
                            <div class="table-responsive" id="table-container">
                                <table id="hb837-table-all" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%" class="text-center">
                                                <input type="checkbox" id="select-all-all" class="form-check-input">
                                            </th>
                                            <th width="20%" class="text-left">Property Name</th>
                                            <th width="12%" class="text-center">County</th>
                                            <th width="15%" class="text-left">Macro Client</th>
                                            <th width="15%" class="text-center">Assigned Consultant</th>
                                            <th width="15%" class="text-center">Scheduled Date Of Inspection</th>
                                            <th width="12%" class="text-center">Report Status</th>
                                            <th width="8%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $tab == 'active' ? 'show active' : '' }}" id="active" role="tabpanel" aria-labelledby="active-tab">
                            <!-- DataTable -->
                            <div class="table-responsive" id="table-container">
                                <table id="hb837-table-active" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%" class="text-center">
                                                <input type="checkbox" id="select-all-active" class="form-check-input">
                                            </th>
                                            <th width="18%" class="text-left">Property Name</th>
                                            <th width="10%" class="text-center">Type/Unit No</th>
                                            <th width="12%" class="text-center">SecurityGauge Crime Risk</th>
                                            <th width="14%" class="text-left">Macro Client</th>
                                            <th width="14%" class="text-center">Assigned Consultant</th>
                                            <th width="14%" class="text-center">Scheduled Date Of Inspection</th>
                                            <th width="10%" class="text-center">Report Status</th>
                                            <th width="5%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $tab == 'quoted' ? 'show active' : '' }}" id="quoted" role="tabpanel" aria-labelledby="quoted-tab">
                            <!-- Same table structure for quoted -->
                            <div class="table-responsive">
                                <table id="hb837-table-quoted" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%" class="text-center">
                                                <input type="checkbox" id="select-all-quoted" class="form-check-input">
                                            </th>
                                            <th width="25%" class="text-left">Property Name</th>
                                            <th width="12%" class="text-center">County</th>
                                            <th width="18%" class="text-left">Macro Client</th>
                                            <th width="15%" class="text-center">Contracting Status</th>
                                            <th width="12%" class="text-center">Agreement Submitted</th>
                                            <th width="10%" class="text-center">Quoted Price</th>
                                            <th width="5%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $tab == 'completed' ? 'show active' : '' }}" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                            <!-- Same table structure for completed -->
                            <div class="table-responsive">
                                <table id="hb837-table-completed" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%" class="text-center">
                                                <input type="checkbox" id="select-all-completed" class="form-check-input">
                                            </th>
                                            <th width="20%" class="text-left">Property Name</th>
                                            <th width="10%" class="text-center">County</th>
                                            <th width="12%" class="text-center">SecurityGauge Crime Risk</th>
                                            <th width="15%" class="text-left">Macro Client</th>
                                            <th width="12%" class="text-center">Billing Request Submitted</th>
                                            <th width="15%" class="text-center">Scheduled Date Of Inspection</th>
                                            <th width="8%" class="text-center">Days Until Renewal</th>
                                            <th width="5%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $tab == 'closed' ? 'show active' : '' }}" id="closed" role="tabpanel" aria-labelledby="closed-tab">
                            <!-- Same table structure for closed -->
                            <div class="table-responsive">
                                <table id="hb837-table-closed" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%" class="text-center">
                                                <input type="checkbox" id="select-all-closed" class="form-check-input">
                                            </th>
                                            <th width="20%" class="text-left">Property Name</th>
                                            <th width="12%" class="text-center">County</th>
                                            <th width="15%" class="text-left">Macro Client</th>
                                            <th width="15%" class="text-center">Assigned Consultant</th>
                                            <th width="15%" class="text-center">Scheduled Date Of Inspection</th>
                                            <th width="12%" class="text-center">Report Status</th>
                                            <th width="8%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
@include('admin.hb837.partials.modals.bulk-actions')

<!-- Property Location Modal -->
@include('admin.hb837.partials.modals.property-location')
@stop

@section('css')
<style>
/* Color coding for Crime Risk levels */
.risk-low {
    background-color: #72b862 !important;
    color: white !important;
}

.risk-moderate {
    background-color: #95f181 !important;
    color: black !important;
}

.risk-elevated {
    background-color: #fae099 !important;
    color: black !important;
}

.risk-high {
    background-color: #f2a36e !important;
    color: black !important;
}

.risk-severe {
    background-color: #c75845 !important;
    color: white !important;
}

/* Color coding for Report Status */
.status-not-started {
    background-color: #f8d7da !important;
    color: #721c24 !important;
}

.status-in-progress {
    background-color: #fff3cd !important;
    color: #856404 !important;
}

.status-in-review {
    background-color: #cce5ff !important;
    color: #004085 !important;
}

.status-completed {
    background-color: #d4edda !important;
    color: #155724 !important;
}

/* Additional status colors for completeness */
.status-quoted {
    background-color: #e2e3e5 !important;
    color: #383d41 !important;
}

.status-active {
    background-color: #b3d9ff !important;
    color: #004085 !important;
}

.status-closed {
    background-color: #f5c6cb !important;
    color: #721c24 !important;
}

/* Contract Status colors */
.contract-executed {
    background-color: #d4edda !important;
    color: #155724 !important;
}

.contract-pending {
    background-color: #fff3cd !important;
    color: #856404 !important;
}

.contract-cancelled {
    background-color: #f8d7da !important;
    color: #721c24 !important;
}

/* Priority colors */
.priority-low {
    background-color: #d4edda !important;
    color: #155724 !important;
}

.priority-medium {
    background-color: #fff3cd !important;
    color: #856404 !important;
}

.priority-high {
    background-color: #f8d7da !important;
    color: #721c24 !important;
}

.priority-urgent {
    background-color: #c75845 !important;
    color: white !important;
}

/* Late Reports Styling */
.text-danger.font-weight-bold {
    color: #dc3545 !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(220, 53, 69, 0.3);
}

/* DataTables custom styling */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

/* Tab styling */
.nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    margin-bottom: -2px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #f8f9fa;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #fff;
    font-weight: 600;
}

.nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #007bff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

/* Tab content styling */
.tab-content {
    padding-top: 1rem;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

.tab-pane.show.active {
    display: block;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Bulk action checkboxes */
.bulk-checkbox {
    transform: scale(1.2);
}

/* Empty State Styling */
.empty-state-container {
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    margin: 0;
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    box-sizing: border-box;
}

.empty-state-content {
    max-width: 450px;
    width: 100%;
}

.empty-state-icon {
    font-size: 3.5rem;
    margin-bottom: 1.25rem;
    opacity: 0.8;
    display: block;
}

.empty-state-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 0.875rem;
    color: #495057;
    line-height: 1.3;
}

.empty-state-message {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: #6c757d;
    line-height: 1.5;
}

.empty-state-action {
    font-size: 0.875rem;
    margin-bottom: 1.75rem;
    font-style: italic;
    color: #868e96;
}

.empty-state-buttons {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.empty-state-buttons .btn {
    margin: 0.25rem;
    min-width: 120px;
}

/* DataTables Processing Indicator */
.dt-processing-wrapper {
    text-align: center;
    padding: 40px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.dt-processing-wrapper i {
    animation: spin 1s linear infinite;
}

.dt-processing-wrapper strong {
    display: block;
    margin-top: 15px;
    color: #495057;
    font-size: 1.1rem;
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Custom DataTables Empty Table Styling - Complete Fix */
.dataTables_empty {
    padding: 0 !important;
    border: none !important;
    background: transparent !important;
    text-align: center !important;
    position: relative !important;
    height: 400px !important;
    vertical-align: middle !important;
}

/* Ensure the empty state spans full table width and is properly centered */
.dataTables_empty .empty-state-container {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 20px !important;
    box-sizing: border-box !important;
    border-radius: 0 !important;
    width: 100% !important;
    height: 100% !important;
}

/* Override DataTables default empty styling */
table.dataTable tbody tr.odd {
    background-color: transparent !important;
}

table.dataTable tbody tr.even {
    background-color: transparent !important;
}

table.dataTable tbody td.dataTables_empty {
    text-align: center !important;
    vertical-align: middle !important;
    padding: 0 !important;
    border: none !important;
    background: transparent !important;
}

/* Empty state row styling for manual injection */
.empty-state-row td {
    border: none !important;
    padding: 0 !important;
    background: transparent !important;
    vertical-align: middle !important;
}

.empty-state-row:hover td {
    background: transparent !important;
}

/* Ensure empty state is visible in table body */
#hb837-table tbody .empty-state-container {
    width: 100%;
    padding: 40px 20px;
    margin: 0;
}

/* Critical: Prevent all horizontal scrolling */
.table-responsive {
    overflow-x: hidden !important;
    overflow-y: visible !important;
    margin-bottom: 0;
    width: 100% !important;
    max-width: 100% !important;
}

.dataTables_wrapper {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
    box-sizing: border-box;
}

.dataTables_scrollBody {
    overflow-x: auto !important;
    overflow-y: visible !important;
    width: 100% !important;
    max-width: 100% !important;
}

/* Table layout for better column alignment */
#hb837-table {
    width: 100% !important;
    max-width: 100% !important;
    table-layout: fixed !important;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dee2e6;
    box-sizing: border-box;
    margin-bottom: 0;
}

/* DataTables wrapper styling */
.dataTables_wrapper {
    width: 100%;
    overflow-x: auto;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin-top: 1rem;
}

/* Disable horizontal scrolling */
.dataTables_scrollBody {
    overflow-x: hidden !important;
}

/* Header alignment fixes */
#hb837-table thead th {
    text-align: center;
    vertical-align: middle;
    padding: 10px 6px;
    white-space: nowrap;
    font-size: 0.8rem;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    border-bottom: 2px solid #dee2e6;
}

#hb837-table thead th:first-child {
    text-align: center;
    width: 3%;
    min-width: 35px;
}

#hb837-table thead th:nth-child(2) {
    width: 20%;
    text-align: left;
    padding-left: 12px;
}

#hb837-table thead th:nth-child(3) { width: 8%; }  /* County */
#hb837-table thead th:nth-child(4) { width: 9%; } /* Crime Risk */
#hb837-table thead th:nth-child(5) {
    width: 11%;
    text-align: left;
    padding-left: 12px;
} /* Client Contact */
#hb837-table thead th:nth-child(6) { width: 9%; } /* Consultant */
#hb837-table thead th:nth-child(7) { width: 8%; }  /* Inspection Date */
#hb837-table thead th:nth-child(8) { width: 8%; }  /* Report Status */
#hb837-table thead th:nth-child(9) { width: 8%; }  /* Contract Status */
#hb837-table thead th:nth-child(10) { width: 6%; } /* Quote */
#hb837-table thead th:nth-child(11) { width: 5%; } /* Priority */
#hb837-table thead th:nth-child(12) { width: 6%; } /* Created */
#hb837-table thead th:nth-child(13) { width: 9%; } /* Actions */

/* Column-specific alignment */
#hb837-table tbody td {
    vertical-align: middle;
    padding: 8px 6px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

#hb837-table tbody td:first-child {
    text-align: center;
}

#hb837-table tbody td:nth-child(2) { /* Property Details */
    white-space: normal;
    word-wrap: break-word;
    text-align: left;
    padding-left: 12px;
}

#hb837-table tbody td:nth-child(5) { /* Client Contact */
    text-align: left;
    padding-left: 12px;
}

/* Center align numeric and status columns */
#hb837-table tbody td:nth-child(3),  /* County */
#hb837-table tbody td:nth-child(4),  /* Crime Risk */
#hb837-table tbody td:nth-child(6),  /* Consultant */
#hb837-table tbody td:nth-child(7),  /* Inspection Date */
#hb837-table tbody td:nth-child(8),  /* Report Status */
#hb837-table tbody td:nth-child(9),  /* Contract Status */
#hb837-table tbody td:nth-child(10), /* Quote */
#hb837-table tbody td:nth-child(11), /* Priority */
#hb837-table tbody td:nth-child(12), /* Created */
#hb837-table tbody td:nth-child(13) /* Actions */ {
    text-align: center;
}

/* Search Empty State Styling */
.search-empty-state-container {
    text-align: center;
    padding: 40px 20px;
    background: #f8f9fa;
    border-radius: 6px;
    margin: 15px 0;
}

.search-empty-state-content {
    max-width: 350px;
    margin: 0 auto;
}

.search-empty-state-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.search-empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #495057;
}

.search-empty-state-message {
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    color: #6c757d;
    line-height: 1.4;
}

/* Table styling enhancements */
#hb837-table {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dee2e6;
}

#hb837-table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

#hb837-table tbody tr {
    transition: all 0.2s ease;
}

#hb837-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05) !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Loading state styling */
.dt-processing {
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    background: rgba(255, 255, 255, 0.95) !important;
    border: none !important;
    border-radius: 8px !important;
    z-index: 1001 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
}

/* Responsive empty state */
@media (max-width: 768px) {
    .empty-state-container {
        padding: 30px 15px !important;
        min-height: 220px !important;
    }

    .empty-state-icon {
        font-size: 2.75rem;
    }

    .empty-state-title {
        font-size: 1.2rem;
    }

    .empty-state-message {
        font-size: 0.85rem;
    }

    .empty-state-buttons {
        flex-direction: column;
        align-items: center;
    }

    .empty-state-buttons .btn {
        width: 80%;
        margin: 0.25rem 0;
        min-width: auto;
    }

    /* Table responsive adjustments */
    #hb837-table thead th {
        padding: 8px 4px;
        font-size: 0.8rem;
    }

    #hb837-table tbody td {
        padding: 8px 4px;
        font-size: 0.85rem;
    }

    /* Hide less important columns on mobile */
    #hb837-table thead th:nth-child(3), /* County */
    #hb837-table tbody td:nth-child(3),
    #hb837-table thead th:nth-child(5), /* Client Contact */
    #hb837-table tbody td:nth-child(5),
    #hb837-table thead th:nth-child(11), /* Priority */
    #hb837-table tbody td:nth-child(11),
    #hb837-table thead th:nth-child(12), /* Created */
    #hb837-table tbody td:nth-child(12) {
        display: none;
    }
}

@media (max-width: 576px) {
    .empty-state-container {
        padding: 25px 10px;
        min-height: 200px;
    }

    .empty-state-icon {
        font-size: 2.5rem;
    }

    .empty-state-title {
        font-size: 1.1rem;
    }

    /* Hide even more columns on very small screens */
    #hb837-table thead th:nth-child(6), /* Consultant */
    #hb837-table tbody td:nth-child(6),
    #hb837-table thead th:nth-child(10), /* Quote */
    #hb837-table tbody td:nth-child(10) {
        display: none;
    }
}

/* Property Location Modal Styling */
.modal-xl {
    max-width: 1200px;
}

#propertyLocationModal .modal-body {
    padding: 1.5rem;
}

#propertyLocationModal .card {
    border: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#propertyLocationModal .card-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

#propertyLocationModal .card-text {
    color: #6c757d;
    margin-bottom: 0;
}

#propertyMap {
    transition: all 0.3s ease;
}

#mapLoadingIndicator,
#mapErrorIndicator {
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    color: #6c757d;
}

#mapLoadingIndicator .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Google Maps styling */
.gm-style-iw {
    border-radius: 8px;
}

.gm-style-iw-d {
    overflow: hidden !important;
}

/* Modal responsiveness */
@media (max-width: 992px) {
    .modal-xl {
        max-width: 90%;
    }
    
    #propertyMap {
        height: 350px !important;
    }
}

@media (max-width: 576px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    #propertyMap {
        height: 300px !important;
    }
    
    #propertyLocationModal .modal-body {
        padding: 1rem;
    }
}
</style>
@stop

@section('js')
<script>
// Global variables for table management
var table; // Global DataTable instance
var currentTab = '{{ $tab }}'; // Current active tab

// Wait for AdminLTE and DataTables to be fully loaded
$(document).ready(function() {
    console.log('Document ready - Starting DataTables initialization...');

    // Check if required libraries are loaded with more detailed logging
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        alert('jQuery library is missing. Please refresh the page.');
        return;
    }
    console.log('✓ jQuery loaded successfully');

    // Wait a bit for AdminLTE plugins to load
    setTimeout(function() {
        console.log('Checking DataTables availability...');

        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables is not loaded');
            console.log('Attempting to load DataTables manually...');

            // Try to load DataTables manually
            $('<script>')
                .attr('src', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js')
                .on('load', function() {
                    console.log('DataTables core loaded manually');
                    $('<script>')
                        .attr('src', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js')
                        .on('load', function() {
                            console.log('DataTables Bootstrap loaded manually');
                            initializeDataTables();
                        })
                        .appendTo('head');
                })
                .on('error', function() {
                    console.error('Failed to load DataTables manually');
                    showFallbackTable();
                })
                .appendTo('head');

            // Load CSS
            $('<link>')
                .attr('rel', 'stylesheet')
                .attr('href', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css')
                .appendTo('head');
        } else {
            console.log('✓ DataTables loaded successfully');
            initializeDataTables();
        }
    }, 500); // Give AdminLTE time to load plugins

    function initializeDataTables() {
        console.log('Initializing DataTables...');

        // Test AJAX connectivity first
        console.log('Testing AJAX connectivity for tab:', currentTab);
        $.ajax({
            url: '/admin/hb837/data/' + currentTab,
            method: 'GET',
            data: {
                draw: 1,
                start: 0,
                length: 10
            },
            success: function(response) {
                console.log('✓ AJAX connectivity test successful:', response);
                console.log('Response structure:', {
                    draw: response.draw,
                    recordsTotal: response.recordsTotal,
                    recordsFiltered: response.recordsFiltered,
                    dataLength: response.data ? response.data.length : 'no data'
                });
                
                // Now initialize the actual DataTables
                initializeDataTablesAfterConnectivityTest();
            },
            error: function(xhr, status, error) {
                console.error('✗ AJAX connectivity test failed:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                // Try to show a helpful error message
                if (xhr.status === 404) {
                    alert('Error: The data endpoint was not found (404). Please check the route configuration.');
                } else if (xhr.status === 500) {
                    alert('Error: Server error (500). Please check the server logs.');
                } else {
                    alert('Error: Failed to connect to data endpoint. Status: ' + xhr.status);
                }
                
                // Still try to initialize DataTables in case it's a temporary issue
                initializeDataTablesAfterConnectivityTest();
            }
        });
    }

    function initializeDataTablesAfterConnectivityTest() {
        console.log('Proceeding with DataTables initialization...');

        // Initialize tabs first
        initializeTabs();

        // Initialize DataTable using global variables
        let tableId = '#hb837-table-' + currentTab;
        table = initDataTable(currentTab, tableId); // Use global table variable

        if (table) {
            console.log('✓ DataTable initialized successfully for tab:', currentTab);
        } else {
            console.error('✗ Failed to initialize DataTable');
            showFallbackTable();
        }
    }

    function showFallbackTable() {
        console.log('Showing fallback table without DataTables');
        $('#hb837-table tbody').html(`
            <tr>
                <td colspan="13" class="text-center p-4">
                    <div class="alert alert-warning">
                        <h5 class="text-shadow-sm"><i class="fas fa-exclamation-triangle"></i> DataTables Loading Issue</h5>
                        <p>The advanced table features are temporarily unavailable.</p>
                        <button onclick="location.reload()" class="btn btn-primary btn-sm">
                            <i class="fas fa-refresh"></i> Refresh Page
                        </button>
                    </div>
                </td>
            </tr>
        `);
    }

    // Get empty state HTML based on tab
    function getEmptyStateHTML(tab) {
        const emptyStates = {
            'active': {
                icon: 'fas fa-project-diagram',
                title: 'No Active Properties',
                message: 'There are no active properties in the system at the moment.',
                action: 'Properties become active when they have been quoted and the contract is executed.',
                color: 'info'
            },
            'quoted': {
                icon: 'fas fa-file-invoice-dollar',
                title: 'No Quoted Properties',
                message: 'There are no properties currently in the quoted stage.',
                action: 'Properties appear here when quotes are sent to clients or work has started.',
                color: 'warning'
            },
            'completed': {
                icon: 'fas fa-check-circle',
                title: 'No Completed Properties',
                message: 'No properties have been completed yet.',
                action: 'Properties appear here when reports are submitted and marked as completed.',
                color: 'success'
            },
            'closed': {
                icon: 'fas fa-times-circle',
                title: 'No Closed Properties',
                message: 'There are no closed properties in the system.',
                action: 'Properties appear here when contracts are closed or cancelled.',
                color: 'danger'
            }
        };

        const state = emptyStates[tab] || emptyStates['active'];

        return `
            <div class="empty-state-container">
                <div class="empty-state-content">
                    <div class="empty-state-icon text-${state.color}">
                        <i class="${state.icon}"></i>
                    </div>
                    <h4 class="empty-state-title text-shadow-sm">${state.title}</h4>
                    <p class="empty-state-message">${state.message}</p>
                    <p class="empty-state-action text-muted">${state.action}</p>
                    <div class="empty-state-buttons">
                        <a href="{{ route('admin.hb837.create') }}" class="btn btn-${state.color} btn-sm">
                            <i class="fas fa-plus"></i> Add New Property
                        </a>
                        <a href="{{ route('admin.hb837.smart-import.show') }}" class="btn btn-outline-${state.color} btn-sm">
                            <i class="fas fa-magic"></i> Smart Import
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    // Get search empty state HTML
    function getSearchEmptyStateHTML() {
        return `
            <div class="search-empty-state-container">
                <div class="search-empty-state-content">
                    <div class="search-empty-state-icon text-secondary">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="search-empty-state-title text-shadow-sm">No Matching Results</h5>
                    <p class="search-empty-state-message">
                        No properties match your search criteria. Try adjusting your search terms or clearing the search to see all properties.
                    </p>
                    <button onclick="if(typeof table !== 'undefined' && table) { table.search('').draw(); }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear Search
                    </button>
                </div>
            </div>
        `;
    }

    // Get tab-specific column configurations
    function getTabColumns(tab) {
        const baseConfig = {
            checkbox: { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, width: '3%', className: 'text-center' },
            property_name: { data: 'property_name', name: 'property_name', className: 'text-left' },
            county: { data: 'county', name: 'county', className: 'text-center' },
            type_unit_type: { data: 'type_unit_type', name: 'type_unit_type', orderable: false, className: 'text-center' },
            securitygauge_crime_risk: { data: 'securitygauge_crime_risk', name: 'securitygauge_crime_risk', orderable: true, className: 'text-center' },
            macro_client: { data: 'macro_client', name: 'macro_client', className: 'text-left' },
            assigned_consultant_id: { data: 'assigned_consultant_id', name: 'assigned_consultant_id', orderable: true, className: 'text-center' },
            scheduled_date_of_inspection: { data: 'scheduled_date_of_inspection', name: 'scheduled_date_of_inspection', className: 'text-center' },
            report_status: { data: 'report_status', name: 'report_status', orderable: false, className: 'text-center' },
            contracting_status: { data: 'contracting_status', name: 'contracting_status', orderable: false, className: 'text-center' },
            agreement_submitted: { data: 'agreement_submitted', name: 'agreement_submitted', orderable: false, className: 'text-center' },
            quoted_price: { data: 'quoted_price', name: 'quoted_price', orderable: true, className: 'text-center' },
            billing_req_submitted: { data: 'billing_req_submitted', name: 'billing_req_submitted', orderable: false, className: 'text-center' },
            days_until_renewal: { data: 'days_until_renewal', name: 'days_until_renewal', orderable: false, className: 'text-center' },
            action: { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        };

        switch (tab) {
            case 'active':
                return {
                    columns: [
                        baseConfig.checkbox,
                        { ...baseConfig.property_name, width: '18%' },
                        { ...baseConfig.type_unit_type, width: '10%' },
                        { ...baseConfig.securitygauge_crime_risk, width: '12%' },
                        { ...baseConfig.macro_client, width: '14%' },
                        { ...baseConfig.assigned_consultant_id, width: '14%' },
                        { ...baseConfig.scheduled_date_of_inspection, width: '14%' },
                        { ...baseConfig.report_status, width: '10%' },
                        { ...baseConfig.action, width: '5%' }
                    ],
                    order: [[6, 'asc']], // Order by scheduled_date_of_inspection ascending (oldest to newest, including "Not Scheduled")
                    columnDefs: [
                        { targets: [0, 2, 7, 8], orderable: false },
                        { targets: '_all', className: 'align-middle' }
                    ]
                };

            case 'quoted':
                return {
                    columns: [
                        baseConfig.checkbox,
                        { ...baseConfig.property_name, width: '25%' },
                        { ...baseConfig.county, width: '12%' },
                        { ...baseConfig.macro_client, width: '18%' },
                        { ...baseConfig.contracting_status, width: '15%' },
                        { ...baseConfig.agreement_submitted, width: '12%' },
                        { ...baseConfig.quoted_price, width: '10%' },
                        { ...baseConfig.action, width: '5%' }
                    ],
                    order: [[6, 'desc']], // Order by quoted_price descending
                    columnDefs: [
                        { targets: [0, 4, 5, 7], orderable: false },
                        { targets: '_all', className: 'align-middle' }
                    ]
                };

            case 'completed':
                return {
                    columns: [
                        baseConfig.checkbox,
                        { ...baseConfig.property_name, width: '20%' },
                        { ...baseConfig.county, width: '10%' },
                        { ...baseConfig.securitygauge_crime_risk, width: '12%' },
                        { ...baseConfig.macro_client, width: '15%' },
                        { ...baseConfig.billing_req_submitted, width: '12%' },
                        { ...baseConfig.scheduled_date_of_inspection, width: '15%' },
                        { ...baseConfig.days_until_renewal, width: '8%' },
                        { ...baseConfig.action, width: '5%' }
                    ],
                    order: [[6, 'asc']], // Order by scheduled_date_of_inspection ascending (earliest first for renewal planning)
                    columnDefs: [
                        { targets: [0, 5, 7, 8], orderable: false },
                        { targets: '_all', className: 'align-middle' }
                    ]
                };

            case 'closed':
            case 'all':
            default:
                return {
                    columns: [
                        baseConfig.checkbox,
                        { ...baseConfig.property_name, width: '20%' },
                        { ...baseConfig.county, width: '12%' },
                        { ...baseConfig.macro_client, width: '15%' },
                        { ...baseConfig.assigned_consultant_id, width: '15%' },
                        { ...baseConfig.scheduled_date_of_inspection, width: '15%' },
                        { ...baseConfig.report_status, width: '12%' },
                        { ...baseConfig.action, width: '8%' }
                    ],
                    order: [[5, 'desc']], // Order by scheduled_date_of_inspection descending
                    columnDefs: [
                        { targets: [0, 6, 7], orderable: false },
                        { targets: '_all', className: 'align-middle' }
                    ]
                };
        }
    }

    function initDataTable(tab, tableId) {
        console.log('Initializing DataTable for tab:', tab, 'with tableId:', tableId);

        // Default to main table if no tableId provided (for backward compatibility)
        if (!tableId) {
            tableId = '#hb837-table';
        }

        // Check if DataTables is loaded
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables is not loaded. Please check the JavaScript includes.');
            alert('DataTables library is not loaded. Please refresh the page.');
            return null;
        }

        // Destroy existing DataTable for this specific table
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }

        // Get tab-specific column configuration
        const columnConfig = getTabColumns(tab);

        // Create the DataTable and store the reference
        var dataTable = $(tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/hb837/data/' + tab,
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    console.error('Status:', xhr.status);
                    
                    // Try to parse the error response
                    try {
                        var errorData = JSON.parse(xhr.responseText);
                        console.error('Parsed error:', errorData);
                    } catch (e) {
                        console.error('Could not parse error response');
                    }
                    
                    alert('Error loading data: ' + error + '. Please check the console and refresh the page.');
                },
                dataSrc: function(json) {
                    console.log('=== DataTables Response Debug for tab:', tab, '===');
                    console.log('Full JSON:', json);
                    
                    if (!json) {
                        console.error('No JSON response received');
                        return [];
                    }
                    
                    // Check if server returned an error
                    if (json.error) {
                        console.error('Server returned error:', json.error);
                        alert('Server Error: ' + json.error);
                        return [];
                    }
                    
                    console.log('Records total:', json.recordsTotal);
                    console.log('Records filtered:', json.recordsFiltered);
                    console.log('Data array length:', json.data ? json.data.length : 'undefined');

                    // Log first data item to see structure
                    if (json.data && json.data.length > 0) {
                        console.log('First data item:', json.data[0]);
                        console.log('First data item keys:', Object.keys(json.data[0]));
                        
                        // Check if consultant data is present
                        if (json.data[0].assigned_consultant_id !== undefined) {
                            console.log('Consultant data sample:', json.data[0].assigned_consultant_id);
                        }
                    }

                    return json.data || [];
                }
            },
            columns: columnConfig.columns,
            order: columnConfig.order,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: false,
            stateSave: true,
            scrollX: true,
            autoWidth: false,
            fixedColumns: false,
            columnDefs: columnConfig.columnDefs,
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                 '<"row"<"col-sm-12"<"table-responsive"t>>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            drawCallback: function(settings) {
                var api = this.api();
                var pageInfo = null;
                var data = null;
                var columnCount = columnConfig.columns.length; // Get actual column count

                try {
                    pageInfo = api.page.info();
                    data = api.rows().data();
                } catch (error) {
                    console.error('Error getting DataTables info:', error);
                    // Fallback: try to get data another way
                    try {
                        data = api.rows().data();
                        pageInfo = { recordsTotal: data.length || 0, recordsFiltered: data.length || 0 };
                    } catch (fallbackError) {
                        console.error('Fallback data retrieval failed:', fallbackError);
                        data = [];
                        pageInfo = { recordsTotal: 0, recordsFiltered: 0 };
                    }
                }

                console.log('DrawCallback - Tab:', tab, 'Page Info:', pageInfo, 'Actual Data Length:', data ? data.length : 0, 'Column Count:', columnCount);

                // Only show empty state if we truly have no data
                // Don't interfere with DataTables rendering if there is data
                if (data && data.length === 0 && pageInfo && pageInfo.recordsTotal === 0) {
                    var emptyStateHtml = getEmptyStateHTML(tab);
                    console.log('Showing empty state for tab:', tab);
                    $(this).find('tbody').html(
                        '<tr class="empty-state-row"><td colspan="' + columnCount + '" class="text-center p-0 border-0">' +
                        emptyStateHtml +
                        '</td></tr>'
                    );
                } else if (data && data.length > 0) {
                    console.log('Data exists, letting DataTables render normally. Rows:', data.length);

                    // Re-initialize tooltips
                    $('[data-toggle="tooltip"]').tooltip();

                    // Apply color coding to entire cells (as per GitHub issue #8)
                    setTimeout(function() {
                        applyCellColorCoding(tableId);
                    }, 100);

                    // Update bulk selection state
                    updateBulkCount();

                    // Show/hide bulk actions based on data
                    $('.bulk-actions-container').show();
                }

                // Add custom styling to empty table message only if it's the DataTables default empty message
                setTimeout(function() {
                    var $emptyCell = $(tableId + ' tbody tr td.dataTables_empty');
                    if ($emptyCell.length > 0) {
                        $emptyCell.css({
                            'border': 'none',
                            'background': 'transparent'
                        });
                    }
                }, 100);
            },
            initComplete: function(settings, json) {
                console.log('DataTable init complete for tab:', tab, 'Data:', json);
                // Force draw callback to run
                this.api().draw(false);
            },
            language: {
                processing: '<div class="dt-processing-wrapper"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><strong>Loading properties...</strong></div>',
                emptyTable: getEmptyStateHTML(tab),
                info: 'Showing _START_ to _END_ of _TOTAL_ properties',
                infoEmpty: 'No properties to display',
                infoFiltered: '(filtered from _MAX_ total properties)',
                lengthMenu: 'Show _MENU_ properties per page',
                search: 'Search properties:',
                zeroRecords: getEmptyStateHTML(tab),
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                }
            }
        });
        
        // Add debugging event handlers for sorting
        // Debug when sorting occurs
        dataTable.on('order.dt', function() {
            console.log('=== DataTable Sort Event ===');
            console.log('Tab:', tab);
            console.log('Current order:', dataTable.order());
            console.log('Table ID:', tableId);
        });

        // Debug when data is reloaded
        dataTable.on('xhr.dt', function(e, settings, json, xhr) {
            console.log('=== DataTable XHR Event ===');
            console.log('Tab:', tab);
            console.log('XHR Status:', xhr.status);
            console.log('Data received:', json ? json.data?.length : 'No data');
            
            if (xhr.status !== 200) {
                console.error('XHR Error - Status:', xhr.status);
                console.error('Response Text:', xhr.responseText);
            }
        });

        // Debug draw events
        dataTable.on('draw.dt', function() {
            console.log('=== DataTable Draw Event ===');
            console.log('Tab:', tab);
            console.log('Visible rows:', dataTable.rows({ page: 'current' }).data().length);
            console.log('Total rows:', dataTable.page.info().recordsTotal);
        });

        return dataTable;
    }

    // Initialize Bootstrap tabs properly
    function initializeTabs() {
        console.log('Initializing Bootstrap tabs...');

        // Handle tab click events
        $('#hb837-tabs a[data-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            var targetTab = $(this).attr('href').substring(1); // Remove # from href
            changeTab(targetTab);
        });

        // Handle tab shown event (after tab is displayed)
        $('#hb837-tabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href').substring(1);
            console.log('Tab shown:', target);

            // Trigger table resize for proper column sizing
            if (table && $.fn.DataTable.isDataTable(table.table().node())) {
                try {
                    table.columns.adjust();
                    // Only call responsive.recalc() if responsive is enabled
                    if (table.responsive) {
                        table.responsive.recalc();
                    }
                } catch (e) {
                    console.warn('Error adjusting table columns:', e);
                }
            }
        });

        // Activate current tab on page load using global currentTab
        $('#' + currentTab + '-tab').tab('show');

        // Update export button for initial tab
        updateExportButton(currentTab);

        console.log('Bootstrap tabs initialized successfully');
    }

    // Tab change function
    window.changeTab = function(tab) {
        console.log('Changing tab to:', tab);

        // Destroy existing DataTable if it exists
        if (table && $.fn.DataTable.isDataTable(table.table().node())) {
            console.log('Destroying existing DataTable');
            table.destroy();
            table = null;
        }

        // Update global currentTab variable
        currentTab = tab;

        // Update tab UI
        $('#hb837-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
        $('.tab-pane').removeClass('show active');
        $('#' + tab + '-tab').addClass('active').attr('aria-selected', 'true');
        $('#' + tab).addClass('show active');

        // Initialize DataTable for the new tab - use single table approach
        var tableId = '#hb837-table-' + tab;
        
        // Make sure the table exists before trying to initialize
        if ($(tableId).length > 0) {
            console.log('Found table:', tableId);
            table = initDataTable(tab, tableId);
            console.log('DataTable initialized for tab:', tab, 'Table exists:', table !== null);
        } else {
            console.error('Table not found:', tableId);
            console.log('Available tables:', $('table[id*="hb837-table"]').map(function() { return this.id; }).get());
        }

        // Update URL
        updateUrl(tab);

        // Update export button href for current tab
        updateExportButton(tab);
    };

    // Update URL when tab changes
    function updateUrl(tab) {
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    }

    // Update export button href when tab changes
    function updateExportButton(tab) {
        // Update button text to show current tab and count
        let tabDisplayName = tab.charAt(0).toUpperCase() + tab.slice(1);
        let tabCount = 0;
        
        // Get count from tab badge
        let tabBadge = $('#' + tab + '-tab .badge');
        if (tabBadge.length > 0) {
            tabCount = parseInt(tabBadge.text()) || 0;
        }
        
        // Update button text
        let exportBtn = $('#export-data-btn');
        let newText = `<i class="fas fa-file-download"></i> Export ${tabDisplayName} (${tabCount})`;
        exportBtn.html(newText);
        
        console.log('Export button updated for tab:', tab, 'with count:', tabCount);
    }

    // Update tab statistics dynamically
    function updateTabStatistics(tab) {
        $.ajax({
            url: '{{ route("admin.hb837.stats") }}',
            method: 'GET',
            success: function(stats) {
                $('.small-box .inner h3').each(function(index) {
                    var tabNames = ['active', 'quoted', 'completed', 'closed'];
                    $(this).text(stats[tabNames[index]] || 0);
                });
            },
            error: function() {
                console.log('Failed to update statistics');
            }
        });
    }

    // Initialize table state management
    $(document).ready(function() {
        // Auto-refresh every 5 minutes
        setInterval(function() {
            if (table) {
                table.ajax.reload(null, false); // false = don't reset pagination
            }
        }, 300000);

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Enhanced functions for record management
    window.deleteRecord = function(id) {
        if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("admin.hb837.index") }}/' + id + '/ajax',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Record deleted successfully!');
                        table.ajax.reload();
                        updateTabCounts(); // Update tab counts after deletion
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the record.');
                }
            });
        }
    };

    // Function to update tab counts dynamically
    window.updateTabCounts = function() {
        $.ajax({
            url: '{{ route("admin.hb837.stats") }}',
            method: 'GET',
            success: function(data) {
                // Update tab badges with new counts
                $('#all-tab .badge').text(data.tabCounts.all);
                $('#active-tab .badge').text(data.tabCounts.active);
                $('#quoted-tab .badge').text(data.tabCounts.quoted);
                $('#completed-tab .badge').text(data.tabCounts.completed);
                $('#closed-tab .badge').text(data.tabCounts.closed);
                
                console.log('Tab counts updated successfully');
            },
            error: function() {
                console.error('Failed to update tab counts');
            }
        });
    };

    // Auto-refresh tab counts every 30 seconds
    setInterval(updateTabCounts, 30000);

    // Select all checkbox
    $('#select-all').on('click', function() {
        $('.bulk-checkbox').prop('checked', this.checked);
        updateBulkCount();
    });

    // Individual checkbox change
    $(document).on('change', '.bulk-checkbox', function() {
        updateBulkCount();

        // Update select-all state
        let totalCheckboxes = $('.bulk-checkbox').length;
        let checkedCheckboxes = $('.bulk-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Update bulk count
    function updateBulkCount() {
        let count = $('.bulk-checkbox:checked').length;
        $('#selected-count').text(count + ' record(s) selected');
    }

    // Get current active tab
    function getCurrentTab() {
        return currentTab; // Return the dynamically updated client-side variable
    }

    // Bulk actions modal
    window.bulkActions = function() {
        let selectedCount = $('.bulk-checkbox:checked').length;
        if (selectedCount === 0) {
            alert('Please select at least one record.');
            return;
        }
        $('#bulkActionsModal').modal('show');
    };

    // Generate bulk PDF report
    window.generateBulkPdfReport = function() {
        // Get current filter parameters
        let currentTab = getCurrentTab();
        let searchValue = $('#hb837-table_filter input').val();
        
        // Create form to submit filters for PDF generation
        let form = $('<form>', {
            'method': 'GET',
            'action': '{{ route("admin.hb837.export.format", "pdf") }}',
            'target': '_blank'
        });
        
        // Add current filters
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'tab',
            'value': currentTab
        }));
        
        if (searchValue) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'search',
                'value': searchValue
            }));
        }
        
        // Add filter type
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'format',
            'value': 'pdf'
        }));
        
        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Show feedback message
        console.log('PDF report generation started...');
    };

    // Export current tab data
    window.exportCurrentTab = function() {
        let currentTab = getCurrentTab();
        
        // Get the current tab count from the badge
        let tabCount = 0;
        let tabBadge = $('#' + currentTab + '-tab .badge');
        if (tabBadge.length > 0) {
            tabCount = parseInt(tabBadge.text()) || 0;
        }
        
        // Get total records info from DataTable if available
        let totalRecords = 0;
        if (table && table.page && table.page.info) {
            try {
                let pageInfo = table.page.info();
                totalRecords = pageInfo.recordsTotal || 0;
            } catch (e) {
                // Fallback to badge count
                totalRecords = tabCount;
            }
        } else {
            totalRecords = tabCount;
        }
        
        // Show confirmation with count
        let tabDisplayName = currentTab.charAt(0).toUpperCase() + currentTab.slice(1);
        let confirmMessage = `Export ${totalRecords} ${tabDisplayName} properties to Excel?\n\nThis will download all ${totalRecords} records from the ${tabDisplayName} tab.`;
        
        if (confirm(confirmMessage)) {
            let exportUrl = '{{ route("admin.hb837.export", ["tab" => "__TAB__"]) }}';
            exportUrl = exportUrl.replace('__TAB__', currentTab);
            
            console.log('Exporting data for tab:', currentTab, 'with', totalRecords, 'records');
            
            // Show loading feedback
            let originalBtn = $('#export-data-btn');
            let originalText = originalBtn.html();
            originalBtn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...');
            originalBtn.prop('disabled', true);
            
            // Reset button after a delay
            setTimeout(function() {
                originalBtn.html(originalText);
                originalBtn.prop('disabled', false);
                
                // Show success notification if available
                if (typeof toastr !== 'undefined') {
                    toastr.success(`Export of ${totalRecords} ${tabDisplayName} properties initiated!`, 'Export Started');
                }
            }, 3000);
            
            window.open(exportUrl, '_blank');
        }
    };

    // Bulk action type change
    $('#bulk-action').on('change', function() {
        let action = $(this).val();
        $('.form-group[id$="-group"]').hide();

        if (action === 'status_update') {
            $('#status-group').show();
        } else if (action === 'consultant_assign') {
            $('#consultant-group').show();
            loadConsultants();
        }
    });

    // Load consultants for bulk assignment
    function loadConsultants() {
        // TODO: Add AJAX call to load consultants
        // For now, adding placeholder
        $('#bulk-consultant').html('<option value="">Unassigned</option><option value="1">Sample Consultant</option>');
    }

    // Bulk actions form submit
    $('#bulk-actions-form').on('submit', function(e) {
        e.preventDefault();

        let selectedIds = $('.bulk-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        let formData = $(this).serialize();
        formData += '&selected_ids=' + selectedIds.join(',');

        $.ajax({
            url: '{{ route("admin.hb837.bulk-action") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    table.ajax.reload();
                    $('#bulkActionsModal').modal('hide');
                    $('.bulk-checkbox').prop('checked', false);
                    $('#select-all').prop('checked', false);
                    updateBulkCount();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing the bulk action.');
            }
        });
    });

    // Apply color coding to entire table cells as specified in GitHub issue #8
    function applyCellColorCoding(tableId) {
        $(tableId + ' tbody tr').each(function() {
            const $row = $(this);

            // Apply crime risk color to entire cell (Column 4 - Crime Risk)
            const $riskCell = $row.find('td:nth-child(4)');
            const $riskSpan = $riskCell.find('span');
            if ($riskSpan.length > 0) {
                const riskClasses = $riskSpan.attr('class');
                if (riskClasses) {
                    const riskColorClass = riskClasses.split(' ').find(cls => cls.startsWith('risk-'));
                    if (riskColorClass) {
                        $riskCell.addClass(riskColorClass);
                        $riskSpan.removeClass(riskColorClass); // Remove from span to avoid double styling
                    }
                }
            }

            // Apply report status color to entire cell (Column 8 - Report Status)
            const $statusCell = $row.find('td:nth-child(8)');
            const $statusSpan = $statusCell.find('span');
            if ($statusSpan.length > 0) {
                const statusClasses = $statusSpan.attr('class');
                if (statusClasses) {
                    const statusColorClass = statusClasses.split(' ').find(cls => cls.startsWith('status-'));
                    if (statusColorClass) {
                        $statusCell.addClass(statusColorClass);
                        $statusSpan.removeClass(statusColorClass); // Remove from span to avoid double styling
                    }
                }
            }

            // Apply contract status color to entire cell (Column 9 - Contract Status)
            const $contractCell = $row.find('td:nth-child(9)');
            const $contractSpan = $contractCell.find('span');
            if ($contractSpan.length > 0) {
                const contractClasses = $contractSpan.attr('class');
                if (contractClasses) {
                    const contractColorClass = contractClasses.split(' ').find(cls => cls.startsWith('contract-'));
                    if (contractColorClass) {
                        $contractCell.addClass(contractColorClass);
                        $contractSpan.removeClass(contractColorClass); // Remove from span to avoid double styling
                    }
                }
            }

            // Apply priority color to entire cell (Column 11 - Priority)
            const $priorityCell = $row.find('td:nth-child(11)');
            const $prioritySpan = $priorityCell.find('span');
            if ($prioritySpan.length > 0) {
                const priorityClasses = $prioritySpan.attr('class');
                if (priorityClasses) {
                    const priorityColorClass = priorityClasses.split(' ').find(cls => cls.startsWith('priority-'));
                    if (priorityColorClass) {
                        $priorityCell.addClass(priorityColorClass);
                        $prioritySpan.removeClass(priorityColorClass); // Remove from span to avoid double styling
                    }
                }
            }
        });
    }

    // Helper functions for color coding as per GitHub issue #8
    function getRiskClass(riskLevel) {
        if (!riskLevel) return '';

        const risk = riskLevel.toLowerCase().trim();
        switch (risk) {
            case 'low':
                return 'risk-low';
            case 'moderate':
                return 'risk-moderate';
            case 'elevated':
                return 'risk-elevated';
            case 'high':
                return 'risk-high';
            case 'severe':
                return 'risk-severe';
            default:
                return '';
        }
    }

    // Report Status color class
    function getStatusClass(status) {
        if (!status) return '';

        const statusLower = status.toLowerCase().replace(/[\s-_]/g, '-');
        switch (statusLower) {
            case 'not-started':
            case 'notstarted':
                return 'status-not-started';
            case 'in-progress':
            case 'underway':
                return 'status-in-progress';
            case 'in-review':
            case 'inreview':
                return 'status-in-review';
            case 'completed':
                return 'status-completed';
            default:
                return '';
        }
    }

    // Contracting Status color class
    function getContractStatusClass(contractStatus) {
        if (!contractStatus) return '';

        const contract = contractStatus.toLowerCase().replace(/[\s-_]/g, '-');
        switch (contract) {
            case 'quoted':
                return 'status-quoted';
            case 'active':
                return 'status-active';
            case 'closed':
                return 'status-closed';
            default:
                return '';
        }
    }

    function getContractClass(contractStatus) {
        if (!contractStatus) return '';

        const contract = contractStatus.toLowerCase().replace(/[\s-_]/g, '-');
        switch (contract) {
            case 'executed':
                return 'contract-executed';
            case 'pending':
                return 'contract-pending';
            case 'cancelled':
            case 'canceled':
                return 'contract-cancelled';
            default:
                return '';
        }
    }

    function getPriorityClass(priority) {
        if (!priority) return '';

        const priorityLower = priority.toLowerCase().trim();
        switch (priorityLower) {
            case 'low':
                return 'priority-low';
            case 'medium':
            case 'normal':
                return 'priority-medium';
            case 'high':
                return 'priority-high';
            case 'urgent':
            case 'critical':
                return 'priority-urgent';
            default:
                return '';
        }
    }

    // Global variables for property location modal
    let currentPropertyData = {};
    let map;
    let marker;

    // Function to view property location in modal with Google Maps
    window.viewPropertyLocation = function(id, propertyName, address, city, state) {
        console.log('Opening location modal for property:', propertyName);
        
        // Store current property data
        currentPropertyData = {
            id: id,
            name: propertyName,
            address: address,
            city: city,
            state: state,
            fullAddress: address + ', ' + city + ', ' + state
        };
        
        // Update modal content
        $('#modal-property-name').text(propertyName);
        $('#modal-property-address').text(currentPropertyData.fullAddress);
        
        // Show modal
        $('#propertyLocationModal').modal('show');
        
        // Initialize map when modal is shown
        $('#propertyLocationModal').on('shown.bs.modal', function() {
            initializePropertyMap();
        });
    };

    // Initialize Google Maps
    function initializePropertyMap() {
        console.log('Initializing map for address:', currentPropertyData.fullAddress);
        
        // Show loading indicator
        $('#mapLoadingIndicator').show();
        $('#mapErrorIndicator').hide();
        $('#propertyMap').hide();
        
        // Check if Google Maps API is loaded
        if (typeof google === 'undefined') {
            console.error('Google Maps API not loaded');
            showMapError();
            return;
        }
        
        // Geocode the address
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: currentPropertyData.fullAddress }, function(results, status) {
            if (status === 'OK') {
                console.log('Geocoding successful:', results[0]);
                
                // Hide loading indicator
                $('#mapLoadingIndicator').hide();
                $('#propertyMap').show();
                
                // Get coordinates
                const location = results[0].geometry.location;
                
                // Initialize map
                map = new google.maps.Map(document.getElementById('propertyMap'), {
                    zoom: 16,
                    center: location,
                    mapTypeId: google.maps.MapTypeId.SATELLITE,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                    zoomControl: true
                });
                
                // Add marker
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: currentPropertyData.name,
                    animation: google.maps.Animation.DROP
                });
                
                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="max-width: 250px;">
                            <h6 class="mb-2"><strong>${currentPropertyData.name}</strong></h6>
                            <p class="mb-1"><i class="fas fa-map-marker-alt text-danger"></i> ${currentPropertyData.fullAddress}</p>
                            <small class="text-muted">Security assessment location</small>
                        </div>
                    `
                });
                
                // Show info window on marker click
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });
                
                // Open info window by default
                infoWindow.open(map, marker);
                
            } else {
                console.error('Geocoding failed:', status);
                showMapError();
            }
        });
    }
    
    // Show map error
    function showMapError() {
        $('#mapLoadingIndicator').hide();
        $('#propertyMap').hide();
        $('#mapErrorIndicator').show();
    }
    
    // Open in Google Maps
    window.openInGoogleMaps = function() {
        if (currentPropertyData.fullAddress) {
            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(currentPropertyData.fullAddress)}`;
            window.open(url, '_blank');
        }
    };
    
    // Clean up map when modal is hidden
    $('#propertyLocationModal').on('hidden.bs.modal', function() {
        if (map) {
            map = null;
            marker = null;
        }
        $('#mapLoadingIndicator').hide();
        $('#mapErrorIndicator').hide();
        $('#propertyMap').show();
    });

});
</script>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY_HERE') }}&libraries=geometry,places"></script>

@stop

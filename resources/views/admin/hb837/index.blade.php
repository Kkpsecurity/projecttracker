@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'HB837 Management - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-shield-alt"></i> HB837 Management</h1>
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
    <!-- Statistics Cards (Updated: {{ now() }}) -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>Active Projects</p>
                    <!-- Debug: Active={{ $stats['active'] }}, Total={{ $stats['total'] }} -->
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="#" onclick="showTab('active')" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['quoted'] }}</h3>
                    <p>Quoted Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="#" onclick="showTab('quoted')" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['completed'] }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" onclick="showTab('completed')" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['closed'] }}</h3>
                    <p>Closed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <a href="#" onclick="showTab('closed')" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
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
                    <a href="{{ route('admin.hb837.export') }}" class="btn btn-info mr-2">
                        <i class="fas fa-file-download"></i> Export Data
                    </a>
                    <button type="button" class="btn btn-warning mr-2 bulk-actions-container" onclick="bulkActions()">
                        <i class="fas fa-edit"></i> Bulk Actions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card with Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="hb837-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'all' ? 'active' : '' }}"
                               id="all-tab" data-toggle="tab" href="#all" role="tab"
                               aria-controls="all" aria-selected="{{ $tab == 'all' ? 'true' : 'false' }}"
                               onclick="changeTab('all')">
                                <i class="fas fa-list"></i> All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'active' ? 'active' : '' }}"
                               id="active-tab" data-toggle="tab" href="#active" role="tab"
                               aria-controls="active" aria-selected="{{ $tab == 'active' ? 'true' : 'false' }}"
                               onclick="changeTab('active')">
                                <i class="fas fa-project-diagram"></i> Active
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'quoted' ? 'active' : '' }}"
                               id="quoted-tab" data-toggle="tab" href="#quoted" role="tab"
                               aria-controls="quoted" aria-selected="{{ $tab == 'quoted' ? 'true' : 'false' }}"
                               onclick="changeTab('quoted')">
                                <i class="fas fa-file-invoice-dollar"></i> Quoted
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'completed' ? 'active' : '' }}"
                               id="completed-tab" data-toggle="tab" href="#completed" role="tab"
                               aria-controls="completed" aria-selected="{{ $tab == 'completed' ? 'true' : 'false' }}"
                               onclick="changeTab('completed')">
                                <i class="fas fa-check-circle"></i> Completed
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'closed' ? 'active' : '' }}"
                               id="closed-tab" data-toggle="tab" href="#closed" role="tab"
                               aria-controls="closed" aria-selected="{{ $tab == 'closed' ? 'true' : 'false' }}"
                               onclick="changeTab('closed')">
                                <i class="fas fa-times-circle"></i> Closed
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
                                            <th width="15%" class="text-center">Assigned Consultant Id</th>
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
                                            <th width="20%" class="text-left">Property Name</th>
                                            <th width="12%" class="text-center">County</th>
                                            <th width="15%" class="text-left">Macro Client</th>
                                            <th width="15%" class="text-center">Assigned Consultant Id</th>
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

                        <div class="tab-pane fade {{ $tab == 'quoted' ? 'show active' : '' }}" id="quoted" role="tabpanel" aria-labelledby="quoted-tab">
                            <!-- Same table structure for quoted -->
                            <div class="table-responsive">
                                <table id="hb837-table-quoted" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%" class="text-center">
                                                <input type="checkbox" id="select-all-quoted" class="form-check-input">
                                            </th>
                                            <th width="20%" class="text-left">Property Name</th>
                                            <th width="12%" class="text-center">County</th>
                                            <th width="15%" class="text-left">Macro Client</th>
                                            <th width="15%" class="text-center">Assigned Consultant Id</th>
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
                                            <th width="12%" class="text-center">County</th>
                                            <th width="15%" class="text-left">Macro Client</th>
                                            <th width="15%" class="text-center">Assigned Consultant Id</th>
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
                                            <th width="15%" class="text-center">Assigned Consultant Id</th>
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
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bulk Actions</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bulk-actions-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bulk-action">Action</label>
                        <select id="bulk-action" name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="status_update">Update Status</option>
                            <option value="consultant_assign">Assign Consultant</option>
                            <option value="delete">Delete Records</option>
                        </select>
                    </div>

                    <div class="form-group" id="status-group" style="display: none;">
                        <label for="bulk-status">New Status</label>
                        <select id="bulk-status" name="bulk_status" class="form-control">
                            <option value="not-started">Not Started</option>
                            <option value="in-progress">In Progress</option>
                            <option value="in-review">In Review</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group" id="consultant-group" style="display: none;">
                        <label for="bulk-consultant">Consultant</label>
                        <select id="bulk-consultant" name="bulk_consultant_id" class="form-control">
                            <option value="">Unassigned</option>
                            <!-- Populated via AJAX -->
                        </select>
                    </div>

                    <div id="selected-count" class="alert alert-info">
                        No records selected
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Execute Action</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
                        <h5><i class="fas fa-exclamation-triangle"></i> DataTables Loading Issue</h5>
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
                    <h4 class="empty-state-title">${state.title}</h4>
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
                    <h5 class="search-empty-state-title">No Matching Results</h5>
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

        return $(tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/hb837/data/' + tab,
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    alert('Error loading data. Please refresh the page.');
                },
                dataSrc: function(json) {
                    console.log('=== DataTables Response Debug for tab:', tab, '===');
                    console.log('Full JSON:', json);
                    console.log('Records total:', json.recordsTotal);
                    console.log('Records filtered:', json.recordsFiltered);
                    console.log('Data array length:', json.data ? json.data.length : 'undefined');

                    // Log first data item to see structure
                    if (json.data && json.data.length > 0) {
                        console.log('First data item:', json.data[0]);
                        console.log('First data item keys:', Object.keys(json.data[0]));
                    }

                    return json.data;
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, width: '3%', className: 'text-center' },
                { data: 'property_name', name: 'property_name', width: '20%', className: 'text-left' },
                { data: 'county', name: 'county', width: '12%', className: 'text-center' },
                { data: 'macro_client', name: 'macro_client', width: '15%', className: 'text-left' },
                { data: 'assigned_consultant_id', name: 'assigned_consultant_id', orderable: false, width: '15%', className: 'text-center' },
                { data: 'scheduled_date_of_inspection', name: 'scheduled_date_of_inspection', width: '15%', className: 'text-center' },
                { data: 'report_status', name: 'report_status', orderable: false, width: '12%', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '8%', className: 'text-center' }
            ],
            order: [[5, 'desc']], // Order by scheduled_date_of_inspection descending
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: false,
            stateSave: true,
            scrollX: true,
            autoWidth: false,
            fixedColumns: false,
            columnDefs: [
                { targets: [0, 4, 5, 6, 7], orderable: false },
                { targets: '_all', className: 'align-middle' }
            ],
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                 '<"row"<"col-sm-12"<"table-responsive"t>>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            drawCallback: function(settings) {
                var api = this.api();
                var pageInfo = api.page.info();
                var data = api.rows().data();

                console.log('DrawCallback - Tab:', tab, 'Page Info:', pageInfo, 'Actual Data Length:', data.length);

                // Only show empty state if we truly have no data
                // Don't interfere with DataTables rendering if there is data
                if (data.length === 0 && pageInfo.recordsTotal === 0) {
                    var emptyStateHtml = getEmptyStateHTML(tab);
                    console.log('Showing empty state for tab:', tab);
                    $(this).find('tbody').html(
                        '<tr class="empty-state-row"><td colspan="8" class="text-center p-0 border-0">' +
                        emptyStateHtml +
                        '</td></tr>'
                    );
                } else if (data.length > 0) {
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

        console.log('Bootstrap tabs initialized successfully');
    }

    // Tab change function
    window.changeTab = function(tab) {
        console.log('Changing tab to:', tab);

        // Save current state using global currentTab
        if (table && $.fn.DataTable.isDataTable(table.table().node())) {
            try {
                var currentPage = table.page();
                var currentSearch = table.search();
                sessionStorage.setItem('hb837_page_' + currentTab, currentPage);
                sessionStorage.setItem('hb837_search_' + currentTab, currentSearch);

                // Destroy current table
                table.destroy();
            } catch (e) {
                console.warn('Error saving table state:', e);
            }
        }

        // Update global currentTab variable
        currentTab = tab;

        // Remove active classes from all tabs and content
        $('#hb837-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
        $('.tab-pane').removeClass('show active');

        // Add active class to clicked tab
        $('#' + tab + '-tab').addClass('active').attr('aria-selected', 'true');
        $('#' + tab).addClass('show active');

        // Initialize DataTable for the new tab using global table variable
        var tableId = '#hb837-table-' + tab;
        table = initDataTable(tab, tableId);

        // Update URL
        updateUrl(tab);

        // Restore state for new tab
        setTimeout(function() {
            if (table && $.fn.DataTable.isDataTable(table.table().node())) {
                try {
                    var savedPage = sessionStorage.getItem('hb837_page_' + tab);
                    var savedSearch = sessionStorage.getItem('hb837_search_' + tab);

                    if (savedSearch) {
                        table.search(savedSearch);
                    }
                    if (savedPage) {
                        table.page(parseInt(savedPage));
                    }
                    table.draw();
                } catch (e) {
                    console.warn('Error restoring table state:', e);
                }
            }
        }, 100);

        // Update tab statistics
        updateTabStatistics(tab);
    };

    // Update URL when tab changes
    function updateUrl(tab) {
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
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
    window.duplicateRecord = function(id) {
        if (confirm('Are you sure you want to duplicate this record?')) {
            $.ajax({
                url: '{{ route("admin.hb837.index") }}/' + id + '/duplicate',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Record duplicated successfully!');
                        table.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while duplicating the record.');
                }
            });
        }
    };

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
                        alert('Record deleted successfully!');
                        table.ajax.reload();
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

    // Bulk actions modal
    window.bulkActions = function() {
        let selectedCount = $('.bulk-checkbox:checked').length;
        if (selectedCount === 0) {
            alert('Please select at least one record.');
            return;
        }
        $('#bulkActionsModal').modal('show');
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

    function getStatusClass(status) {
        if (!status) return '';

        const statusLower = status.toLowerCase().replace(/[\s-_]/g, '-');
        switch (statusLower) {
            case 'not-started':
            case 'notstarted':
                return 'status-not-started';
            case 'in-progress':
            case 'inprogress':
                return 'status-in-progress';
            case 'in-review':
            case 'inreview':
                return 'status-in-review';
            case 'completed':
                return 'status-completed';
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

});
</script>
@stop

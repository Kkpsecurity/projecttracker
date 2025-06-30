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
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>Active Projects</p>
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
                    <a href="{{ route('admin.hb837.import.show') }}" class="btn btn-primary mr-2">
                        <i class="fas fa-file-upload"></i> Import Data
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
                            <a class="nav-link {{ $tab == 'active' ? 'active' : '' }}"
                               id="active-tab" data-toggle="pill" href="#active" role="tab"
                               onclick="changeTab('active')">
                                <i class="fas fa-project-diagram"></i> Active
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'quoted' ? 'active' : '' }}"
                               id="quoted-tab" data-toggle="pill" href="#quoted" role="tab"
                               onclick="changeTab('quoted')">
                                <i class="fas fa-file-invoice-dollar"></i> Quoted
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'completed' ? 'active' : '' }}"
                               id="completed-tab" data-toggle="pill" href="#completed" role="tab"
                               onclick="changeTab('completed')">
                                <i class="fas fa-check-circle"></i> Completed
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'closed' ? 'active' : '' }}"
                               id="closed-tab" data-toggle="pill" href="#closed" role="tab"
                               onclick="changeTab('closed')">
                                <i class="fas fa-times-circle"></i> Closed
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="hb837-tabContent">
                        <!-- DataTable -->
                        <div class="table-responsive" id="table-container">
                            <table id="hb837-table" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th class="text-left">Property Details</th>
                                        <th class="text-center">County</th>
                                        <th class="text-center">Crime Risk</th>
                                        <th class="text-left">Client Contact</th>
                                        <th class="text-center">Consultant</th>
                                        <th class="text-center">Inspection<br>Date</th>
                                        <th class="text-center">Report<br>Status</th>
                                        <th class="text-center">Contract<br>Status</th>
                                        <th class="text-center">Quote</th>
                                        <th class="text-center">Priority</th>
                                        <th class="text-center">Created</th>
                                        <th class="text-center">Actions</th>
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
/* Color coding for GitHub Issue #8 */
.crime-risk-low { background-color: #72b862 !important; color: white !important; }
.crime-risk-moderate { background-color: #95f181 !important; color: black !important; }
.crime-risk-elevated { background-color: #fae099 !important; color: black !important; }
.crime-risk-high { background-color: #f2a36e !important; color: black !important; }
.crime-risk-severe { background-color: #c75845 !important; color: white !important; }

.status-not-started { background-color: #f8d7da !important; color: #721c24 !important; }
.status-in-progress { background-color: #fff3cd !important; color: #856404 !important; }
.status-in-review { background-color: #cce5ff !important; color: #004085 !important; }
.status-completed { background-color: #d4edda !important; color: #155724 !important; }

/* DataTables custom styling */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

/* Tab styling */
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
    background-color: transparent;
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
    overflow: hidden !important;
    box-sizing: border-box;
}

.dataTables_scrollBody {
    overflow-x: hidden !important;
    overflow-y: visible !important;
    width: 100% !important;
    max-width: 100% !important;
}

/* Force table to fit container width - no horizontal scroll */
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
}

/* DataTables wrapper styling */
.dataTables_wrapper {
    width: 100%;
    overflow: hidden;
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
    padding: 10px 4px;
    white-space: nowrap;
    font-size: 0.8rem;
    overflow: hidden;
    text-overflow: ellipsis;
}

#hb837-table thead th:first-child {
    text-align: center;
    width: 3%;
    min-width: 35px;
}

#hb837-table thead th:nth-child(2) { width: 20%; } /* Property Details */
#hb837-table thead th:nth-child(3) { width: 8%; }  /* County */
#hb837-table thead th:nth-child(4) { width: 9%; } /* Crime Risk */
#hb837-table thead th:nth-child(5) { width: 11%; } /* Client Contact */
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
    padding: 8px 4px;
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
}

/* Right align numeric columns */
#hb837-table tbody td:nth-child(10), /* Quote column */
#hb837-table tbody td:nth-child(11), /* Priority column */
#hb837-table thead th:nth-child(10),
#hb837-table thead th:nth-child(11) {
    text-align: center;
}

/* Center align action column */
#hb837-table tbody td:last-child,
#hb837-table thead th:last-child {
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

    /* Hide less important columns on small screens */
    #hb837-table thead th:nth-child(3),
    #hb837-table tbody td:nth-child(3),
    #hb837-table thead th:nth-child(4),
    #hb837-table tbody td:nth-child(4),
    #hb837-table thead th:nth-child(7),
    #hb837-table tbody td:nth-child(7),
    #hb837-table thead th:nth-child(12),
    #hb837-table tbody td:nth-child(12) {
        display: none;
    }

    /* Adjust remaining column widths for mobile */
    #hb837-table thead th:nth-child(2) { width: 25%; }
    #hb837-table thead th:nth-child(5) { width: 20%; }
    #hb837-table thead th:nth-child(6) { width: 15%; }
    #hb837-table thead th:nth-child(8) { width: 10%; }
    #hb837-table thead th:nth-child(9) { width: 10%; }
    #hb837-table thead th:nth-child(10) { width: 8%; }
    #hb837-table thead th:nth-child(11) { width: 7%; }
    #hb837-table thead th:nth-child(13) { width: 15%; }
}

@media (max-width: 576px) {
    .empty-state-container {
        padding: 20px 10px !important;
        min-height: 200px !important;
    }

    .empty-state-icon {
        font-size: 2.5rem;
    }

    .empty-state-title {
        font-size: 1.1rem;
    }

    .empty-state-message {
        font-size: 0.8rem;
    }

    /* Hide even more columns on very small screens */
    #hb837-table thead th:nth-child(6),
    #hb837-table tbody td:nth-child(6),
    #hb837-table thead th:nth-child(10),
    #hb837-table tbody td:nth-child(10),
    #hb837-table thead th:nth-child(11),
    #hb837-table tbody td:nth-child(11) {
        display: none;
    }

    /* Minimal columns for very small screens */
    #hb837-table thead th:nth-child(2) { width: 35%; }
    #hb837-table thead th:nth-child(5) { width: 25%; }
    #hb837-table thead th:nth-child(8) { width: 15%; }
    #hb837-table thead th:nth-child(9) { width: 15%; }
    #hb837-table thead th:nth-child(13) { width: 10%; }
}

/* Ensure no content breaks out of containers */
* {
    box-sizing: border-box;
}

body, html {
    overflow-x: hidden !important;
}

.content-wrapper {
    overflow-x: hidden !important;
}

.card-body {
    overflow-x: hidden !important;
    padding: 1rem;
}

/* DataTables specific fixes */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    overflow: hidden;
    text-overflow: ellipsis;
}
    }

    .empty-state-message {
        font-size: 0.9rem;
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

        // Initialize DataTable
        let currentTab = '{{ $tab }}';
        let table = initDataTable(currentTab);

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
                        <a href="{{ route('admin.hb837.import.show') }}" class="btn btn-outline-${state.color} btn-sm">
                            <i class="fas fa-file-upload"></i> Import Data
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
                    <button onclick="table.search('').draw();" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear Search
                    </button>
                </div>
            </div>
        `;
    }

    function initDataTable(tab) {
        // Check if DataTables is loaded
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables is not loaded. Please check the JavaScript includes.');
            alert('DataTables library is not loaded. Please refresh the page.');
            return null;
        }

        if ($.fn.DataTable.isDataTable('#hb837-table')) {
            $('#hb837-table').DataTable().destroy();
        }

        return $('#hb837-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.hb837.data.tab", "active") }}'.replace('active', tab),
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    alert('Error loading data. Please refresh the page.');
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, width: '40px', className: 'text-center' },
                { data: 'property_name', name: 'property_name', width: '250px', className: 'text-left' },
                { data: 'county', name: 'county', width: '90px', className: 'text-center' },
                { data: 'securitygauge_crime_risk', name: 'securitygauge_crime_risk', orderable: false, width: '110px', className: 'text-center' },
                { data: 'macro_client', name: 'macro_client', width: '140px', className: 'text-left' },
                { data: 'assigned_consultant_id', name: 'assigned_consultant_id', orderable: false, width: '120px', className: 'text-center' },
                { data: 'scheduled_date_of_inspection', name: 'scheduled_date_of_inspection', width: '120px', className: 'text-center' },
                { data: 'report_status', name: 'report_status', orderable: false, width: '100px', className: 'text-center' },
                { data: 'contracting_status', name: 'contracting_status', orderable: false, width: '100px', className: 'text-center' },
                { data: 'quoted_price', name: 'quoted_price', width: '80px', className: 'text-center' },
                { data: 'priority', name: 'priority', orderable: true, width: '70px', className: 'text-center' },
                { data: 'created_at', name: 'created_at', width: '90px', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '140px', className: 'text-center' }
            ],
            order: [[11, 'desc']], // Order by created_at descending
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            stateSave: true,
            scrollX: false,
            autoWidth: false,
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                 '<"row"<"col-sm-12"<"table-responsive"t>>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            language: {
                processing: '<div class="dt-processing-wrapper"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><strong>Loading properties...</strong></div>',
                emptyTable: getEmptyStateHTML(tab),
                info: 'Showing _START_ to _END_ of _TOTAL_ properties',
                infoEmpty: 'No properties to display',
                infoFiltered: '(filtered from _MAX_ total properties)',
                lengthMenu: 'Show _MENU_ properties per page',
                search: 'Search properties:',
                zeroRecords: getSearchEmptyStateHTML(),
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                }
            },
            drawCallback: function(settings) {
                // Re-initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Update bulk selection state
                updateBulkCount();

                // Add custom styling to empty table message
                setTimeout(function() {
                    if ($('#hb837-table tbody tr').length === 1 &&
                        $('#hb837-table tbody tr td').hasClass('dataTables_empty')) {
                        $('#hb837-table tbody tr td').css({
                            'border': 'none',
                            'background': 'transparent'
                        });
                    }
                }, 100);

                // Show/hide bulk actions based on data
                if (settings.aoData && settings.aoData.length > 0) {
                    $('.bulk-actions-container').show();
                } else {
                    $('.bulk-actions-container').hide();
                }
            }
        });
    }

    // Tab change function
    window.changeTab = function(tab) {
        // Save current state
        if (table) {
            var currentPage = table.page();
            var currentSearch = table.search();
            sessionStorage.setItem('hb837_page_' + currentTab, currentPage);
            sessionStorage.setItem('hb837_search_' + currentTab, currentSearch);
        }

        currentTab = tab;
        table = initDataTable(tab);
        updateUrl(tab);

        // Restore state for new tab
        setTimeout(function() {
            var savedPage = sessionStorage.getItem('hb837_page_' + tab);
            var savedSearch = sessionStorage.getItem('hb837_search_' + tab);

            if (savedSearch) {
                table.search(savedSearch);
            }
            if (savedPage) {
                table.page(parseInt(savedPage));
            }
            table.draw();
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
});
</script>
@stop

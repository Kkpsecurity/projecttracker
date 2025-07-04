@extends('adminlte::page')

@section('title', 'Analytics Dashboard - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-chart-line mr-2"></i>
                Analytics Dashboard
            </h1>
        </div>
        <div class="col-sm-6">
            <div class="float-sm-right">
                <ol class="breadcrumb d-inline-block mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
                <div class="btn-group d-block" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download mr-1"></i> Export Data
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.analytics.export', ['type' => 'overview', 'format' => 'csv']) }}">
                            <i class="fas fa-file-csv mr-2"></i> Overview (CSV)
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.analytics.export', ['type' => 'projects', 'format' => 'csv']) }}">
                            <i class="fas fa-file-csv mr-2"></i> Projects (CSV)
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.analytics.export', ['type' => 'team', 'format' => 'csv']) }}">
                            <i class="fas fa-file-csv mr-2"></i> Team Metrics (CSV)
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.analytics.export', ['type' => 'system', 'format' => 'csv']) }}">
                            <i class="fas fa-file-csv mr-2"></i> System Health (CSV)
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('admin.analytics.export', ['type' => 'overview', 'format' => 'json']) }}">
                            <i class="fas fa-file-code mr-2"></i> All Data (JSON)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">

    @if(isset($error))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong> {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <!-- Overview KPI Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $overview['total_projects'] }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $overview['completed_projects'] }}</h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $overview['active_projects'] }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $overview['completion_rate'] }}%</h3>
                    <p>Completion Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-2"></i>
                        Advanced Filters
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="analytics-filters" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_range">Date Range</label>
                                <select class="form-control" id="date_range" name="date_range">
                                    <option value="last_30_days" selected>Last 30 Days</option>
                                    <option value="last_90_days">Last 90 Days</option>
                                    <option value="last_6_months">Last 6 Months</option>
                                    <option value="last_year">Last Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="consultant_filter">Consultant</label>
                                <select class="form-control" id="consultant_filter" name="consultant">
                                    <option value="">All Consultants</option>
                                    <!-- Will be populated via AJAX -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status_filter">Project Status</label>
                                <select class="form-control" id="status_filter" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="not_started">Not Started</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="in_review">In Review</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-primary btn-block" id="apply-filters">
                                        <i class="fas fa-search mr-1"></i> Apply Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="custom-date-range" style="display: none;">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="analytics-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="projects-tab" data-toggle="pill" href="#projects" 
                               role="tab" aria-controls="projects" aria-selected="true">
                                <i class="fas fa-project-diagram"></i> Project Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="team-tab" data-toggle="pill" href="#team" 
                               role="tab" aria-controls="team" aria-selected="false">
                                <i class="fas fa-users"></i> Team Performance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="system-tab" data-toggle="pill" href="#system" 
                               role="tab" aria-controls="system" aria-selected="false">
                                <i class="fas fa-server"></i> System Health
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="financial-tab" data-toggle="pill" href="#financial" 
                               role="tab" aria-controls="financial" aria-selected="false">
                                <i class="fas fa-dollar-sign"></i> Financial Metrics
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="analytics-tabContent">
                        
                        <!-- Project Analytics Tab -->
                        <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                            @include('admin.analytics.tabs.projects', ['projectAnalytics' => $projectAnalytics])
                        </div>
                        
                        <!-- Team Performance Tab -->
                        <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
                            @include('admin.analytics.tabs.team', ['teamMetrics' => $teamMetrics])
                        </div>
                        
                        <!-- System Health Tab -->
                        <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                            @include('admin.analytics.tabs.system', ['systemHealth' => $systemHealth])
                        </div>
                        
                        <!-- Financial Metrics Tab -->
                        <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                            @include('admin.analytics.tabs.financial')
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Comparison Widget -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Performance Benchmarks
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-light" onclick="loadBenchmarks()">
                            <i class="fas fa-sync mr-1"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>This Month vs Last Month</h5>
                                <div class="progress-group">
                                    Projects Created
                                    <span class="float-right"><b id="current-month-projects">-</b>/<b id="last-month-projects">-</b></span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" id="projects-progress" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    Completion Rate
                                    <span class="float-right"><b id="current-completion-rate">-</b>%</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" id="completion-progress" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>Growth Trends</h5>
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Project Growth</span>
                                        <span class="info-box-number" id="project-growth">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>Industry Benchmarks</h5>
                                <small class="text-muted">How you compare</small>
                                <div class="mt-2">
                                    <canvas id="benchmarkRadarChart" style="height: 150px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>Performance Score</h5>
                                <div class="knob-wrapper">
                                    <canvas id="performanceGauge" style="height: 150px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Status Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-info"></i> Real-time Analytics Dashboard</h5>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Last Updated:</strong> <span id="last-updated">{{ date('Y-m-d H:i:s') }}</span>
                        <span class="badge badge-success ml-2" id="live-indicator">
                            <i class="fas fa-circle fa-sm mr-1"></i> Live
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Active Projects:</strong> <span id="active-projects-count">{{ $overview['active_projects'] }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Pending Reviews:</strong> <span id="pending-reviews-count">0</span>
                    </div>
                    <div class="col-md-3">
                        <strong>System Health:</strong> <span id="system-health-score">85</span>%
                        <span class="badge badge-success ml-1">Healthy</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Stats Footer -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat mr-2"></i>
                        Real-time System Status
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success" id="live-indicator">
                            <i class="fas fa-circle"></i> Live
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="realtime-stats">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Projects</span>
                                    <span class="info-box-number" id="active-projects-count">{{ $overview['active_projects'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-eye"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Reviews</span>
                                    <span class="info-box-number" id="pending-reviews-count">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed Today</span>
                                    <span class="info-box-number" id="completed-today-count">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-heartbeat"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">System Health</span>
                                    <span class="info-box-number" id="system-health-score">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Details Modal -->
    <div class="modal fade" id="projectDetailsModal" tabindex="-1" role="dialog" aria-labelledby="projectDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectDetailsModalLabel">
                        <i class="fas fa-chart-line mr-2"></i>
                        Detailed Project Analytics
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Project Timeline Analysis</h6>
                            <canvas id="detailedTimelineChart" style="height: 300px;"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>Consultant Performance Comparison</h6>
                            <canvas id="consultantComparisonChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avg. Project Duration</span>
                                    <span class="info-box-number" id="avgProjectDuration">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">On-Time Delivery Rate</span>
                                    <span class="info-box-number" id="onTimeDeliveryRate">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Projects at Risk</span>
                                    <span class="info-box-number" id="projectsAtRisk">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="exportDetailedData()">
                        <i class="fas fa-download mr-1"></i> Export Details
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
    .analytics-chart {
        height: 400px;
        width: 100%;
    }
    
    .small-chart {
        height: 250px;
        width: 100%;
    }
    
    .metric-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .metric-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .metric-label {
        color: #7f8c8d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .trend-up {
        color: #27ae60;
    }
    
    .trend-down {
        color: #e74c3c;
    }
    
    .live-indicator {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .nav-tabs .nav-link {
        border-radius: 0;
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        background-color: transparent;
        border-bottom-color: #007bff;
        color: #007bff;
    }
    
    .card-tabs .card-header {
        background: #f8f9fa;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize real-time updates
    initializeRealtimeUpdates();
    
    // Initialize charts when tabs are shown
    $('#analytics-tabs a').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");
        if (target === '#projects') {
            initializeProjectCharts();
        } else if (target === '#team') {
            initializeTeamCharts();
        } else if (target === '#system') {
            initializeSystemCharts();
        } else if (target === '#financial') {
            initializeFinancialCharts();
        }
    });
    
    // Initialize default charts
    initializeProjectCharts();
    
    // Load performance benchmarks
    setTimeout(loadBenchmarks, 1000);
    
    // Handle date range filter changes
    $('#date_range').change(function() {
        if ($(this).val() === 'custom') {
            $('#custom-date-range').show();
        } else {
            $('#custom-date-range').hide();
        }
    });
    
    // Apply filters
    $('#apply-filters').click(function() {
        const filters = {
            date_range: $('#date_range').val(),
            consultant: $('#consultant_filter').val(),
            status: $('#status_filter').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        };
        
        applyAnalyticsFilters(filters);
    });
    
    // Load consultants for filter dropdown
    loadConsultantsForFilter();
});

// Modal and detailed analytics functionality
function showProjectDetails() {
    $('#projectDetailsModal').modal('show');
    
    // Load detailed analytics when modal is shown
    loadDetailedProjectAnalytics();
}

function loadDetailedProjectAnalytics() {
    // Simulate loading detailed data
    const timelineData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Projects Started',
            data: [5, 8, 12, 7, 15, 10],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }, {
            label: 'Projects Completed',
            data: [3, 6, 10, 9, 12, 13],
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }]
    };
    
    const consultantData = {
        labels: ['John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Wilson'],
        datasets: [{
            label: 'Projects Completed',
            data: [12, 15, 8, 11],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
        }]
    };
    
    // Initialize detailed timeline chart
    const timelineCtx = document.getElementById('detailedTimelineChart');
    if (timelineCtx) {
        new Chart(timelineCtx, {
            type: 'line',
            data: timelineData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Initialize consultant comparison chart
    const consultantCtx = document.getElementById('consultantComparisonChart');
    if (consultantCtx) {
        new Chart(consultantCtx, {
            type: 'bar',
            data: consultantData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Update KPI values
    $('#avgProjectDuration').text('24 days');
    $('#onTimeDeliveryRate').text('87%');
    $('#projectsAtRisk').text('3');
}

function exportDetailedData() {
    // Export detailed analytics data
    window.open('{{ route("admin.analytics.export", ["type" => "projects", "format" => "csv", "detailed" => "true"]) }}');
}

// Filter functionality
$('#date_range').on('change', function() {
    if ($(this).val() === 'custom') {
        $('#custom-date-range').show();
    } else {
        $('#custom-date-range').hide();
    }
});

$('#apply-filters').on('click', function() {
    const filters = $('#analytics-filters').serialize();
    refreshAnalyticsData(filters);
});

function refreshAnalyticsData(filters) {
    // Show loading indicators
    $('.card-body').append('<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>');
    
    // Fetch filtered data
    fetch('{{ route("admin.analytics.index") }}?' + filters)
        .then(response => response.text())
        .then(html => {
            // Parse the response and update charts
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract updated data from the new response
            const scriptTags = doc.querySelectorAll('script');
            scriptTags.forEach(script => {
                if (script.textContent.includes('analyticsData')) {
                    // Re-execute the analytics data initialization
                    eval(script.textContent);
                }
            });
            
            // Re-initialize charts with new data
            destroyExistingCharts();
            initializeProjectCharts();
            initializeTeamCharts();
            initializeSystemCharts();
            
            // Remove loading indicators
            $('.overlay').remove();
            
            // Show success message
            toastr.success('Analytics data updated successfully!');
        })
        .catch(error => {
            console.error('Error refreshing analytics:', error);
            $('.overlay').remove();
            toastr.error('Failed to refresh analytics data');
        });
}

function destroyExistingCharts() {
    // Destroy existing Chart.js instances to prevent memory leaks
    Chart.helpers.each(Chart.instances, function(instance) {
        instance.destroy();
    });
}

// Load consultant options for filter
fetch('{{ route("admin.analytics.consultant-metrics") }}')
    .then(response => response.json())
    .then(data => {
        const consultantSelect = $('#consultant_filter');
        if (data.consultant_workload) {
            data.consultant_workload.forEach(consultant => {
                consultantSelect.append(`<option value="${consultant.consultant_id}">${consultant.consultant_name}</option>`);
            });
        }
    })
    .catch(error => {
        console.error('Error loading consultants:', error);
    });

function initializeRealtimeUpdates() {
    // Update real-time stats every 30 seconds
    setInterval(function() {
        $.get('{{ route("admin.analytics.realtime-stats") }}')
            .done(function(data) {
                // Update status indicators
                $('#active-projects-count').text(data.active_projects);
                $('#pending-reviews-count').text(data.pending_reviews);
                $('#completed-today-count').text(data.completed_today);
                $('#system-health-score').text(data.system_health);
                
                // Update KPI cards
                updateKPICards(data);
                
                // Update health badge
                const healthBadge = $('#system-health-score').next('.badge');
                if (data.system_health >= 80) {
                    healthBadge.removeClass('badge-warning badge-danger').addClass('badge-success').text('Healthy');
                } else if (data.system_health >= 60) {
                    healthBadge.removeClass('badge-success badge-danger').addClass('badge-warning').text('Warning');
                } else {
                    healthBadge.removeClass('badge-success badge-warning').addClass('badge-danger').text('Critical');
                }
                
                // Update last updated timestamp
                $('#last-updated').text(new Date().toLocaleString());
                
                // Update indicator
                $('#live-indicator').removeClass('badge-danger').addClass('badge-success');
                
                console.log('Real-time stats updated successfully');
            })
            .fail(function() {
                $('#live-indicator').removeClass('badge-success').addClass('badge-danger');
                console.error('Failed to update real-time stats');
            });
    }, 30000);
    
    // Initial update
    setTimeout(function() {
        $.get('{{ route("admin.analytics.realtime-stats") }}')
            .done(function(data) {
                $('#active-projects-count').text(data.active_projects);
                $('#pending-reviews-count').text(data.pending_reviews);
                $('#system-health-score').text(data.system_health);
            });
    }, 2000);
}

function initializeProjectCharts() {
    // Project status distribution chart
    const statusCtx = document.getElementById('projectStatusChart');
    if (statusCtx) {
        const statusData = @json($projectAnalytics['status_distribution']);
        if (statusData && statusData.length > 0) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.report_status || 'Unknown'),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: [
                            '#17a2b8', // info
                            '#ffc107', // warning  
                            '#28a745', // success
                            '#6c757d', // secondary
                            '#dc3545', // danger
                            '#6f42c1'  // purple
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Show message if no data
            statusCtx.parentElement.innerHTML = '<div class="alert alert-info">No project status data available</div>';
        }
    }
    
    // Monthly trends chart
    const trendsCtx = document.getElementById('monthlyTrendsChart');
    if (trendsCtx) {
        const trendsData = @json($projectAnalytics['monthly_trends']);
        if (trendsData && trendsData.length > 0) {
            const labels = trendsData.map(item => item.month);
            const createdData = trendsData.map(item => item.created || item.new_projects || 0);
            const completedData = trendsData.map(item => item.completed || item.completed_projects || 0);
            
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Projects',
                        data: createdData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Completed Projects',
                        data: completedData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        } else {
            trendsCtx.parentElement.innerHTML = '<div class="alert alert-info">No monthly trends data available</div>';
        }
    }
    
    // Property type breakdown chart
    const propertyCtx = document.getElementById('propertyTypeChart');
    if (propertyCtx) {
        const propertyData = @json($projectAnalytics['property_type_breakdown']);
        if (propertyData && propertyData.length > 0) {
            new Chart(propertyCtx, {
                type: 'pie',
                data: {
                    labels: propertyData.map(item => item.property_type || 'Unknown'),
                    datasets: [{
                        data: propertyData.map(item => item.count),
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', 
                            '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            propertyCtx.parentElement.innerHTML = '<div class="alert alert-info">No property type data available</div>';
        }
    }
}

function initializeTeamCharts() {
    // Consultant workload chart
    const workloadCtx = document.getElementById('consultantWorkloadChart');
    if (workloadCtx) {
        const workloadData = @json($teamMetrics['consultant_workload']);
        if (workloadData && workloadData.length > 0) {
            new Chart(workloadCtx, {
                type: 'bar',
                data: {
                    labels: workloadData.map(item => item.consultant_name || 'Unknown'),
                    datasets: [{
                        label: 'Active Projects',
                        data: workloadData.map(item => item.active_projects || 0),
                        backgroundColor: '#007bff',
                        borderColor: '#0056b3',
                        borderWidth: 1
                    }, {
                        label: 'Completed Projects',
                        data: workloadData.map(item => item.completed_projects || 0),
                        backgroundColor: '#28a745',
                        borderColor: '#1e7e34',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        } else {
            workloadCtx.parentElement.innerHTML = '<div class="alert alert-info">No consultant workload data available</div>';
        }
    }

    // Team efficiency metrics
    const efficiencyCtx = document.getElementById('teamEfficiencyChart');
    if (efficiencyCtx) {
        const efficiencyData = @json($teamMetrics['team_efficiency']);
        if (efficiencyData) {
            new Chart(efficiencyCtx, {
                type: 'radar',
                data: {
                    labels: ['Completion Rate', 'Avg Processing Time', 'Productivity Score', 'Quality Score', 'Client Satisfaction'],
                    datasets: [{
                        label: 'Team Performance',
                        data: [
                            efficiencyData.completion_rate || 0,
                            Math.min(100, (efficiencyData.avg_processing_time || 0) / 30 * 100), // Normalize to 0-100
                            efficiencyData.productivity_score || 0,
                            80, // Placeholder for quality score
                            85  // Placeholder for client satisfaction
                        ],
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderColor: '#007bff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    }
}

function initializeSystemCharts() {
    // Database growth chart
    const dbGrowthCtx = document.getElementById('databaseGrowthChart');
    if (dbGrowthCtx) {
        const dbStats = @json($systemHealth['database_stats']);
        if (dbStats && dbStats.growth_metrics && dbStats.growth_metrics.length > 0) {
            new Chart(dbGrowthCtx, {
                type: 'line',
                data: {
                    labels: dbStats.growth_metrics.map(item => item.month),
                    datasets: [{
                        label: 'Total Records',
                        data: dbStats.growth_metrics.map(item => item.total_records),
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        } else {
            dbGrowthCtx.parentElement.innerHTML = '<div class="alert alert-info">No database growth data available</div>';
        }
    }

    // System health score gauge
    const healthCtx = document.getElementById('systemHealthGauge');
    if (healthCtx) {
        const healthScore = @json($systemHealth['backup_compliance']['backup_health']) || 0;
        new Chart(healthCtx, {
            type: 'doughnut',
            data: {
                labels: ['Health Score', 'Issues'],
                datasets: [{
                    data: [healthScore, 100 - healthScore],
                    backgroundColor: [
                        healthScore >= 80 ? '#28a745' : healthScore >= 60 ? '#ffc107' : '#dc3545',
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: -90,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [{
                beforeDraw: function(chart) {
                    const width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;
                    
                    ctx.restore();
                    const fontSize = (height / 160).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "top";
                    
                    const text = healthScore + "%",
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 1.4;
                    
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });
    }

    // User activity chart
    const userActivityCtx = document.getElementById('userActivityChart');
    if (userActivityCtx) {
        const userActivity = @json($systemHealth['user_activity']);
        if (userActivity && userActivity.user_roles && userActivity.user_roles.length > 0) {
            new Chart(userActivityCtx, {
                type: 'bar',
                data: {
                    labels: userActivity.user_roles.map(item => item.role || 'Unknown'),
                    datasets: [{
                        label: 'Active Users',
                        data: userActivity.user_roles.map(item => item.count || 0),
                        backgroundColor: '#6f42c1',
                        borderColor: '#563d7c',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                });
        } else {
            userActivityCtx.parentElement.innerHTML = '<div class="alert alert-info">No user activity data available</div>';
        }
    }
}

function initializeFinancialCharts() {
    // Revenue trends chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        // Placeholder financial data - would be populated from backend
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 15000, 18000, 14000, 20000, 22000],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

// Performance comparison functions
function loadBenchmarks() {
    $.get('{{ route("admin.analytics.benchmarks") }}')
        .done(function(data) {
            // Update current vs last month data
            $('#current-month-projects').text(data.current_month.total_projects);
            $('#last-month-projects').text(data.last_month.total_projects);
            
            // Calculate and show completion rate
            const currentRate = data.current_month.total_projects > 0 ? 
                Math.round((data.current_month.completed_projects / data.current_month.total_projects) * 100) : 0;
            $('#current-completion-rate').text(currentRate);
            $('#completion-progress').css('width', currentRate + '%');
            
            // Update project progress bar
            const projectProgress = data.last_month.total_projects > 0 ? 
                Math.min(100, (data.current_month.total_projects / data.last_month.total_projects) * 100) : 100;
            $('#projects-progress').css('width', projectProgress + '%');
            
            // Update growth indicators
            const projectGrowth = data.trends.project_growth;
            $('#project-growth').text(projectGrowth + '%');
            
            // Color code growth
            const growthElement = $('#project-growth').parent().find('.info-box-icon');
            if (projectGrowth > 0) {
                growthElement.removeClass('bg-danger bg-warning').addClass('bg-success');
                growthElement.find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
            } else if (projectGrowth < 0) {
                growthElement.removeClass('bg-success bg-warning').addClass('bg-danger');
                growthElement.find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
            } else {
                growthElement.removeClass('bg-success bg-danger').addClass('bg-warning');
                growthElement.find('i').removeClass('fa-arrow-up fa-arrow-down').addClass('fa-minus');
            }
            
            // Initialize benchmark radar chart
            initializeBenchmarkRadar(data);
            
            // Initialize performance gauge
            initializePerformanceGauge(data);
            
            console.log('Benchmarks loaded successfully');
        })
        .fail(function() {
            console.error('Failed to load benchmarks');
            toastr.error('Failed to load performance benchmarks');
        });
}

function initializeBenchmarkRadar(data) {
    const ctx = document.getElementById('benchmarkRadarChart');
    if (ctx) {
        const currentRate = data.current_month.total_projects > 0 ? 
            Math.round((data.current_month.completed_projects / data.current_month.total_projects) * 100) : 0;
        
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Completion Rate', 'Processing Time', 'Quality Score'],
                datasets: [{
                    label: 'Your Performance',
                    data: [
                        currentRate,
                        Math.max(0, 100 - (data.current_month.avg_processing_time || 0) * 2), // Invert processing time
                        85 // Placeholder for quality score
                    ],
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: '#007bff',
                    borderWidth: 2
                }, {
                    label: 'Industry Average',
                    data: [
                        data.industry_benchmarks.avg_completion_rate,
                        Math.max(0, 100 - data.industry_benchmarks.avg_processing_time * 2),
                        data.industry_benchmarks.quality_score
                    ],
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: '#28a745',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            fontSize: 10
                        }
                    }
                }
            }
        });
    }
}

function initializePerformanceGauge(data) {
    const ctx = document.getElementById('performanceGauge');
    if (ctx) {
        // Calculate overall performance score
        const completionRate = data.current_month.total_projects > 0 ? 
            (data.current_month.completed_projects / data.current_month.total_projects) * 100 : 0;
        const timeScore = Math.max(0, 100 - (data.current_month.avg_processing_time || 0) * 2);
        const overallScore = Math.round((completionRate + timeScore + 85) / 3); // Include quality placeholder
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Performance', 'Gap'],
                datasets: [{
                    data: [overallScore, 100 - overallScore],
                    backgroundColor: [
                        overallScore >= 80 ? '#28a745' : overallScore >= 60 ? '#ffc107' : '#dc3545',
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: -90,
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [{
                beforeDraw: function(chart) {
                    const width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;
                    
                    ctx.restore();
                    const fontSize = (height / 120).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "top";
                    
                    const text = overallScore + "%",
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 1.3;
                    
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });
    }
}

// ...existing code...
</script>
@stop

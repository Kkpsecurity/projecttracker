@extends('adminlte::page')

@section('title', 'Consultant Financial Report')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-file-invoice-dollar"></i> Consultant Financial Summary Report</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.consultants.index') }}">Consultants</a></li>
                <li class="breadcrumb-item active">Financial Report</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Top 10 Consultants by Projects</h3>
                </div>
                <div class="card-body">
                    <canvas id="topConsultantsChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Project Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="projectStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Top 10 Consultants by Revenue</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-percentage"></i> Completion Rate by Consultant</h3>
                </div>
                <div class="card-body">
                    <canvas id="completionRateChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Consultant Performance & Project Summary</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This report provides a comprehensive overview of each consultant's project portfolio, 
                        including total projects, completion rates, and average project completion times.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="consultants-report-table" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Consultant Name</th>
                            <th>Company</th>
                            <th>Total Projects</th>
                            <th>Active Projects</th>
                            <th>Completed Projects</th>
                            <th>Completion Rate</th>
                            <th>Total Financial Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-consultants">-</h3>
                    <p>Total Consultants</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="total-projects">-</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="active-projects">-</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="completed-projects">-</h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        let topConsultantsChart, projectStatusChart, revenueChart, completionRateChart;

        $(document).ready(function() {
            const table = $('#consultants-report-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.consultants.report') }}',
                columns: [
                    { data: 'name', name: 'first_name' },
                    { data: 'company', name: 'dba_company_name' },
                    { 
                        data: 'total_projects', 
                        name: 'total_projects',
                        className: 'text-center'
                    },
                    { 
                        data: 'active_projects', 
                        name: 'active_projects',
                        className: 'text-center'
                    },
                    { 
                        data: 'completed_projects', 
                        name: 'completed_projects',
                        className: 'text-center'
                    },
                    { 
                        data: 'completion_rate', 
                        name: 'completion_rate',
                        className: 'text-center',
                        orderable: false
                    },
                    { 
                        data: 'total_financial_value', 
                        name: 'total_financial_value',
                        className: 'text-right',
                        orderable: false
                    },
                    { 
                        data: 'actions', 
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [[2, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        orientation: 'landscape'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }
                ],
                drawCallback: function(settings) {
                    updateSummaryStats(settings.json);
                    updateCharts(settings.json);
                }
            });

            function updateSummaryStats(data) {
                if (!data || !data.data) return;

                let totalConsultants = data.recordsTotal || 0;
                let totalProjects = 0;
                let activeProjects = 0;
                let completedProjects = 0;

                data.data.forEach(function(row) {
                    totalProjects += parseInt(row.total_projects) || 0;
                    activeProjects += parseInt(row.active_projects) || 0;
                    completedProjects += parseInt(row.completed_projects) || 0;
                });

                $('#total-consultants').text(totalConsultants);
                $('#total-projects').text(totalProjects);
                $('#active-projects').text(activeProjects);
                $('#completed-projects').text(completedProjects);
            }

            function updateCharts(data) {
                if (!data || !data.data) return;

                // Destroy existing charts
                if (topConsultantsChart) topConsultantsChart.destroy();
                if (projectStatusChart) projectStatusChart.destroy();
                if (revenueChart) revenueChart.destroy();
                if (completionRateChart) completionRateChart.destroy();

                // Sort by total projects and get top 10
                let sortedByProjects = [...data.data].sort((a, b) => 
                    parseInt(b.total_projects) - parseInt(a.total_projects)
                ).slice(0, 10);

                // Top Consultants by Projects Chart
                const ctxProjects = document.getElementById('topConsultantsChart').getContext('2d');
                topConsultantsChart = new Chart(ctxProjects, {
                    type: 'bar',
                    data: {
                        labels: sortedByProjects.map(row => row.name),
                        datasets: [{
                            label: 'Total Projects',
                            data: sortedByProjects.map(row => parseInt(row.total_projects)),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Project Status Distribution Chart
                let totalActive = 0;
                let totalCompleted = 0;
                data.data.forEach(row => {
                    totalActive += parseInt(row.active_projects) || 0;
                    totalCompleted += parseInt(row.completed_projects) || 0;
                });

                const ctxStatus = document.getElementById('projectStatusChart').getContext('2d');
                projectStatusChart = new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active Projects', 'Completed Projects'],
                        datasets: [{
                            data: [totalActive, totalCompleted],
                            backgroundColor: [
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)'
                            ],
                            borderColor: [
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // Sort by financial value and get top 10
                let sortedByRevenue = [...data.data].map(row => ({
                    ...row,
                    revenue: parseFloat(row.total_financial_value.replace(/[$,]/g, ''))
                })).sort((a, b) => b.revenue - a.revenue).slice(0, 10);

                // Revenue Chart
                const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
                revenueChart = new Chart(ctxRevenue, {
                    type: 'bar',
                    data: {
                        labels: sortedByRevenue.map(row => row.name),
                        datasets: [{
                            label: 'Total Revenue ($)',
                            data: sortedByRevenue.map(row => row.revenue),
                            backgroundColor: 'rgba(75, 192, 192, 0.8)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
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

                // Completion Rate Chart (Top 10)
                let sortedByCompletion = [...data.data]
                    .filter(row => parseInt(row.total_projects) > 0)
                    .map(row => ({
                        ...row,
                        rate: parseFloat(row.completion_rate.replace('%', ''))
                    }))
                    .sort((a, b) => b.rate - a.rate)
                    .slice(0, 10);

                const ctxCompletion = document.getElementById('completionRateChart').getContext('2d');
                completionRateChart = new Chart(ctxCompletion, {
                    type: 'bar',
                    data: {
                        labels: sortedByCompletion.map(row => row.name),
                        datasets: [{
                            label: 'Completion Rate (%)',
                            data: sortedByCompletion.map(row => row.rate),
                            backgroundColor: 'rgba(153, 102, 255, 0.8)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@stop

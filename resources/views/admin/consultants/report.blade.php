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
                        orientation: 'landscape',
                        customize: function (doc) {
                            // Reduce wrapping-induced row growth (and resulting blank space) by
                            // giving the Company column more room and tightening typography.
                            doc.pageMargins = [20, 20, 20, 20];
                            doc.defaultStyle.fontSize = 7;
                            doc.styles.tableHeader.fontSize = 8;

                            // Find the exported table node in the document.
                            let tableNode = null;
                            let tableIndex = -1;
                            for (let i = 0; i < doc.content.length; i++) {
                                if (doc.content[i].table) {
                                    tableNode = doc.content[i];
                                    tableIndex = i;
                                    break;
                                }
                            }
                            if (!tableNode) return;

                            // Make Company column flex to take remaining width.
                            // Columns: Consultant, Company, Total, Active, Completed, Rate, Value
                            tableNode.table.widths = ['auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto'];

                            // Truncate long company names so they don't wrap into tall rows.
                            const maxCompanyChars = 40;
                            const truncate = function (value, maxChars) {
                                if (!value) return '';
                                const s = String(value);
                                if (s.length <= maxChars) return s;
                                return s.slice(0, Math.max(0, maxChars - 1)) + '…';
                            };

                            const body = tableNode.table.body || [];
                            for (let r = 1; r < body.length; r++) {
                                const cell = body[r][1]; // Company column

                                if (typeof cell === 'string') {
                                    body[r][1] = truncate(cell, maxCompanyChars);
                                    continue;
                                }

                                if (cell && typeof cell === 'object') {
                                    const current = (cell.text ?? '');
                                    cell.text = truncate(current, maxCompanyChars);
                                    cell.noWrap = true;
                                }
                            }

                            // --- Grand Totals (based on exported rows) ---
                            const getCellText = function (cell) {
                                if (cell === null || cell === undefined) return '';
                                if (typeof cell === 'string' || typeof cell === 'number') return String(cell);
                                if (typeof cell === 'object' && cell.text !== undefined) return String(cell.text);
                                return '';
                            };

                            const toInt = function (value) {
                                const n = parseInt(String(value).replace(/[^0-9-]/g, ''), 10);
                                return Number.isFinite(n) ? n : 0;
                            };

                            const toMoney = function (value) {
                                const n = parseFloat(String(value).replace(/[^0-9.-]/g, ''));
                                return Number.isFinite(n) ? n : 0;
                            };

                            let totalConsultants = 0;
                            let totalProjects = 0;
                            let activeProjects = 0;
                            let completedProjects = 0;
                            let totalRevenue = 0;

                            // Columns: 0 Consultant, 1 Company, 2 Total, 3 Active, 4 Completed, 5 Rate, 6 Value
                            for (let r = 1; r < body.length; r++) {
                                totalConsultants++;
                                totalProjects += toInt(getCellText(body[r][2]));
                                activeProjects += toInt(getCellText(body[r][3]));
                                completedProjects += toInt(getCellText(body[r][4]));
                                totalRevenue += toMoney(getCellText(body[r][6]));
                            }

                            const overallCompletionRate = totalProjects > 0
                                ? Math.round(((completedProjects / totalProjects) * 100) * 10) / 10
                                : 0;

                            const formatMoney = function (amount) {
                                const n = Number.isFinite(amount) ? amount : 0;
                                return '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            };

                            // Insert the totals section after the main table.
                            if (tableIndex >= 0) {
                                doc.content.splice(tableIndex + 1, 0,
                                    { text: 'Grand Totals', bold: true, margin: [0, 10, 0, 4] },
                                    {
                                        table: {
                                            widths: ['*', 'auto'],
                                            body: [
                                                [{ text: 'Total Consultants', bold: true }, String(totalConsultants)],
                                                [{ text: 'Total Projects', bold: true }, String(totalProjects)],
                                                [{ text: 'Active Projects', bold: true }, String(activeProjects)],
                                                [{ text: 'Completed Projects', bold: true }, String(completedProjects)],
                                                [{ text: 'Overall Completion Rate', bold: true }, String(overallCompletionRate) + '%'],
                                                [{ text: 'Total Financial Value', bold: true }, formatMoney(totalRevenue)],
                                            ]
                                        },
                                        layout: 'lightHorizontalLines',
                                        margin: [0, 0, 0, 0]
                                    }
                                );
                            }

                            // Tighten cell padding so multi-line cells waste less vertical space.
                            tableNode.layout = {
                                paddingLeft: function () { return 4; },
                                paddingRight: function () { return 4; },
                                paddingTop: function () { return 2; },
                                paddingBottom: function () { return 2; }
                            };
                        }
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
                    // Charts + summary cards are loaded via the metrics endpoint.
                    // Keeping this empty avoids incorrect per-page totals when using serverSide paging.
                }
            });

            function refreshMetrics() {
                $.ajax({
                    url: '{{ route('admin.consultants.report.metrics') }}',
                    method: 'GET',
                    dataType: 'json',
                }).done(function(payload) {
                    if (!payload || !payload.rows) return;

                    updateSummaryStatsFromMetrics(payload.summary);
                    updateChartsFromMetrics(payload.rows);
                });
            }

            function updateSummaryStatsFromMetrics(summary) {
                if (!summary) return;
                $('#total-consultants').text(summary.total_consultants ?? 0);
                $('#total-projects').text(summary.total_projects ?? 0);
                $('#active-projects').text(summary.active_projects ?? 0);
                $('#completed-projects').text(summary.completed_projects ?? 0);
            }

            function updateChartsFromMetrics(rows) {
                if (!rows || !rows.length) return;

                // Destroy existing charts
                if (topConsultantsChart) topConsultantsChart.destroy();
                if (projectStatusChart) projectStatusChart.destroy();
                if (revenueChart) revenueChart.destroy();
                if (completionRateChart) completionRateChart.destroy();

                // Sort by total projects and get top 10
                let sortedByProjects = [...rows]
                    .sort((a, b) => (b.total_projects || 0) - (a.total_projects || 0))
                    .slice(0, 10);

                // Top Consultants by Projects Chart
                const ctxProjects = document.getElementById('topConsultantsChart').getContext('2d');
                topConsultantsChart = new Chart(ctxProjects, {
                    type: 'bar',
                    data: {
                        labels: sortedByProjects.map(row => row.name),
                        datasets: [{
                            label: 'Total Projects',
                            data: sortedByProjects.map(row => row.total_projects || 0),
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
                rows.forEach(row => {
                    totalActive += row.active_projects || 0;
                    totalCompleted += row.completed_projects || 0;
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
                let sortedByRevenue = [...rows]
                    .sort((a, b) => (b.total_revenue || 0) - (a.total_revenue || 0))
                    .slice(0, 10);

                // Revenue Chart
                const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
                revenueChart = new Chart(ctxRevenue, {
                    type: 'bar',
                    data: {
                        labels: sortedByRevenue.map(row => row.name),
                        datasets: [{
                            label: 'Total Revenue ($)',
                            data: sortedByRevenue.map(row => row.total_revenue || 0),
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

                // Completion Rate Chart (Top 10) - exclude consultants with 0 total projects
                let sortedByCompletion = [...rows]
                    .filter(row => (row.total_projects || 0) > 0)
                    .sort((a, b) => (b.completion_rate || 0) - (a.completion_rate || 0))
                    .slice(0, 10);

                const ctxCompletion = document.getElementById('completionRateChart').getContext('2d');
                completionRateChart = new Chart(ctxCompletion, {
                    type: 'bar',
                    data: {
                        labels: sortedByCompletion.map(row => row.name),
                        datasets: [{
                            label: 'Completion Rate (%)',
                            data: sortedByCompletion.map(row => row.completion_rate || 0),
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

            // Initial load (charts + summary are based on full dataset)
            refreshMetrics();
        });
    </script>
@stop

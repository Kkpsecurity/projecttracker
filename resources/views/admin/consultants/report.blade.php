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
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.consultants.report.date-anomalies') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-exclamation-triangle"></i> View Date Anomalies
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Top 10 Consultants by Completed Projects</h3>
                </div>
                <div class="card-body">
                    <canvas id="topConsultantsChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Gross vs Net Revenue (Top 10)</h3>
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
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Top 10 Consultants by Gross Revenue</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Average Report Completion Time (Top 10 Fastest)</h3>
                </div>
                <div class="card-body">
                    <canvas id="completionRateChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Consultant Revenue Summary (Completed Projects)</h3>
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
                        This report breaks down work completed by our consultants and associated revenue, 
                        showing completed projects with gross revenue, estimated expenses, net revenue, and average report completion time.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="consultants-report-table" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Consultant Name</th>
                            <th>Company</th>
                            <th>No. of Completed Projects</th>
                            <th>Gross Revenue</th>
                            <th>Estimated Expenses</th>
                            <th>Net Revenue</th>
                            <th>Avg Report Completion Time (Days)</th>
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
                    <h3 id="completed-projects">-</h3>
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
                    <h3 id="gross-revenue">-</h3>
                    <p>Gross Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="net-revenue">-</h3>
                    <p>Net Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
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
                        data: 'completed_projects', 
                        name: 'completed_projects',
                        className: 'text-center'
                    },
                    { 
                        data: 'gross_revenue', 
                        name: 'gross_revenue',
                        className: 'text-right',
                        orderable: false
                    },
                    { 
                        data: 'estimated_expenses', 
                        name: 'estimated_expenses',
                        className: 'text-right',
                        orderable: false
                    },
                    { 
                        data: 'net_revenue', 
                        name: 'net_revenue',
                        className: 'text-right',
                        orderable: false
                    },
                    { 
                        data: 'avg_completion_time', 
                        name: 'avg_completion_time',
                        className: 'text-center',
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
                            // Columns: Consultant, Company, Completed, Gross Revenue, Est Expenses, Net Revenue, Avg Completion
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
                            let completedProjects = 0;
                            let grossRevenue = 0;
                            let estimatedExpenses = 0;
                            let netRevenue = 0;

                            // Columns: 0 Consultant, 1 Company, 2 Completed, 3 Gross, 4 Expenses, 5 Net, 6 Avg Days
                            for (let r = 1; r < body.length; r++) {
                                totalConsultants++;
                                completedProjects += toInt(getCellText(body[r][2]));
                                grossRevenue += toMoney(getCellText(body[r][3]));
                                estimatedExpenses += toMoney(getCellText(body[r][4]));
                                netRevenue += toMoney(getCellText(body[r][5]));
                            }

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
                                                [{ text: 'Completed Projects', bold: true }, String(completedProjects)],
                                                [{ text: 'Gross Revenue', bold: true }, formatMoney(grossRevenue)],
                                                [{ text: 'Estimated Expenses', bold: true }, formatMoney(estimatedExpenses)],
                                                [{ text: 'Net Revenue', bold: true }, formatMoney(netRevenue)],
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
                $('#completed-projects').text(summary.completed_projects ?? 0);
                
                const formatMoney = function(amount) {
                    return '$' + (amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };
                
                $('#gross-revenue').text(formatMoney(summary.gross_revenue ?? 0));
                $('#net-revenue').text(formatMoney(summary.net_revenue ?? 0));
            }

            function updateChartsFromMetrics(rows) {
                if (!rows || !rows.length) return;

                // Destroy existing charts
                if (topConsultantsChart) topConsultantsChart.destroy();
                if (projectStatusChart) projectStatusChart.destroy();
                if (revenueChart) revenueChart.destroy();
                if (completionRateChart) completionRateChart.destroy();

                // Sort by completed projects and get top 10
                let sortedByProjects = [...rows]
                    .sort((a, b) => (b.completed_projects || 0) - (a.completed_projects || 0))
                    .slice(0, 10);

                // Top Consultants by Completed Projects Chart
                const ctxProjects = document.getElementById('topConsultantsChart').getContext('2d');
                topConsultantsChart = new Chart(ctxProjects, {
                    type: 'bar',
                    data: {
                        labels: sortedByProjects.map(row => row.name),
                        datasets: [{
                            label: 'Completed Projects',
                            data: sortedByProjects.map(row => row.completed_projects || 0),
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

                // Revenue Breakdown Chart (Gross vs Net)
                let sortedByRevenue = [...rows]
                    .sort((a, b) => (b.gross_revenue || 0) - (a.gross_revenue || 0))
                    .slice(0, 10);

                const ctxStatus = document.getElementById('projectStatusChart').getContext('2d');
                projectStatusChart = new Chart(ctxStatus, {
                    type: 'bar',
                    data: {
                        labels: sortedByRevenue.map(row => row.name),
                        datasets: [
                            {
                                label: 'Gross Revenue',
                                data: sortedByRevenue.map(row => row.gross_revenue || 0),
                                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Net Revenue',
                                data: sortedByRevenue.map(row => row.net_revenue || 0),
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
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

                // Gross Revenue Chart (Top 10)
                const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
                revenueChart = new Chart(ctxRevenue, {
                    type: 'bar',
                    data: {
                        labels: sortedByRevenue.map(row => row.name),
                        datasets: [{
                            label: 'Gross Revenue ($)',
                            data: sortedByRevenue.map(row => row.gross_revenue || 0),
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

                // Average Completion Time Chart (Top 10) - exclude consultants with null avg_completion_days
                let sortedByCompletion = [...rows]
                    .filter(row => row.avg_completion_days !== null && row.avg_completion_days !== undefined)
                    .sort((a, b) => (a.avg_completion_days || 0) - (b.avg_completion_days || 0))
                    .slice(0, 10);

                const ctxCompletion = document.getElementById('completionRateChart').getContext('2d');
                completionRateChart = new Chart(ctxCompletion, {
                    type: 'bar',
                    data: {
                        labels: sortedByCompletion.map(row => row.name),
                        datasets: [{
                            label: 'Avg Completion Time (Days)',
                            data: sortedByCompletion.map(row => row.avg_completion_days || 0),
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

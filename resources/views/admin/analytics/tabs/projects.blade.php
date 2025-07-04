<!-- Project Analytics Tab Content -->
<div class="row">
    <!-- Project Status Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Project Status Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="projectStatusChart" class="small-chart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Property Type Breakdown -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building mr-2"></i>
                    Property Type Breakdown
                </h3>
            </div>
            <div class="card-body">
                <canvas id="propertyTypeChart" class="small-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Project Trends -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Monthly Project Trends
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm mr-2" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-period="monthly">Monthly</button>
                        <button type="button" class="btn btn-outline-primary" data-period="weekly">Weekly</button>
                        <button type="button" class="btn btn-outline-primary" data-period="quarterly">Quarterly</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#projectDetailsModal">
                        <i class="fas fa-search-plus mr-1"></i> Detailed Analysis
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendsChart" class="analytics-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Backlog Analysis -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Project Backlog
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Overdue</span>
                                <span class="info-box-number">{{ $projectAnalytics['backlog_analysis']['overdue'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-calendar-week"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Due This Week</span>
                                <span class="info-box-number">{{ $projectAnalytics['backlog_analysis']['due_this_week'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Due This Month</span>
                                <span class="info-box-number">{{ $projectAnalytics['backlog_analysis']['due_this_month'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Geographic Distribution -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Geographic Distribution
                </h3>
            </div>
            <div class="card-body">
                @if(!empty($projectAnalytics['geographic_distribution']))
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Projects</th>
                                    <th>Percentage</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = array_sum($projectAnalytics['geographic_distribution']);
                                @endphp
                                @foreach($projectAnalytics['geographic_distribution'] as $location => $count)
                                    @php
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $location ?: 'Unknown' }}</td>
                                        <td>{{ $count }}</td>
                                        <td>{{ $percentage }}%</td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <p>No geographic data available</p>
                        <small>Location data will appear here once projects have address information.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Property Type Chart
$(document).ready(function() {
    const propertyTypeCtx = document.getElementById('propertyTypeChart');
    if (propertyTypeCtx) {
        const propertyData = @json($projectAnalytics['property_type_breakdown']);
        
        if (Object.keys(propertyData).length > 0) {
            new Chart(propertyTypeCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(propertyData),
                    datasets: [{
                        label: 'Projects',
                        data: Object.values(propertyData),
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#2ecc71', '#f39c12', 
                            '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
    
    // Period filter for trends
    $('[data-period]').click(function() {
        const period = $(this).data('period');
        $('[data-period]').removeClass('active');
        $(this).addClass('active');
        
        // Load new trend data
        $.get('{{ route("admin.analytics.project-trends") }}', { period: period })
            .done(function(data) {
                updateTrendsChart(data);
            });
    });
});

function updateTrendsChart(data) {
    // Implementation for updating the trends chart with new data
    console.log('Updating trends chart with period data:', data);
}
</script>

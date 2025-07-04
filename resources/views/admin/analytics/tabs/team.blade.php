<!-- Team Performance Tab Content -->
<div class="row">
    <!-- Team Efficiency Overview -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Team Efficiency Overview
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Completion Rate</span>
                                <span class="info-box-number">{{ $teamMetrics['team_efficiency']['completion_rate'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Avg Processing Time</span>
                                <span class="info-box-number">{{ $teamMetrics['team_efficiency']['avg_processing_time'] ?? 0 }} days</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Productivity Score</span>
                                <span class="info-box-number">{{ $teamMetrics['team_efficiency']['productivity_score'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-tachometer-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Capacity Utilization</span>
                                <span class="info-box-number">{{ $teamMetrics['capacity_utilization']['current_utilization'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Consultant Workload -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Consultant Workload Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="consultantWorkloadChart" class="small-chart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Team Capacity -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users-cog mr-2"></i>
                    Team Capacity Analysis
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="metric-card text-center">
                            <div class="metric-value">{{ $teamMetrics['capacity_utilization']['active_consultants'] ?? 0 }}</div>
                            <div class="metric-label">Active Consultants</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card text-center">
                            <div class="metric-value">{{ $teamMetrics['capacity_utilization']['active_projects'] ?? 0 }}</div>
                            <div class="metric-label">Active Projects</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>Capacity Utilization</h6>
                    <div class="progress mb-2">
                        <div class="progress-bar 
                            @if(($teamMetrics['capacity_utilization']['current_utilization'] ?? 0) > 80) 
                                bg-danger 
                            @elseif(($teamMetrics['capacity_utilization']['current_utilization'] ?? 0) > 60) 
                                bg-warning 
                            @else 
                                bg-success 
                            @endif" 
                            style="width: {{ $teamMetrics['capacity_utilization']['current_utilization'] ?? 0 }}%">
                            {{ $teamMetrics['capacity_utilization']['current_utilization'] ?? 0 }}%
                        </div>
                    </div>
                    <small class="text-muted">
                        Total Capacity: {{ $teamMetrics['capacity_utilization']['total_capacity'] ?? 0 }} projects
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Consultant Performance Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Individual Consultant Performance
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="refreshConsultantData">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(!empty($teamMetrics['consultant_performance']))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Consultant</th>
                                    <th>Total Projects</th>
                                    <th>Completed</th>
                                    <th>Completion Rate</th>
                                    <th>Avg Completion Time</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamMetrics['consultant_performance'] as $consultant)
                                    @php
                                        $completionRate = $consultant['total_projects'] > 0 
                                            ? round(($consultant['completed_projects'] / $consultant['total_projects']) * 100, 1) 
                                            : 0;
                                        $avgDays = round($consultant['avg_completion_days'] ?? 0, 1);
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $consultant['name'] }}</strong>
                                        </td>
                                        <td>{{ $consultant['total_projects'] }}</td>
                                        <td>{{ $consultant['completed_projects'] }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($completionRate >= 80) badge-success 
                                                @elseif($completionRate >= 60) badge-warning 
                                                @else badge-danger 
                                                @endif">
                                                {{ $completionRate }}%
                                            </span>
                                        </td>
                                        <td>{{ $avgDays }} days</td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar 
                                                    @if($completionRate >= 80) bg-success 
                                                    @elseif($completionRate >= 60) bg-warning 
                                                    @else bg-danger 
                                                    @endif" 
                                                    style="width: {{ $completionRate }}%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>No consultant performance data available</p>
                        <small>Performance metrics will appear here once consultants have assigned projects.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Consultant Workload Chart
    const workloadCtx = document.getElementById('consultantWorkloadChart');
    if (workloadCtx) {
        const workloadData = @json($teamMetrics['consultant_workload']);
        
        if (Object.keys(workloadData).length > 0) {
            new Chart(workloadCtx, {
                type: 'horizontalBar',
                data: {
                    labels: Object.keys(workloadData),
                    datasets: [{
                        label: 'Active Projects',
                        data: Object.values(workloadData),
                        backgroundColor: '#3498db',
                        borderColor: '#2980b9',
                        borderWidth: 1
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
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
    
    // Refresh consultant data
    $('#refreshConsultantData').click(function() {
        const btn = $(this);
        btn.prop('disabled', true);
        btn.find('i').addClass('fa-spin');
        
        $.get('{{ route("admin.analytics.consultant-metrics") }}')
            .done(function(data) {
                // Update the consultant performance data
                location.reload(); // Simple refresh for now
            })
            .always(function() {
                btn.prop('disabled', false);
                btn.find('i').removeClass('fa-spin');
            });
    });
});
</script>

<!-- System Health Tab Content -->
<div class="row">
    <!-- Database Statistics -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database mr-2"></i>
                    Database Statistics
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($systemHealth['database_stats']['total_records'] as $table => $count)
                        <div class="col-6 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-{{ $table === 'hb837' ? 'project-diagram' : ($table === 'users' ? 'users' : 'table') }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ ucfirst($table) }}</span>
                                    <span class="info-box-number">{{ number_format($count) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <hr>
                
                <h6>Data Growth Metrics</h6>
                <div class="row">
                    <div class="col-4">
                        <div class="metric-card text-center">
                            <div class="metric-value 
                                @if(($systemHealth['database_stats']['growth_metrics']['monthly_growth'] ?? 0) > 0) 
                                    trend-up 
                                @else 
                                    trend-down 
                                @endif">
                                {{ $systemHealth['database_stats']['growth_metrics']['monthly_growth'] ?? 0 }}%
                            </div>
                            <div class="metric-label">Monthly Growth</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="metric-card text-center">
                            <div class="metric-value">{{ $systemHealth['database_stats']['growth_metrics']['current_month'] ?? 0 }}</div>
                            <div class="metric-label">This Month</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="metric-card text-center">
                            <div class="metric-value">{{ $systemHealth['database_stats']['growth_metrics']['last_month'] ?? 0 }}</div>
                            <div class="metric-label">Last Month</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Backup Compliance -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Backup Compliance
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon 
                                @if(($systemHealth['backup_compliance']['backup_health'] ?? 0) > 80) 
                                    bg-success 
                                @elseif(($systemHealth['backup_compliance']['backup_health'] ?? 0) > 60) 
                                    bg-warning 
                                @else 
                                    bg-danger 
                                @endif">
                                <i class="fas fa-heartbeat"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Backup Health Score</span>
                                <span class="info-box-number">{{ $systemHealth['backup_compliance']['backup_health'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <strong>Last Backup:</strong><br>
                        <span class="text-muted">{{ $systemHealth['backup_compliance']['last_backup'] ?? 'Never' }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Monthly Backups:</strong><br>
                        <span class="text-muted">{{ $systemHealth['backup_compliance']['monthly_backups'] ?? 0 }}</span>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <strong>Storage Used:</strong><br>
                        <span class="text-muted">{{ number_format(($systemHealth['backup_compliance']['storage_used'] ?? 0) / 1024 / 1024, 2) }} MB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Import/Export Activity -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Import/Export Activity
                </h3>
            </div>
            <div class="card-body">
                @if(!empty($systemHealth['import_export_activity']['monthly_stats']))
                    <canvas id="importExportChart" class="small-chart"></canvas>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                        <p>No import/export activity data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- User Activity -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    User Activity
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Users</span>
                                <span class="info-box-number">{{ $systemHealth['user_activity']['total_users'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Users</span>
                                <span class="info-box-number">{{ $systemHealth['user_activity']['active_users'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-user-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">New This Month</span>
                                <span class="info-box-number">{{ $systemHealth['user_activity']['new_users_this_month'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Import/Export Activity -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Recent Import/Export Activity
                </h3>
            </div>
            <div class="card-body">
                @if(!empty($systemHealth['import_export_activity']['recent_activity']))
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($systemHealth['import_export_activity']['recent_activity'] as $activity)
                                    <tr>
                                        <td>
                                            <span class="badge 
                                                @if($activity['type'] === 'import') badge-primary 
                                                @elseif($activity['type'] === 'backup') badge-success 
                                                @else badge-info 
                                                @endif">
                                                {{ ucfirst($activity['type']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(isset($activity['changes']['filename']))
                                                {{ $activity['changes']['filename'] }}
                                            @else
                                                {{ $activity['type'] }} operation
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($activity['created_at'])->format('M j, Y g:i A') }}</td>
                                        <td>
                                            @if(isset($activity['changes']['status']))
                                                <span class="badge 
                                                    @if($activity['changes']['status'] === 'completed') badge-success 
                                                    @elseif($activity['changes']['status'] === 'failed') badge-danger 
                                                    @else badge-warning 
                                                    @endif">
                                                    {{ ucfirst($activity['changes']['status']) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history fa-3x mb-3"></i>
                        <p>No recent activity to display</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Import/Export Activity Chart
    const activityCtx = document.getElementById('importExportChart');
    if (activityCtx) {
        const activityData = @json($systemHealth['import_export_activity']['monthly_stats']);
        
        if (activityData && activityData.length > 0) {
            const labels = activityData.map(item => item.month);
            const importsData = activityData.map(item => item.imports);
            const backupsData = activityData.map(item => item.backups);
            
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Imports',
                        data: importsData,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Backups',
                        data: backupsData,
                        borderColor: '#27ae60',
                        backgroundColor: 'rgba(39, 174, 96, 0.1)',
                        tension: 0.4
                    }]
                },
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
    }
});
</script>

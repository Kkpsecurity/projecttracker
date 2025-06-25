<!-- Monthly Backup & Import Activity -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="text-muted mb-0">
                    <i class="fa fa-chart-line me-2"></i>Monthly Backup & Import Activity
                </h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active" onclick="toggleChart('activity')">
                        Activity
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="toggleChart('volume')">
                        Data Volume
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if(!empty($systemStats['monthly_trends'])): ?>
                    <!-- Activity Chart -->
                    <div id="activityChart" class="chart-container">
                        <canvas id="activityCanvas" width="400" height="100"></canvas>
                    </div>

                    <!-- Volume Chart (hidden by default) -->
                    <div id="volumeChart" class="chart-container d-none">
                        <div class="text-center text-muted py-3">
                            <p>Data volume trends showing monthly record changes</p>
                        </div>
                    </div>

                    <!-- Simple table for trends -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-center">Backups</th>
                                            <th class="text-center">Imports</th>
                                            <th class="text-center">Data Volume</th>
                                            <th class="text-center">Activity Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $systemStats['monthly_trends']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $totalActivity = $trend['backups_created'] + $trend['imports_performed'];
                                                $activityLevel = $totalActivity > 5 ? 'High' : ($totalActivity > 2 ? 'Medium' : 'Low');
                                                $activityColor = $totalActivity > 5 ? 'success' : ($totalActivity > 2 ? 'warning' : 'secondary');
                                            ?>
                                            <tr>
                                                <td><?php echo e($trend['month']); ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?php echo e($trend['backups_created']); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info"><?php echo e($trend['imports_performed']); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary"><?php echo e($trend['data_volume']); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?php echo e($activityColor); ?>"><?php echo e($activityLevel); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-chart-line fa-3x mb-3"></i>
                        <p>No activity data available</p>
                        <small>Data will appear as backups and imports are performed</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleChart(type) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Toggle chart visibility
    if (type === 'activity') {
        document.getElementById('activityChart').classList.remove('d-none');
        document.getElementById('volumeChart').classList.add('d-none');
    } else {
        document.getElementById('activityChart').classList.add('d-none');
        document.getElementById('volumeChart').classList.remove('d-none');
    }
}

// Simple canvas chart for backup/import activity
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('activityCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const data = <?php echo json_encode($systemStats['monthly_trends'] ?? [], 15, 512) ?>;

        if (data.length > 0) {
            // Simple bar chart implementation
            const maxValue = Math.max(...data.map(d => Math.max(d.backups_created, d.imports_performed)));
            const chartHeight = 80;
            const chartWidth = canvas.width - 80;
            const barWidth = chartWidth / (data.length * 2);

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw bars
            data.forEach((item, index) => {
                const x = 40 + (index * barWidth * 2);
                const backupHeight = (item.backups_created / Math.max(maxValue, 1)) * chartHeight;
                const importHeight = (item.imports_performed / Math.max(maxValue, 1)) * chartHeight;

                // Backup bars (blue)
                ctx.fillStyle = '#0d6efd';
                ctx.fillRect(x, canvas.height - backupHeight - 20, barWidth * 0.8, backupHeight);

                // Import bars (teal)
                ctx.fillStyle = '#17a2b8';
                ctx.fillRect(x + barWidth * 0.8, canvas.height - importHeight - 20, barWidth * 0.8, importHeight);

                // Month labels
                ctx.fillStyle = '#666';
                ctx.font = '10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(item.month.split(' ')[0], x + barWidth * 0.8, canvas.height - 5);
            });

            // Legend
            ctx.fillStyle = '#0d6efd';
            ctx.fillRect(10, 10, 12, 8);
            ctx.fillStyle = '#666';
            ctx.font = '10px Arial';
            ctx.textAlign = 'left';
            ctx.fillText('Backups', 25, 18);

            ctx.fillStyle = '#17a2b8';
            ctx.fillRect(80, 10, 12, 8);
            ctx.fillText('Imports', 95, 18);
        }
    }
});
</script>
<?php /**PATH /var/www/projecttracker/resources/views/admin/services/backup/monthly_trends_new.blade.php ENDPATH**/ ?>
<!-- Monthly Trends Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="text-muted mb-0">
                    <i class="fa fa-chart-line me-2"></i>Monthly Project Trends
                </h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active" onclick="toggleChart('projects')">
                        Projects
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="toggleChart('financial')">
                        Financial
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if(!empty($systemStats['monthly_trends'])): ?>
                    <!-- Projects Chart -->
                    <div id="projectsChart" class="chart-container">
                        <canvas id="projectsCanvas" width="400" height="100"></canvas>
                    </div>

                    <!-- Financial Chart (hidden by default) -->
                    <div id="financialChart" class="chart-container d-none">
                        <div class="text-center text-muted py-5">
                            <i class="fa fa-dollar-sign fa-3x mb-3"></i>
                            <p>Financial trends will be available when Chart.js is included</p>
                            <small>Consider adding Chart.js for interactive financial charts</small>
                        </div>
                    </div>

                    <!-- Simple table fallback for trends -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-center">Created</th>
                                            <th class="text-center">Completed</th>
                                            <th class="text-center">Completion Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $systemStats['monthly_trends']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $completionRate = $trend['created'] > 0
                                                    ? round(($trend['completed'] / $trend['created']) * 100, 1)
                                                    : 0;
                                            ?>
                                            <tr>
                                                <td><?php echo e($trend['month']); ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?php echo e($trend['created']); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success"><?php echo e($trend['completed']); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar bg-success" style="width: <?php echo e($completionRate); ?>%"></div>
                                                        </div>
                                                        <small class="text-muted"><?php echo e($completionRate); ?>%</small>
                                                    </div>
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
                        <p>No trend data available</p>
                        <small>Data will appear as projects are created and completed</small>
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
    if (type === 'projects') {
        document.getElementById('projectsChart').classList.remove('d-none');
        document.getElementById('financialChart').classList.add('d-none');
    } else {
        document.getElementById('projectsChart').classList.add('d-none');
        document.getElementById('financialChart').classList.remove('d-none');
    }
}

// Simple canvas chart for projects (basic implementation)
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('projectsCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const data = <?php echo json_encode($systemStats['monthly_trends'] ?? [], 15, 512) ?>;

        if (data.length > 0) {
            // Simple bar chart implementation
            const maxValue = Math.max(...data.map(d => Math.max(d.created, d.completed)));
            const chartHeight = 80;
            const chartWidth = canvas.width - 80;
            const barWidth = chartWidth / (data.length * 2);

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw bars
            data.forEach((item, index) => {
                const x = 40 + (index * barWidth * 2);
                const createdHeight = (item.created / maxValue) * chartHeight;
                const completedHeight = (item.completed / maxValue) * chartHeight;

                // Created bars (blue)
                ctx.fillStyle = '#0d6efd';
                ctx.fillRect(x, canvas.height - createdHeight - 20, barWidth * 0.8, createdHeight);

                // Completed bars (green)
                ctx.fillStyle = '#198754';
                ctx.fillRect(x + barWidth * 0.8, canvas.height - completedHeight - 20, barWidth * 0.8, completedHeight);

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
            ctx.fillText('Created', 25, 18);

            ctx.fillStyle = '#198754';
            ctx.fillRect(80, 10, 12, 8);
            ctx.fillText('Completed', 95, 18);
        }
    }
});
</script>
<?php /**PATH /var/www/projecttracker/resources/views/admin/services/backup/monthly_trends.blade.php ENDPATH**/ ?>
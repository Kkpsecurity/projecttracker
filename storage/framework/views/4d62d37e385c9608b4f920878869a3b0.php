<?php $__env->startSection('title', 'Dashboard | Project Tracker'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <!-- Total Projects Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo e($stats['total_projects']); ?></h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="<?php echo e(route('admin.hb837.index')); ?>" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Active Projects Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo e($stats['active_projects']); ?></h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <a href="<?php echo e(route('admin.home.tabs', ['tab' => 'active'])); ?>" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Completed Projects Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo e($stats['completed_projects']); ?></h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="<?php echo e(route('admin.home.tabs', ['tab' => 'completed'])); ?>" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Consultants Card -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo e($stats['total_consultants']); ?></h3>
                    <p>Consultants</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="<?php echo e(route('admin.consultants.index')); ?>" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Projects Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Projects</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Property ID</th>
                                <th>Owner Name</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $stats['recent_projects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($project->property_id); ?></td>
                                    <td><?php echo e($project->owner_name); ?></td>
                                    <td><?php echo e($project->property_address); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo e($project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'warning' : 'secondary')); ?>">
                                            <?php echo e(ucfirst($project->status ?? 'pending')); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($project->created_at?->format('M d, Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No projects found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <a href="<?php echo e(route('admin.hb837.index')); ?>" class="btn btn-sm btn-info float-left">View All Projects</a>
                    <a href="<?php echo e(route('admin.hb837.create')); ?>" class="btn btn-sm btn-success float-right">Add New Project</a>
                </div>
            </div>
        </div>

        <!-- Recent Backups -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Backups</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $stats['recent_backups']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo e($backup->name); ?></strong><br>
                                        <small class="text-muted"><?php echo e($backup->created_at?->diffForHumans()); ?></small>
                                    </div>
                                    <span class="badge badge-<?php echo e($backup->status == 'completed' ? 'success' : ($backup->status == 'failed' ? 'danger' : 'warning')); ?>">
                                        <?php echo e(ucfirst($backup->status)); ?>

                                    </span>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item text-center">No backups found</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="<?php echo e(route('admin.hb837.backup.dashboard')); ?>" class="btn btn-sm btn-primary btn-block">Manage Backups</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.hb837.create')); ?>" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Add New Project
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.hb837.import')); ?>" class="btn btn-info btn-block">
                                <i class="fas fa-upload"></i> Import Data
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.services.backup')); ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-database"></i> Create Backup
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.hb837.report')); ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-file-pdf"></i> Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .small-box .icon {
            top: 10px;
        }

        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }

        .badge {
            font-size: 0.75em;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        console.log('AdminLTE Dashboard loaded successfully!');

        // Add any dashboard-specific JavaScript here
        $(document).ready(function() {
            // Initialize any dashboard widgets or charts
            console.log('Dashboard statistics:', <?php echo json_encode($stats, 15, 512) ?>);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>
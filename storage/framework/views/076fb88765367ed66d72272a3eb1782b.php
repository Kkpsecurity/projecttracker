

<?php $__env->startSection('title', 'HB837 Management'); ?>

<?php $__env->startSection('content_header_content'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">HB837 Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">HB837</li>
            </ol>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <?php
        $active_tab = Request()->segment(4) ?? 'Active';
        $tabs = [
            'Active' => [
                'icon' => 'fas fa-play-circle',
                'name' => 'Active',
                'class' => 'btn-success'
            ],
            'Quoted' => [
                'icon' => 'fas fa-quote-right',
                'name' => 'Quoted',
                'class' => 'btn-warning'
            ],
            'Completed' => [
                'icon' => 'fas fa-check-circle',
                'name' => 'Completed',
                'class' => 'btn-primary'
            ],
            'Closed' => [
                'icon' => 'fas fa-times-circle',
                'name' => 'Closed',
                'class' => 'btn-secondary'
            ],
        ];
    ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="<?php echo e($tabs[$active_tab]['icon']); ?>"></i>
                                <?php echo e($tabs[$active_tab]['name']); ?> Records
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <a href="<?php echo e(route('admin.hb837.create')); ?>" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add New Record
                                </a>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#export_import_modal">
                                    <i class="fas fa-exchange-alt"></i> Import/Export
                                </button>
                                <a href="<?php echo e(route('admin.hb837.backup.dashboard')); ?>" class="btn btn-warning">
                                    <i class="fas fa-database"></i> Backup
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.hb837.tabs', ['tab' => $key])); ?>" 
                                       class="nav-link <?php echo e($active_tab == $key ? 'active' : ''); ?>">
                                        <i class="<?php echo e($tab['icon']); ?>"></i>
                                        <?php echo e($tab['name']); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <!-- Records Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="hb837-table">
                            <thead>
                                <tr>
                                    <th>Property ID</th>
                                    <th>Property Name</th>
                                    <th>Owner Name</th>
                                    <th>Address</th>
                                    <th>Consultant</th>
                                    <th>Status</th>
                                    <th>Crime Risk</th>
                                    <th>Updated</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $collection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong>#<?php echo e($record->id); ?></strong></td>
                                        <td><?php echo e($record->property_name); ?></td>
                                        <td><?php echo e($record->owner_name); ?></td>
                                        <td>
                                            <small>
                                                <?php echo e($record->address); ?><br>
                                                <?php echo e($record->city); ?>, <?php echo e($record->state); ?> <?php echo e($record->zip); ?>

                                            </small>
                                        </td>
                                        <td>
                                            <?php if($record->consultant): ?>
                                                <span class="badge badge-info"><?php echo e($record->consultant->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo e(strtolower($record->report_status)); ?>">
                                                <?php echo e($record->report_status); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($record->securitygauge_crime_risk): ?>
                                                <span class="risk-<?php echo e(strtolower($record->securitygauge_crime_risk)); ?>">
                                                    <i class="fas fa-circle"></i>
                                                    <?php echo e(ucfirst($record->securitygauge_crime_risk)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo e($record->updated_at->diffForHumans()); ?>

                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo e(route('admin.hb837.edit', $record->id)); ?>" 
                                                   class="btn btn-info btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.hb837.report', $record->id)); ?>" 
                                                   class="btn btn-success btn-sm" title="Report">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.hb837.destroy', $record->id)); ?>" 
                                                      method="POST" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <br>
                                            No HB837 records found for this status.
                                            <br>
                                            <a href="<?php echo e(route('admin.hb837.create')); ?>" class="btn btn-success btn-sm mt-2">
                                                <i class="fas fa-plus"></i> Add First Record
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if(isset($collection) && method_exists($collection, 'hasPages') && $collection->hasPages()): ?>
                    <div class="card-footer">
                        <?php echo e($collection->appends(request()->query())->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mt-3">
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Records</span>
                    <span class="info-box-number"><?php echo e($stats['total'] ?? 0); ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-play-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active</span>
                    <span class="info-box-number"><?php echo e($stats['active'] ?? 0); ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-quote-right"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quoted</span>
                    <span class="info-box-number"><?php echo e($stats['quoted'] ?? 0); ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number"><?php echo e($stats['completed'] ?? 0); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the existing modals -->
    <?php echo $__env->make('partials.modals.export_import_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_css'); ?>
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .nav-tabs-custom {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs .nav-link {
            border-radius: 0;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #007bff;
            color: #007bff;
        }
        
        .nav-tabs .nav-link.active {
            background-color: transparent;
            color: #007bff;
            border-bottom-color: #007bff;
            font-weight: bold;
        }
        
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .btn-group-sm > .btn {
            margin: 0 1px;
        }
        
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #28a745; color: white; }
        .status-quoted { background-color: #ffc107; color: #212529; }
        .status-completed { background-color: #007bff; color: white; }
        .status-closed { background-color: #dc3545; color: white; }
        
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_js'); ?>
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with basic features
            $('#hb837-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": false, // Disable DataTable pagination since we're using Laravel pagination
                "order": [[ 7, "desc" ]], // Sort by updated column by default
                "columnDefs": [
                    { "orderable": false, "targets": 8 } // Disable ordering on actions column
                ]
            });
            
            // Confirm deletion
            $(document).on('click', '.btn-delete', function(e) {
                if (!confirm('Are you sure you want to delete this HB837 record? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Auto-refresh every 5 minutes
            setInterval(function() {
                if (!$('.modal').hasClass('show')) { // Don't refresh if modal is open
                    window.location.reload();
                }
            }, 300000); // 5 minutes
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/hb837/hb837_new.blade.php ENDPATH**/ ?>
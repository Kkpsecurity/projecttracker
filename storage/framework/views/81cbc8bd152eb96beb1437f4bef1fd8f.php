

<?php $__env->startSection('title', 'Database Backup & Services'); ?>

<?php $__env->startSection('content_header                                                       <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="fas fa-upload"></i> Import Data
                                    </button>              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#backupModal">
                                        <i class="fas fa-save"></i> Create Backup
                                    </button>ntent'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Database Backup & Services</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Backup & Services</li>
            </ol>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
    <!-- System Status Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo e($stats['total_backups'] ?? 0); ?></h3>
                    <p>Total Backups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="#backup-history" class="small-box-footer">
                    View History <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo e($systemStats['db_size'] ?? 'N/A'); ?></h3>
                    <p>Database Size</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <a href="#system-info" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo e($systemStats['hb837_records'] ?? 0); ?></h3>
                    <p>HB837 Records</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="<?php echo e(route('admin.hb837.index')); ?>" class="small-box-footer">
                    View Records <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo e($stats['recent_backups'] ?? 0); ?></h3>
                    <p>Recent Backups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#recent-activity" class="small-box-footer">
                    View Activity <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i>
                        Backup Operations
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Cards removed - buttons moved to header area -->
                </div>
            </div>
        </div>
    </div>

    <!-- Backup History -->
    <div class="row" id="backup-history">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Backup History
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#backupModal">
                                <i class="fas fa-database"></i> Create Backup
                            </button>
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#export_import_modal">
                                <i class="fas fa-file-excel"></i> Import Data
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive-modern">
                        <table class="table table-modern table-compact">
                            <thead>
                                <tr>
                                    <th>Backup Name</th>
                                    <th style="width: 120px;">Created By</th>
                                    <th style="width: 140px;">Created At</th>
                                    <th style="width: 100px;">File Size</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($backup->name ?: 'Backup_' . $backup->id); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($backup->filename); ?></small>
                                        </td>
                                        <td>
                                            <?php echo e($backup->user->name ?? 'System'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($backup->created_at->format('M d, Y H:i')); ?>

                                            <br>
                                            <small class="text-muted"><?php echo e($backup->created_at->diffForHumans()); ?></small>
                                        </td>
                                        <td>
                                            <?php if($backup->size): ?>
                                                <?php echo e(number_format($backup->size / 1024, 2)); ?> KB
                                            <?php else: ?>
                                                <span class="text-muted">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($backup->status === 'completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php elseif($backup->status === 'failed'): ?>
                                                <span class="badge badge-danger">Failed</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Processing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if($backup->status === 'completed'): ?>
                                                    <a href="<?php echo e(route('admin.hb837.backup.download', $backup->filename)); ?>" 
                                                       class="btn btn-info btn-sm" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#restoreModal" 
                                                            data-backup-id="<?php echo e($backup->uuid); ?>" 
                                                            data-backup-name="<?php echo e($backup->name ?: 'Backup_' . $backup->id); ?>"
                                                            data-backup-filename="<?php echo e($backup->filename); ?>"
                                                            title="Restore">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <form action="<?php echo e(route('admin.hb837.backup.delete_file', $backup->id)); ?>" 
                                                      method="POST" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-database fa-3x mb-3"></i>
                                            <br>
                                            No backups found. Create your first backup to get started.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if($backups->hasPages()): ?>
                    <div class="card-footer">
                        <?php echo e($backups->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Import Activity -->
    <?php if(isset($importAudits) && $importAudits->count() > 0): ?>
    <div class="row mt-3" id="recent-activity">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i>
                        Recent Import Activity
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Import Date</th>
                                    <th>User</th>
                                    <th>Records Imported</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $importAudits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($audit->created_at->format('M d, Y H:i')); ?></td>
                                        <td><?php echo e($audit->user->name ?? 'System'); ?></td>
                                        <td><?php echo e($audit->records_processed ?? 0); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($audit->status === 'success' ? 'success' : 'danger'); ?>">
                                                <?php echo e(ucfirst($audit->status)); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Include Modals -->
    <?php echo $__env->make('partials.modals.backup_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.modals.restore_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.modals.export_import_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_css'); ?>
    <style>
        .card-outline {
            border-top: 3px solid;
        }
        
        .card-outline.card-primary {
            border-top-color: #007bff;
        }
        
        .card-outline.card-success {
            border-top-color: #28a745;
        }
        
        .card-outline.card-warning {
            border-top-color: #ffc107;
        }
        
        .small-box .icon {
            top: -10px;
            font-size: 70px;
        }
        
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_js'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap 5 modals explicitly
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modalElement => {
                new bootstrap.Modal(modalElement);
            });
            
            // Handle restore modal data with vanilla JS
            const restoreModal = document.getElementById('restoreModal');
            if (restoreModal) {
                restoreModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    
                    if (!button) {
                        return;
                    }
                    
                    const backupId = button.getAttribute('data-backup-id');
                    const backupName = button.getAttribute('data-backup-name') || 'Backup';
                    const backupFilename = button.getAttribute('data-backup-filename') || 'backup.sql';
                    
                    if (backupId) {
                        this.querySelector('#restore_backup_id').value = backupId;
                        this.querySelector('#restore-backup-name').textContent = backupName;
                        this.querySelector('#restore-backup-filename').textContent = backupFilename;
                        
                        // Update the form action URL
                        const restoreUrl = "<?php echo e(route('admin.hb837.backup.restore', ':uuid')); ?>".replace(':uuid', backupId);
                        this.querySelector('#restore-form').setAttribute('action', restoreUrl);
                    }
                });
            }
        });
        
        $(document).ready(function() {
            // Fallback jQuery handlers for compatibility
            $('#restoreModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                
                if (button.length === 0) {
                    return;
                }
                
                var backupId = button.data('backup-id');
                var backupName = button.data('backup-name') || 'Backup';
                var backupFilename = button.data('backup-filename') || 'backup.sql';
                
                if (backupId) {
                    $(this).find('#restore_backup_id').val(backupId);
                    $(this).find('#restore-backup-name').text(backupName);
                    $(this).find('#restore-backup-filename').text(backupFilename);
                    
                    // Update the form action URL
                    var restoreUrl = "<?php echo e(route('admin.hb837.backup.restore', ':uuid')); ?>".replace(':uuid', backupId);
                    $(this).find('#restore-form').attr('action', restoreUrl);
                }
            });
            
            // Confirm deletion
            $(document).on('click', '.btn-delete', function(e) {
                if (!confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Auto-refresh status every 30 seconds
            setInterval(function() {
                if (!$('.modal').hasClass('show')) { // Don't refresh if modal is open
                    $('.badge-warning').closest('tr').each(function() {
                        // Only refresh processing backups
                        if ($(this).find('.badge-warning').text().trim() === 'Processing') {
                            window.location.reload();
                        }
                    });
                }
            }, 30000);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/services/backup/index_new.blade.php ENDPATH**/ ?>
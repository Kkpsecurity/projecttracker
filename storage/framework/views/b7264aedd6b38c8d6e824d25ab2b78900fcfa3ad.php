<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">
            <i class="fas fa-clock-rotate-left me-2 text-secondary"></i>Backup History
        </h5>
        <div>
            <button class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <div class="card-body">
        <?php echo $__env->make('partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php if(!$backups || $backups->isEmpty()): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-inbox fa-4x text-light" style="font-size: 5rem;"></i>
                </div>
                <h4 class="text-muted mb-2">No backups created yet</h4>
                <p class="text-muted mb-4">Your first backup will appear here</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#backupModal">
                    <i class="fas fa-plus me-1"></i>Create First Backup
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Backup Name</th>
                            <th>Created At</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('admin.services.backup.partials.item_row', ['backup' => $backup], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing <?php echo e($backups->firstItem()); ?> to <?php echo e($backups->lastItem()); ?> of <?php echo e($backups->total()); ?> entries
                </div>
                <div>
                    <?php echo e($backups->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if($backups && $backups->isNotEmpty()): ?>
    <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('partials.modals.backup_info_modal', ['backup' => $backup], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials.modals.restore_modal', ['backup' => $backup], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /var/www/projecttracker/resources/views/admin/services/backup/backup_history.blade.php ENDPATH**/ ?>
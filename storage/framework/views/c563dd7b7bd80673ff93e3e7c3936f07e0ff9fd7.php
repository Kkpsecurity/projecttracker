<div class="container">
    <h1 class="mb-4">📥 Import Audits</h1>
    <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-dark">#</th>
                    <th class="text-dark">User</th>
                    <th class="text-dark">Summary</th>
                    <th class="text-success">+ New</th>
                    <th class="text-dark">~ Updated</th>
                    <th class="text-dark">× Skipped</th>
                    <th class="text-dark">Started At</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $importAudits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($audit->user->name ?? 'System'); ?></td>
                        <td>
                            <span class="text-muted small">
                                Processed:
                                <?php echo e(($audit->changes['imported'] + $audit->changes['updated'] + $audit->changes['skipped']) ?? 0); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo e($audit->changes['imported'] ?? 0); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <?php echo e($audit->changes['updated'] ?? 0); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo e($audit->changes['skipped'] ?? 0); ?>

                            </span>
                        </td>
                        <td><i class="fa fa-calendar me-1"></i><?php echo e($audit->created_at ? $audit->created_at->format('Y-m-d H:i') : '-'); ?></td>

                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No import audits found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(method_exists($importAudits, 'links')): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($importAudits->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/projecttracker/resources/views/admin/services/backup/imports.blade.php ENDPATH**/ ?>
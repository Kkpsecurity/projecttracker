<tr class="backup-item">
    <td>
        <div class="d-flex align-items-center">
            <i class="fas fa-file-excel text-success fa-2x me-3"></i>
            <div>
                <strong><?php echo e($backup->name ?: 'Backup #' . $backup->id); ?></strong>
                <div class="text-muted small">ID: <?php echo e($backup->id); ?></div>
            </div>
        </div>
    </td>

    <td>
        <div><?php echo e($backup->created_at->format('M j, Y')); ?></div>
        <div class="text-muted small"><?php echo e($backup->created_at->format('g:i A')); ?></div>
    </td>

    <td><?php echo e(number_format($backup->size / 1024, 2)); ?> KB</td>

    <!--Displays what tables was backedup #?-->
    <td>
        <a href="<?php echo e(route('admin.hb837.backup.download', $backup->filename)); ?>" class="btn btn-sm btn-light text-success"
            title="Download">
            <i class="fa fa-download"></i>
        </a>
        <?php
            $tables = is_array($backup->tables)
                ? $backup->tables
                : (is_string($backup->tables)
                    ? array_map('trim', explode(',', $backup->tables))
                    : []);
        ?>

        <?php if(count($tables)): ?>
            <?php echo e(implode(' | ', $tables)); ?>

        <?php else: ?>
            <span class="text-muted">No tables</span>
        <?php endif; ?>
    </td>

    <td class="text-end">
        <div class="btn-group">

            <button class="btn btn-sm btn-light text-info" data-bs-toggle="modal"
                data-bs-target="#infoModal<?php echo e($backup->id); ?>" title="Details">
                <i class="fa fa-info-circle"></i>
            </button>
            <button class="btn btn-sm btn-light text-warn" data-bs-toggle="modal"
                data-bs-target="#restoreModal<?php echo e($backup->id); ?>" title="Restore">
                <i class="fa fa-refresh"></i>
            </button>
            <form action="<?php echo e(route('admin.hb837.backup.delete_file', $backup->uuid)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-light text-danger"
                    onclick="return confirm('Are you sure you want to delete this backup?')" title="Delete">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
<?php /**PATH /var/www/projecttracker/resources/views/admin/services/backup/partials/item_row.blade.php ENDPATH**/ ?>
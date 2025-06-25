<table class="table table-light table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Company Name</th>
            <th>Phone</th>
            <th class="text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($owner->id); ?></td>
                <td><?php echo e($owner->name); ?></td>
                <td><?php echo e($owner->email); ?></td>
                <td><?php echo e($owner->company_name); ?></td>
                <td><?php echo e($owner->phone); ?></td>
                <td class="text-right">
                    <form action="<?php echo e(route('admin.owners.destroy', $owner->id)); ?>" method="POST">
                        <a class="btn btn-primary" href="<?php echo e(route('admin.owners.edit', $owner->id)); ?>">Edit</a>
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this owner?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center">No Owner records found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if($owners->isNotEmpty()): ?>
    <?php echo $owners->links(); ?>

<?php endif; ?>
<?php /**PATH /var/www/projecttracker/resources/views/admin/owners/tables/owners.blade.php ENDPATH**/ ?>
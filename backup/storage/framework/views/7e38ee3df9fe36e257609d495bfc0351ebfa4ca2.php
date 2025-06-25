<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2>Owners List</h2>
                <div>
                    <a class="btn btn-primary" href="<?php echo e(route('admin.dashboard')); ?>">Back</a>
                    <a class="btn btn-success" href="<?php echo e(route('admin.owners.create')); ?>">Add New Owner</a>
                    <a class="btn btn-warning" href="<?php echo e(route('admin.owners.export')); ?>">Export</a>
                </div>
            </div>
        </div>

        <?php if($message = Session::get('success')): ?>
            <div class="alert alert-success">
                <p><?php echo e($message); ?></p>
            </div>
        <?php endif; ?>

        <?php echo $__env->make('admin.owners.tables.owners', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/owners/show.blade.php ENDPATH**/ ?>
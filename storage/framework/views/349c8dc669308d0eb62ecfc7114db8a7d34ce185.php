<?php $__env->startSection('content'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2 class="text-white">Consultants List</h2>
                <div>
                    <a class="btn btn-primary" href="<?php echo e(route('admin.hb837.index')); ?>">Back</a>
                    <a class="btn btn-success" href="<?php echo e(route('admin.consultants.create')); ?>">Add New Consultant</a>
                    <a class="btn btn-warning" href="<?php echo e(route('admin.consultants.export')); ?>">Export</a>
                </div>
            </div>
        </div>

        <?php if($message = Session::get('success')): ?>
            <div class="alert alert-success mt-3">
                <p><?php echo e($message); ?></p>
            </div>
        <?php endif; ?>

        <?php echo $__env->make('admin.consultants.tables.consultants', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/consultants/show.blade.php ENDPATH**/ ?>
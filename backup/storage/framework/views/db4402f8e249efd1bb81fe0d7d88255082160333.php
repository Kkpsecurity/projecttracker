<?php $__env->startSection('content'); ?>
    <style>
        body, .container {
            font-family: 'Arial', sans-serif;
            font-size: 1rem;
            line-height: 1.6;
            color: #343a40; /* Dark gray text for readability */
        }
        h2 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        .card {
            background-color: #ffffff; /* White background */
            color: #343a40; /* Dark text */
            font-size: 1rem;
            padding: 2rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            font-size: 1rem;
        }
        .alert {
            font-size: 1rem;
        }
    </style>

    <div class="container p-3">
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 style="color: #dee2e6">Add New Consultant</h2>
                    <a class="btn btn-primary" href="<?php echo e(route('admin.consultants.index')); ?>">Back</a>
                </div>
            </div>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card bg-light m-2 p-5 shadow">
            <form action="<?php echo e(route('admin.consultants.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('admin.consultants.forms.form', ['consultant' => new App\Models\Consultant()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/consultants/create.blade.php ENDPATH**/ ?>
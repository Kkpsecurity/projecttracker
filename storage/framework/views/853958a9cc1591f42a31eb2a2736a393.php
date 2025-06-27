<?php $__env->startSection('title', 'Create HB837 Record'); ?>

<?php $__env->startSection('content_header_content'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Create HB837 Record</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.hb837.index')); ?>">HB837</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php
    $hb837 = new App\Models\HB837();
    $property_types = config('hb837.property_types');
    $securitygauge_crime_risks = config('hb837.security_gauge');
    $consultants = App\Models\Consultant::all();
?>

<?php $__env->startSection('main_content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i>
                        New HB837 Record
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.hb837.index')); ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="<?php echo e(route('admin.hb837.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                               <?php echo $__env->make('admin.hb837.partials.general', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo e(route('admin.hb837.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Record
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/hb837/create-hb837.blade.php ENDPATH**/ ?>
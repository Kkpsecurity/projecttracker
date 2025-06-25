<?php $__env->startSection('styles'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
<?php $__env->stopSection(); ?>

<?php
    $hb837 = new App\Models\HB837();
    $property_types = config('hb837.property_types');
    $securitygauge_crime_risks = config('hb837.security_gauge');
    $consultants = App\Models\Consultant::all();
?>

<?php $__env->startSection('content'); ?>
<div class="container bg-light p-3">
    <h1>Create HB837 Record</h1>
    <form action="<?php echo e(route('admin.hb837.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="tab-content">
            <!-- General Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
               <?php echo $__env->make('admin.hb837.partials.general', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Submit</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/create-hb837.blade.php ENDPATH**/ ?>
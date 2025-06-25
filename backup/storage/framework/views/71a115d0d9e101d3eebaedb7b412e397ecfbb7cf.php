<?php $__env->startSection('content'); ?>
<div class="container bg-light p-4 rounded shadow">
     <div class="row">
         <div class="col-lg-12 margin-tb">
             <div class="pull-left">
                 <h2>Add New User</h2>
             </div>
             <div class="pull-right">
                 <a class="btn btn-primary" href="<?php echo e(url('admin/users')); ?>"> Back</a>
             </div>
         </div>
     </div>

     <?php if($errors->any()): ?>
     <div class="alert alert-danger">
         There were some problems with your input.<br><br>
         <ul>
             <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <li><?php echo e($error); ?></li>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         </ul>
     </div>
     <?php endif; ?>

     <form action="<?php echo e(url('admin/users/store')); ?>" method="POST">
         <?php echo csrf_field(); ?>

         <div class="row">
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Name:</strong>
                     <input type="text" name="name" class="form-control" placeholder="Name">
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
               <div class="form-group">
                   <strong>Email:</strong>
                   <input type="email" class="form-control" name="email" placeholder="Email" />
               </div>
           </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Password:</strong>
                     <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Confirm Password:</strong>
                     <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" />
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                 <button type="submit" class="btn btn-primary">Submit</button>
             </div>
         </div>

     </form>
 </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/users/create.blade.php ENDPATH**/ ?>
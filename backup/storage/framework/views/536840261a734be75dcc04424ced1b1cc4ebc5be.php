<?php $__env->startSection('content'); ?>
    <div class="container bg-light p-3">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Users</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="<?php echo e(url('admin/users/create')); ?>">Create New User</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?php if($message = Session::get('success')): ?>
                    <div class="alert alert-success">
                        <p><?php echo e($message); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($user->id); ?></td>
                                <td><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-warning" href="<?php echo e(url('admin/users/' . $user->id)); ?>/edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-sm btn-danger" href="#"
                                        onclick="confirmDelete(<?php echo e($user->id); ?>)">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <form id="delete-user-form-<?php echo e($user->id); ?>"
                                        action="<?php echo e(url('admin/users/delete/' . $user->id)); ?>" method="POST"
                                        style="display: none;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    <?php echo e($users->links()); ?>

                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                event.preventDefault();
                document.getElementById('delete-user-form-' + id).submit();
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/users/table_list.blade.php ENDPATH**/ ?>
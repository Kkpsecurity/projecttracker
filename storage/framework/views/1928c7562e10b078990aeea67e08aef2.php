<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
            <?php echo e(config('app.name', 'Laravel')); ?>

        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="<?php echo e(__('Toggle navigation')); ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse border" id="navbarSupportedContent">
            <?php if(auth()->guard()->check()): ?>
                <ul class="navbar-nav me-auto">
                    <?php if(Auth()->id() == 1 || Auth()->id() == 2): ?>
                        <li><a href="<?php echo e(url('admin/users')); ?>" class="btn btn-sm btn-flat m-1 btn-primary">Admin</a></li>
                    <?php endif; ?>

                    <li><a href="<?php echo e(url('admin/home')); ?>" class="btn btn-sm btn-flat m-1 btn-primary">Home</a></li>
                    <li><a href="<?php echo e(url('admin/hb837')); ?>" class="btn btn-sm btn-flat m-1 btn-success">HB837</a></li>
                    <li><a href="<?php echo e(url('admin/mapplots')); ?>" class="btn btn-sm btn-flat m-1 btn-warning">Plot Map</a></li>
                </ul>
            <?php endif; ?>

            <ul class="navbar-nav ms-auto">
                <?php if(auth()->guard()->guest()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.login')); ?>"><?php echo e(__('Login')); ?></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <?php echo e(Auth::user()->name); ?>

                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="<?php echo e(url('admin.profile.change_password')); ?>">Change
                                Password</a>
                            <a class="dropdown-item" href="<?php echo e(route('admin.logout')); ?>"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <?php echo e(__('Logout')); ?>

                            </a>
                            <form id="logout-form" action="<?php echo e(route('admin.logout')); ?>" method="POST"
                                class="d-none">
                                <?php echo csrf_field(); ?>
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\laragon\www\projecttracker\resources\views/partials/navbar.blade.php ENDPATH**/ ?>
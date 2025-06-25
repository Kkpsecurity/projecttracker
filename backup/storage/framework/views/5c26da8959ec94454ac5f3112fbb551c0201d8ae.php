<?php $__env->startSection('content'); ?>
    <style>
        .card {
            background-color: #f8f9fa; /* Light gray/white background */
            color: #343a40; /* Dark text */
            font-size: 1rem;
        }

        label {
            font-weight: bold;
            color: #343a40 !important; /* Dark labels */
        }

        .file-item {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 50px;
            cursor: pointer;
        }

        .file-hover {
            display: none;
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px;
            border-radius: 5px;
            white-space: nowrap;
        }

        .file-item:hover .file-hover {
            display: block;
        }

        .delete-btn {
            background: red;
            color: white;
            border: none;
            padding: 3px 6px;
            cursor: pointer;
            font-size: 12px;
            border-radius: 3px;
        }
    </style>

    <div class="container bg-light p-3">
        <!-- Header -->
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Create Owner</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="<?php echo e(route('admin.owners.index')); ?>">Back</a>
                </div>
            </div>
        </div>

        <?php echo $__env->make('partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- Tab System -->
        <div class="card bg-light m-2 p-3 shadow">
            <ul class="nav nav-tabs" id="ownerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="owner-tab" data-toggle="tab" href="#owner" role="tab" aria-controls="owner" aria-selected="true">
                        Owner
                    </a>
                </li>
                <!-- Additional tabs for owner-related data can be added here if needed -->
            </ul>
            <div class="tab-content" id="ownerTabsContent">
                <!-- Owner Tab: Contains the owner create form -->
                <div class="tab-pane fade show active" id="owner" role="tabpanel" aria-labelledby="owner-tab">
                    <div class="p-3">
                        <form action="<?php echo e(route('admin.owners.store')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo $__env->make('admin.owners.forms.owner_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/owners/create.blade.php ENDPATH**/ ?>
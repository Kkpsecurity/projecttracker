<?php $__env->startSection('styles'); ?>
    <style>
        /* General Tab Styles */
        .nav-tabs {
            border-bottom: 2px solid #ddd;
        }
        .nav-tabs .nav-link {
            color: #555;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 4px 4px 0 0;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-tabs .nav-link:hover {
            background: #e9ecef;
            color: #000;
        }
        .nav-tabs .nav-link.active {
            background: #333;
            color: #fff;
            border-color: #333;
            cursor: default;
        }
        /* Tab Content Styles */
        .tab-content {
            border: 1px solid #ddd;
            border-radius: 0 0 4px 4px;
            padding: 20px;
            background: #fff;
            margin-top: -1px;
        }
        .tab-pane h3 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 60px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container p-3">
        <div class="row justify-content-between align-items-center">
            <div class="col-8">
                <h4 class="text-white">Editing:
                    <strong  class="text-white bold">
                        <?php echo e($hb837->property_name); ?> - <?php echo e($hb837->address); ?>

                    </strong>
                </h4>
            </div>
            <div class="col-4 text-end">
                <a href="<?php echo e(route('admin.hb837.index')); ?>" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <a href="<?php echo e(route('admin.hb837.report', $hb837->id)); ?>" class="btn btn-success" target="_blank">
                    <i class="fa fa-file-alt"></i> Property Report
                </a>
            </div>
        </div>

        <div class="bg-light w-100 p-3 mt-3 rounded shadow-sm">
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="editTabs" role="tablist">
                <?php $__currentLoopData = ['general', 'address', 'contacts', 'financials', 'files', 'notes', 'google_maps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php echo e($tab == $tabName ? 'active' : ''); ?>"
                           id="<?php echo e($tabName); ?>-tab"
                           href="<?php echo e(url('admin/hb837/' . $hb837->id . '/edit/' . $tabName)); ?>"
                           role="tab"
                           aria-controls="<?php echo e($tabName); ?>"
                           aria-selected="<?php echo e($tab == $tabName ? 'true' : 'false'); ?>">
                            <?php echo e(ucfirst($tabName)); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="editTabsContent">
                <?php echo $__env->make('admin.hb837.partials.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Hidden Form for Detaching Consultant -->
            <form id="detach-consultant-form" class="mt-3 form" action="<?php echo e(route('admin.consultants.detach', $hb837->id)); ?>"
                  method="POST" style="display: <?php echo e($hb837->assigned_consultant_id ? 'block' : 'none'); ?>;">
                <?php echo csrf_field(); ?>
            </form>
        </div>
    </div>

    <div class="danger-zone mt-3 p-3 bg-light rounded shadow-sm">
        <h4 class="text-danger">Danger Zone</h4>
        <p class="text-danger">This section contains actions that are irreversible. Please proceed with caution.</p>
        <form action="<?php echo e(route('admin.hb837.destroy', $hb837->id)); ?>"
            method="POST"
            style="display:inline;">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure you want to delete this item?');">
                <i class="fa fa-trash"></i> Delete Record
            </button>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>

        document.addEventListener("DOMContentLoaded", function() {
            const consultantSelect = document.querySelector("#consultant_id");
            const consultantDetails = document.querySelector("#consultant-details");
            if (consultantSelect && consultantDetails) {
                consultantSelect.addEventListener("change", function() {
                    const consultantId = this.value;
                    if (consultantId === "-1") {
                        consultantDetails.innerHTML = `<p class="text-muted">No consultant selected.</p>`;
                        return;
                    }
                    fetch(`/admin/consultants/get/${consultantId}`)
                        .then(response => response.json())
                        .then(data => {
                            consultantDetails.innerHTML = `
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Consultant Details</h4>
                                        <hr>
                                        <ul class="list-group">
                                            <li class="list-group-item"><strong>Name:</strong> ${data.first_name} ${data.last_name}</li>
                                            <li class="list-group-item"><strong>Email:</strong> ${data.email}</li>
                                            <li class="list-group-item"><strong>Phone:</strong> ${data.phone}</li>
                                        </ul>
                                    </div>
                                </div>
                            `;
                        })
                        .catch(error => {
                            consultantDetails.innerHTML = `<p class="text-danger">Error retrieving consultant details. ${error.message}</p>`;
                        });
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.body.addEventListener("click", function(event) {
                if (event.target && event.target.id === "detach-consultant") {
                    const detachForm = document.querySelector("#detach-consultant-form");
                    if (detachForm) {
                        if (confirm("Are you sure you want to detach the consultant?")) {
                            detachForm.submit();
                        }
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/edit-hb837.blade.php ENDPATH**/ ?>
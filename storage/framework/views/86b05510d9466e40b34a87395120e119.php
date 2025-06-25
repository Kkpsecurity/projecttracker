<div class="row justify-content-between align-items-start mb-3">
            <div class="col-md-6">
                <h4 class="text-white">
                    Google Maps:
                    <?php if($selectedPlotId && isset($selectedPlot)): ?>
                        <strong><?php echo e($selectedPlot->plot_name); ?></strong>
                    <?php elseif($selectedMacroClient): ?>
                        <strong><?php echo e(Str::ucfirst($selectedMacroClient)); ?></strong>
                    <?php else: ?>
                        <strong>Plots</strong>
                    <?php endif; ?>
                </h4>
            </div>

            <div class="col-md-6 text-md-end">
                <div class="btn-group" role="group" aria-label="Plot tools">
                    <a href="<?php echo e(route('admin.mapplots.index')); ?>" class="btn btn-primary m-1">Back</a>
                    <button type="button" class="btn btn-success m-1" data-bs-toggle="modal"
                        data-bs-target="#createPlotModal">Create a New Plot</button>

                    <?php if($selectedPlotId && isset($selectedPlot)): ?>
                        <form action="<?php echo e(route('admin.mapplots.destroy', $selectedPlot->id)); ?>" method="POST"
                            class="d-inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger m-1"
                                onclick="return confirm('Are you sure you want to delete this plot?')">
                                <i class="fa fa-trash"></i> Delete Plot
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if($selectedPlotId): ?>
                    <div class="input-group address-forms">
                        <input type="text" id="address-input" class="form-control" placeholder="Enter an address">
                        <button id="add-address-btn" class="btn btn-secondary">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Plot Selection -->
                <select id="plot-select" class="form-select mt-2">
                    <option value="">Select a Plot</option>
                    <?php $__currentLoopData = $plots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($plot->id); ?>" <?php echo e($selectedPlotId == $plot->id ? 'selected' : ''); ?>>
                            <?php echo e($plot->plot_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <!-- Macro Client Selection -->
                <select id="macro-client-select" class="form-select mt-2">
                    <option value="">Select Macro Address Group</option>
                    <?php $__currentLoopData = $macroClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($client->macro_client); ?>"
                            <?php echo e(request()->get('macro_client') == $client->macro_client ? 'selected' : ''); ?>>
                            <?php echo e(Str::ucfirst($client->macro_client)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
<?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/hb837/partials/header.blade.php ENDPATH**/ ?>
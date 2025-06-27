<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="text-white mb-0">
            <i class="fa fa-map-marked-alt me-2"></i>
            Property Map Visualization
        </h3>
        <p class="text-white-50 mb-0">
            <?php if($selectedPlotId && isset($selectedPlot)): ?>
                <strong><?php echo e($selectedPlot->plot_name); ?></strong> 
                <span class="badge bg-<?php echo e($selectedPlot->plot_type === 'custom' ? 'success' : ($selectedPlot->plot_type === 'prospect' ? 'info' : 'warning')); ?>">
                    <?php echo e(ucfirst($selectedPlot->plot_type)); ?>

                </span>
            <?php elseif($selectedMacroClient): ?>
                <strong><?php echo e(Str::title($selectedMacroClient)); ?></strong>
                <span class="badge bg-info">Existing Client</span>
            <?php else: ?>
                Select a plot or existing client to view properties
            <?php endif; ?>
        </p>
    </div>

    <div class="col-md-6">
        <div class="d-flex flex-column gap-2">
            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="<?php echo e(route('admin.mapplots.index')); ?>" class="btn btn-outline-light btn-sm">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createPlotModal">
                    <i class="fa fa-plus"></i> New Plot
                </button>
                
                <?php if($selectedPlotId && isset($selectedPlot)): ?>
                    <button type="button" class="btn btn-info btn-sm" id="edit-plot-btn" data-plot-id="<?php echo e($selectedPlot->id); ?>">
                        <i class="fa fa-edit"></i> Edit Plot
                    </button>
                    
                    <button type="button" class="btn btn-outline-success btn-sm" id="export-data" title="Export Plot Data">
                        <i class="fa fa-download"></i> Export
                    </button>
                    
                    <?php if($selectedPlot->plot_type === 'custom'): ?>
                        <button type="button" class="btn btn-warning btn-sm" id="convert-to-client-btn" data-plot-id="<?php echo e($selectedPlot->id); ?>">
                            <i class="fa fa-arrow-up"></i> Convert to Client
                        </button>
                    <?php endif; ?>
                    
                    <form action="<?php echo e(route('admin.mapplots.destroy', $selectedPlot->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                onclick="return confirm('Are you sure you want to delete this plot and all its addresses?')">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Selection Controls -->
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label text-white-50 small">Custom Plots & Prospects</label>
                    <select id="plot-select" class="form-select form-select-sm">
                        <option value="">Choose a plot...</option>
                        <?php $__currentLoopData = $plots->groupBy('plot_type'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $plotGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <optgroup label="<?php echo e(ucfirst($type)); ?> Plots">
                                <?php $__currentLoopData = $plotGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($plot->id); ?>" 
                                            <?php echo e($selectedPlotId == $plot->id ? 'selected' : ''); ?>

                                            data-type="<?php echo e($plot->plot_type); ?>">
                                        <?php echo e($plot->plot_name); ?>

                                        <?php if($plot->client_contact_name): ?>
                                            (<?php echo e($plot->client_contact_name); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </optgroup>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50 small">Existing Property Management Clients</label>
                    <select id="macro-client-select" class="form-select form-select-sm">
                        <option value="">Choose a client...</option>
                        <?php $__currentLoopData = $macroClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client); ?>" 
                                    <?php echo e(request()->get('macro_client') == $client ? 'selected' : ''); ?>>
                                <?php echo e(Str::title($client)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- Address Input (only show when plot is selected) -->
            <?php if($selectedPlotId): ?>
                <div class="mt-2">
                    <div class="input-group input-group-sm">
                        <input type="text" id="address-input" class="form-control" 
                               placeholder="Enter address to add to this plot...">
                        <button id="add-address-btn" class="btn btn-outline-light" type="button">
                            <i class="fa fa-plus"></i> Add Address
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\projecttracker\resources\views/admin/hb837/partials/header.blade.php ENDPATH**/ ?>
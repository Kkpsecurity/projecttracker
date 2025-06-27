
<?php if($paginator->hasPages()): ?>
    <nav aria-label="Pagination Navigation" role="navigation">
        <div class="d-flex justify-content-between align-items-center">
            
            <div>
                <p class="text-sm text-muted mb-0">
                    <?php echo __('Showing'); ?>

                    <span class="font-weight-bold"><?php echo e($paginator->firstItem()); ?></span>
                    <?php echo __('to'); ?>

                    <span class="font-weight-bold"><?php echo e($paginator->lastItem()); ?></span>
                    <?php echo __('of'); ?>

                    <span class="font-weight-bold"><?php echo e($paginator->total()); ?></span>
                    <?php echo __('results'); ?>

                </p>
            </div>

            
            <div>
                <ul class="pagination pagination-sm mb-0">
                    
                    <?php if($paginator->onFirstPage()): ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-left" style="font-size: 0.75rem;"></i>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">
                                <i class="fas fa-angle-left" style="font-size: 0.75rem;"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(is_string($element)): ?>
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link"><?php echo e($element); ?></span>
                            </li>
                        <?php endif; ?>

                        
                        <?php if(is_array($element)): ?>
                            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $paginator->currentPage()): ?>
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link"><?php echo e($page); ?></span>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if($paginator->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">
                                <i class="fas fa-angle-right" style="font-size: 0.75rem;"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-right" style="font-size: 0.75rem;"></i>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH C:\laragon\www\projecttracker\resources\views/custom/pagination.blade.php ENDPATH**/ ?>
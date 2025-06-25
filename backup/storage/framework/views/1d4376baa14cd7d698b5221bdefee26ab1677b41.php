<div class="modal fade" id="infoModal<?php echo e($backup->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fas fa-database me-2"></i>Backup Details – #<?php echo e($backup->id); ?>

                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body bg-white px-4 py-4">
                <div class="row g-4">
                    <!-- Left: Metadata -->
                    <div class="col-md-5">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted fw-semibold mb-3">Backup Info</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>Name:</strong> <?php echo e($backup->name ?: 'N/A'); ?></li>
                                <li class="mb-2"><strong>Created:</strong> <?php echo e($backup->created_at->format('M j, Y g:i A')); ?></li>
                                <li class="mb-2"><strong>File Size:</strong> <?php echo e(number_format($backup->size / 1024, 2)); ?> KB</li>
                                <li class="mb-2"><strong>Filename:</strong> <code><?php echo e($backup->filename); ?></code></li>
                                <li class="mb-2"><strong>Status:</strong>
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-check-circle me-1"></i>Completed
                                    </span>
                                </li>
                                <li class="mb-2"><strong>Duration:</strong> <?php echo e($backup->duration ?? 'N/A'); ?></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right: Contents -->
                    <div class="col-md-7">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted fw-semibold mb-3">Contents Summary</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="bg-white p-3 rounded shadow-sm h-100">
                                        <div class="text-muted small">Project Records</div>
                                        <div class="fw-bold fs-5"><?php echo e($backup->record_count); ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white p-3 rounded shadow-sm h-100">
                                        <div class="text-muted small">Files Included</div>
                                        <div class="fw-bold fs-5"><?php echo e($backup->file_count ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                                <?php if(!empty($backup->notes)): ?>
                                    <div class="col-12">
                                        <div class="alert alert-secondary">
                                            <strong>Notes:</strong><br><?php echo e($backup->notes); ?>

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>

            <div class="modal-footer bg-light justify-content-between">
                <div>
                    <small class="text-muted">Backup ID: <?php echo e($backup->id); ?> • UUID: <?php echo e($backup->import_id ?? 'N/A'); ?></small>
                </div>
                <div>
                    <a href="<?php echo e(url('admin.hb837.backup.download', $backup->filename)); ?>" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/projecttracker/resources/views/partials/modals/backup_info_modal.blade.php ENDPATH**/ ?>
<!-- Enhanced Backup Modal -->
<div class="modal fade" id="backupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-save me-2"></i>Create New Backup</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-light border-primary border-start border-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Choose the data to include in this backup. Exported as a single Excel file, optionally with
                    attachments.
                </div>

               <form id="backupForm" method="POST" action="<?php echo e(route('admin.hb837.backup.save')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label for="backupName" class="form-label fw-medium">Backup Name (optional)</label>
                        <input type="text" class="form-control form-control-lg" id="backupName" name="name"
                            placeholder="e.g. Pre-Update Backup">
                        <div class="form-text">A descriptive name helps identify backups later</div>
                    </div>

                    <label class="form-label fw-medium mb-3">Data to include:</label>
                    <div class="list-group mb-4">
                        <label class="list-group-item list-group-item-action border mb-2 rounded">
                            <div class="d-flex gap-3 align-items-center">
                                <input class="form-check-input flex-shrink-0 mt-0" type="checkbox" name="tables[]"
                                    value="hb837" checked>
                                <div>
                                    <strong>HB837 Projects</strong>
                                    <div class="text-muted small">Core project data including timelines and metadata
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="list-group-item list-group-item-action border mb-2 rounded">
                            <div class="d-flex gap-3 align-items-center">
                                <input class="form-check-input flex-shrink-0 mt-0" type="checkbox" name="tables[]"
                                    value="consultants" checked>
                                <div>
                                    <strong>Consultants</strong>
                                    <div class="text-muted small">Personnel assignments and contact information</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="form-check form-switch ml-3 mb-4 p-3">
                        <input class="form-check-input" type="checkbox" id="includeFiles" name="include_files" checked>
                        <label class="form-check-label fw-medium" for="includeFiles">
                            <i class="fas fa-file-archive me-1 text-warning"></i>
                            Include physical files (ZIP bundled)
                        </label>
                    </div>

                    <!-- Progress Bar -->
                    <div class="d-none py-3" id="backupProgress">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <span class="fw-medium">Creating backup...</span>
                            </div>
                            <div class="text-primary fw-bold">25%</div>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: 25%"></div>
                        </div>
                        <div class="text-end mt-2 small text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Estimated time remaining: <span id="timeRemaining">2 minutes</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-lg btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="backupForm" class="btn btn-lg btn-primary px-4">
                    <i class="fas fa-database me-2"></i>Create Backup
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/projecttracker/resources/views/partials/modals/backup_modal.blade.php ENDPATH**/ ?>